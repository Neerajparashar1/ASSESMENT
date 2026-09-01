<?php
// local_uidesign - the rule engine: CRUD + compile stored rules into live CSS.

namespace local_uidesign\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Stateless helpers for the {local_uidesign_rule} table.
 *
 * A "rule" is one presentation override:
 *   - kind=token   : selector is a CSS custom property (--itm-*), value is its new value.
 *   - kind=element : selector + property + value  -> a scoped CSS declaration (!important).
 *   - kind=hide    : selector  -> display:none.
 *   - kind=text    : selector + value(text)  -> applied by editor JS shim (best-effort).
 * pagetype is $PAGE->pagetype ("my-index", "mod-quiz-attempt", ...) or "*" for everywhere.
 */
class rules {

    /** CSS properties an "element" rule may set. */
    const ALLOWED_PROPS = [
        'color', 'background-color', 'background-image', 'background-size', 'background-position',
        'background-repeat', 'font-size', 'font-family', 'font-weight', 'font-style',
        'text-align', 'text-transform', 'letter-spacing', 'line-height', 'text-decoration',
        'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
        'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
        'border-radius', 'border', 'border-color', 'border-width', 'border-style', 'opacity',
    ];

    /** kinds: the usual four, plus "lang" = a Moodle string override (component/stringid). */
    const KINDS = ['token', 'element', 'hide', 'text', 'lang'];

    /** Components searched by the "rename a fixed label" tool. */
    const LANG_SEARCH_COMPONENTS = [
        'moodle', 'core_grades', 'core_course', 'core_admin', 'core_user',
        'core_calendar', 'core_completion', 'core_message', 'core_block', 'core_role',
        'mod_quiz', 'mod_assign', 'gradereport_overview', 'gradereport_user',
        'theme_boost', 'theme_boost_union', 'block_myoverview', 'block_timeline',
        'tool_uploaduser', 'enrol_cohort', 'enrol_manual',
    ];

    // ---------------------------------------------------------------
    //  Read / compile
    // ---------------------------------------------------------------

    /**
     * Build the CSS that belongs in <style id="uid-live"> for a given page.
     *
     * @param string $pagetype $PAGE->pagetype
     * @return string CSS (no <style> wrapper)
     */
    public static function compile_css(string $pagetype, bool $includedrafts = false): string {
        global $DB;

        $pagetype = self::clean_pagetype($pagetype);
        [$insql, $params] = $DB->get_in_or_equal(['*', $pagetype], SQL_PARAMS_NAMED, 'pt');
        $draftsql = $includedrafts ? '' : ' AND published = 1';
        $rows = $DB->get_records_select('local_uidesign_rule',
            "enabled = 1 AND pagetype $insql" . $draftsql, $params, 'sortorder ASC, id ASC');

        $tokens = [];
        $blocks = [];
        foreach ($rows as $r) {
            if ($r->kind === 'token') {
                $name = self::clean_token_name($r->selector);
                if ($name !== '') {
                    $tokens[$name] = self::clean_value((string) $r->value);
                }
            } else if ($r->kind === 'element') {
                $prop = self::clean_prop($r->property);
                $sel  = self::scope_selector($r->selector, $r->pagetype);
                $val  = self::clean_value_for($prop, (string) $r->value);
                if ($prop !== '' && $sel !== '' && $val !== '') {
                    $blocks[] = $sel . '{' . $prop . ':' . $val . ' !important}';
                }
            } else if ($r->kind === 'hide') {
                $sel = self::scope_selector($r->selector, $r->pagetype);
                if ($sel !== '') {
                    $blocks[] = $sel . '{display:none !important}';
                }
            }
        }

        $out = '';
        if ($tokens) {
            $decl = '';
            foreach ($tokens as $k => $v) {
                if ($v !== '') {
                    $decl .= $k . ':' . $v . ';';
                }
            }
            if ($decl !== '') {
                $out .= ':root{' . $decl . '}';
            }
        }
        $out .= implode('', $blocks);
        return $out;
    }

    /**
     * Text-swap rules for the JS shim: [ selector => replacement text ].
     *
     * @param string $pagetype
     * @return array
     */
    public static function text_map(string $pagetype, bool $includedrafts = false): array {
        global $DB;
        $pagetype = self::clean_pagetype($pagetype);
        [$insql, $params] = $DB->get_in_or_equal(['*', $pagetype], SQL_PARAMS_NAMED, 'pt');
        $draftsql = $includedrafts ? '' : ' AND published = 1';
        $rows = $DB->get_records_select('local_uidesign_rule',
            "enabled = 1 AND kind = 'text' AND pagetype $insql" . $draftsql, $params, 'sortorder ASC, id ASC');
        $map = [];
        foreach ($rows as $r) {
            $sel = self::clean_selector($r->selector);
            if ($sel !== '') {
                $map[$sel] = (string) $r->value;
            }
        }
        return $map;
    }

    /** Every rule, for the manager UI. */
    public static function all(): array {
        global $DB;
        return $DB->get_records('local_uidesign_rule', null, 'kind ASC, pagetype ASC, sortorder ASC, id ASC');
    }

    /** Cache-busting revision for the compiled CSS / editor assets. */
    public static function rev(): int {
        return (int) get_config('local_uidesign', 'rulesrev') ?: 1;
    }

    // ---------------------------------------------------------------
    //  Write
    // ---------------------------------------------------------------

    /**
     * Create or update a rule (unique on kind+pagetype+selector+property).
     *
     * @param array $data kind, pagetype, selector, property, value, label, enabled
     * @return int rule id
     */
    public static function upsert(array $data): int {
        global $DB, $USER;

        $now = time();
        $rec = (object) [
            'kind'         => self::clean_kind($data['kind'] ?? ''),
            'pagetype'     => self::clean_pagetype($data['pagetype'] ?? '*'),
            'selector'     => \core_text::substr(trim((string) ($data['selector'] ?? '')), 0, 255),
            'property'     => self::clean_prop($data['property'] ?? ''),
            'value'        => \core_text::substr((string) ($data['value'] ?? ''), 0, 2000),
            'label'        => \core_text::substr(trim((string) ($data['label'] ?? '')), 0, 255),
            // Default enabled = 1 unless explicitly falsey.
            'enabled'      => array_key_exists('enabled', $data) ? (empty($data['enabled']) ? 0 : 1) : 1,
            // New edits are drafts (published = 0) until the admin hits Publish.
            'published'    => array_key_exists('published', $data) ? (empty($data['published']) ? 0 : 1) : 0,
            'sortorder'    => (int) ($data['sortorder'] ?? ($now % 100000)),
            'usermodified' => $USER->id,
            'timemodified' => $now,
        ];

        if ($rec->kind === '' || $rec->selector === '') {
            throw new \moodle_exception('err_invalidrule', 'local_uidesign');
        }
        if ($rec->kind === 'element' && self::clean_prop($rec->property) === '') {
            throw new \moodle_exception('err_invalidprop', 'local_uidesign');
        }

        // Sanitise on the way in too (compile_css sanitises again on the way out).
        if ($rec->kind === 'token') {
            $rec->value = self::clean_value((string) $rec->value);
        } else if ($rec->kind === 'element') {
            $rec->value = self::clean_value_for($rec->property, (string) $rec->value);
        } else if ($rec->kind === 'text') {
            $rec->value = \core_text::substr(str_replace(['<', '>'], '', (string) $rec->value), 0, 2000);
        } else if ($rec->kind === 'lang') {
            $rec->value = \core_text::substr(str_replace(['<', '>'], '', (string) $rec->value), 0, 255);
        }
        if ($rec->kind !== 'hide' && trim((string) $rec->value) === '') {
            throw new \moodle_exception('err_invalidrule', 'local_uidesign');
        }
        if ($rec->kind === 'token') {
            $rec->selector = self::clean_token_name($rec->selector);
            if ($rec->selector === '') {
                throw new \moodle_exception('err_invalidrule', 'local_uidesign');
            }
        } else if ($rec->kind === 'lang') {
            // A lang override is "component/stringid"; it is global + applied at once,
            // never a draft, and never scoped to a pagetype.
            $rec->selector = self::clean_lang_key($rec->selector);
            $rec->pagetype = 'lang';
            $rec->property = '';
            $rec->published = 1;
            if ($rec->selector === '') {
                throw new \moodle_exception('err_invalidrule', 'local_uidesign');
            }
        } else {
            $rec->selector = self::clean_selector($rec->selector);
        }
        if ($rec->label === '') {
            $rec->label = self::autolabel($rec);
        }

        $existing = $DB->get_record('local_uidesign_rule', [
            'kind' => $rec->kind, 'pagetype' => $rec->pagetype,
            'selector' => $rec->selector, 'property' => $rec->property,
        ]);
        if ($existing) {
            $rec->id = $existing->id;
            $DB->update_record('local_uidesign_rule', $rec);
        } else {
            $rec->timecreated = $now;
            $rec->id = $DB->insert_record('local_uidesign_rule', $rec);
        }
        self::bump_rev();

        // A lang override takes effect the moment it is saved (writes a customlang file).
        if ($rec->kind === 'lang') {
            [$comp, $sid] = array_pad(explode('/', $rec->selector, 2), 2, '');
            if ($comp !== '' && $sid !== '') {
                self::override_string($comp, $sid, (string) $rec->value);
            }
        }
        return (int) $rec->id;
    }

    public static function set_enabled(int $id, bool $on): void {
        global $DB, $USER;
        $rule = $DB->get_record('local_uidesign_rule', ['id' => $id]);
        $DB->set_field('local_uidesign_rule', 'enabled', $on ? 1 : 0, ['id' => $id]);
        $DB->set_field('local_uidesign_rule', 'timemodified', time(), ['id' => $id]);
        $DB->set_field('local_uidesign_rule', 'usermodified', $USER->id, ['id' => $id]);
        if ($rule && $rule->kind === 'lang') {
            [$c, $sid] = array_pad(explode('/', $rule->selector, 2), 2, '');
            if ($c !== '' && $sid !== '') {
                $on ? self::override_string($c, $sid, (string) $rule->value)
                    : self::revert_string($c, $sid);
            }
        }
        self::bump_rev();
    }

    public static function delete(int $id): void {
        global $DB;
        $rule = $DB->get_record('local_uidesign_rule', ['id' => $id]);
        if ($rule && $rule->kind === 'lang') {
            [$c, $sid] = array_pad(explode('/', $rule->selector, 2), 2, '');
            if ($c !== '' && $sid !== '') {
                self::revert_string($c, $sid);
            }
        }
        $DB->delete_records('local_uidesign_rule', ['id' => $id]);
        self::bump_rev();
    }

    public static function reset_all(): void {
        global $DB;
        // Undo any string overrides before dropping the rows that track them.
        foreach ($DB->get_records('local_uidesign_rule', ['kind' => 'lang']) as $r) {
            [$c, $sid] = array_pad(explode('/', $r->selector, 2), 2, '');
            if ($c !== '' && $sid !== '') {
                self::revert_string($c, $sid);
            }
        }
        $DB->delete_records('local_uidesign_rule');
        self::bump_rev();
    }

    // ---------------------------------------------------------------
    //  Draft / publish + version history
    // ---------------------------------------------------------------

    /** How many unpublished (draft) changes are waiting. */
    public static function pending_count(): int {
        global $DB;
        return $DB->count_records('local_uidesign_rule', ['published' => 0]);
    }

    /** Make every draft rule live for all users, and snapshot the result. */
    public static function publish(string $note = ''): void {
        global $DB, $USER;
        if (!$DB->record_exists('local_uidesign_rule', ['published' => 0])) {
            return;
        }
        $DB->set_field('local_uidesign_rule', 'published', 1, ['published' => 0]);
        $DB->set_field('local_uidesign_rule', 'usermodified', $USER->id, []);
        self::save_version($note !== '' ? $note : 'Published ' . userdate(time(), '%d %b %Y %H:%M'));
        self::bump_rev();
    }

    /**
     * Throw away all unpublished changes and return to the last published state
     * (the newest version snapshot). Falls back to a full reset if never published.
     */
    public static function discard_drafts(): void {
        global $DB;
        $last = $DB->get_records('local_uidesign_version', null, 'timecreated DESC', 'id, snapshot', 0, 1);
        self::reset_all();
        if ($last) {
            $v = reset($last);
            self::import_json((string) $v->snapshot);
            $DB->set_field('local_uidesign_rule', 'published', 1, []);
        }
        self::bump_rev();
    }

    /** Store a full snapshot of the current rule set. */
    public static function save_version(string $note): int {
        global $DB, $USER;
        $rec = (object) [
            'note'         => \core_text::substr(trim($note), 0, 255),
            'rulecount'    => $DB->count_records('local_uidesign_rule'),
            'snapshot'     => self::export_json(),
            'usermodified' => $USER->id,
            'timecreated'  => time(),
        ];
        $id = (int) $DB->insert_record('local_uidesign_version', $rec);
        // Keep only the newest 25 versions.
        $old = $DB->get_records('local_uidesign_version', null, 'timecreated DESC', 'id', 25, 1000);
        if ($old) {
            $DB->delete_records_list('local_uidesign_version', 'id', array_keys($old));
        }
        return $id;
    }

    /** Version list for the UI (no snapshot payload). */
    public static function versions(): array {
        global $DB;
        return $DB->get_records('local_uidesign_version', null, 'timecreated DESC',
            'id, note, rulecount, usermodified, timecreated', 0, 25);
    }

    /** Replace the whole rule set with a stored version and publish it. */
    public static function rollback(int $versionid): int {
        global $DB;
        $v = $DB->get_record('local_uidesign_version', ['id' => $versionid], '*', MUST_EXIST);
        self::reset_all();
        $n = self::import_json((string) $v->snapshot);
        $DB->set_field('local_uidesign_rule', 'published', 1, []);
        self::save_version('Rolled back to ' . userdate($v->timecreated, '%d %b %Y %H:%M'));
        self::bump_rev();
        return $n;
    }

    public static function export_json(): string {
        global $DB;
        $out = [];
        foreach ($DB->get_records('local_uidesign_rule', null, 'kind, pagetype, sortorder, id') as $r) {
            $out[] = [
                'kind' => $r->kind, 'pagetype' => $r->pagetype, 'selector' => $r->selector,
                'property' => $r->property, 'value' => $r->value, 'label' => $r->label,
                'enabled' => (int) $r->enabled, 'published' => (int) $r->published,
                'sortorder' => (int) $r->sortorder,
            ];
        }
        return json_encode(['plugin' => 'local_uidesign', 'version' => 2, 'rules' => $out],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param string $json
     * @return int number of rules imported
     */
    public static function import_json(string $json): int {
        $data = json_decode($json, true);
        if (!is_array($data) || empty($data['rules']) || !is_array($data['rules'])) {
            throw new \moodle_exception('err_badimport', 'local_uidesign');
        }
        $n = 0;
        foreach ($data['rules'] as $r) {
            if (!is_array($r)) {
                continue;
            }
            self::upsert($r);
            $n++;
        }
        return $n;
    }

    private static function bump_rev(): void {
        set_config('rulesrev', time(), 'local_uidesign');
    }

    // ---------------------------------------------------------------
    //  "Rename a fixed label" - Moodle string overrides (tool_customlang route)
    // ---------------------------------------------------------------

    /**
     * Find core / plugin strings whose English value is exactly $text, so the
     * admin can rename an on-screen label they clicked.
     *
     * @param string $text the visible label
     * @return array of ['component' => , 'stringid' => , 'current' => ]
     */
    public static function find_string_matches(string $text): array {
        $text = trim($text);
        if ($text === '' || \core_text::strlen($text) > 140) {
            return [];
        }
        $sm = get_string_manager();
        $out = [];
        foreach (self::LANG_SEARCH_COMPONENTS as $comp) {
            try {
                $strings = $sm->load_component_strings($comp, 'en');
            } catch (\Throwable $e) {
                continue;
            }
            foreach ($strings as $id => $val) {
                if (!is_string($val) || strpos($val, '{$a') !== false) {
                    continue; // skip parametrised strings - risky to override blind.
                }
                if (trim($val) === $text) {
                    $out[] = ['component' => $comp, 'stringid' => (string) $id, 'current' => $val];
                    if (count($out) >= 15) {
                        return $out;
                    }
                }
            }
        }
        return $out;
    }

    /** Add / change one string override in {langlocalroot}/en_local/<file>.php. */
    public static function override_string(string $component, string $stringid, string $newtext): void {
        self::write_lang_local($component, [$stringid => $newtext], []);
    }

    /** Remove one string override again. */
    public static function revert_string(string $component, string $stringid): void {
        self::write_lang_local($component, [], [$stringid]);
    }

    /** The lang/en file name Moodle uses for a component (mirrors the string manager). */
    public static function lang_file_for_component(string $component): string {
        [$type, $name] = \core_component::normalize_component($component);
        if ($type === 'core') {
            return ($name === null || $name === '') ? 'moodle' : $name;
        }
        if ($type === 'mod') {
            return $name; // activity modules use the short name, e.g. quiz.php
        }
        return $type . '_' . $name; // everything else uses the frankenstyle name.
    }

    private static function langlocal_dir(): string {
        global $CFG;
        $root = !empty($CFG->langlocalroot) ? $CFG->langlocalroot : ($CFG->dataroot . '/lang');
        return $root . '/en_local';
    }

    /**
     * Rewrite the en_local file for one component: apply $set (id => text), drop
     * $unset (ids), keep every other override already in the file untouched.
     */
    private static function write_lang_local(string $component, array $set, array $unset): void {
        $file = self::lang_file_for_component($component);
        if ($file === '' || !preg_match('~^[a-z][a-z0-9_]{1,60}$~', $file)) {
            return;
        }
        $dir = self::langlocal_dir();
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $path = $dir . '/' . $file . '.php';

        $string = [];
        if (is_file($path)) {
            include($path);
        }
        foreach ($set as $k => $v) {
            $string[(string) $k] = (string) $v;
        }
        foreach ($unset as $k) {
            unset($string[(string) $k]);
        }

        if (!$string) {
            @unlink($path);
        } else {
            $body = "<?php\n// Managed by local_uidesign (Design Studio) - local string overrides.\n";
            foreach ($string as $k => $v) {
                $body .= '$string[' . var_export((string) $k, true) . '] = '
                    . var_export((string) $v, true) . ";\n";
            }
            file_put_contents($path, $body);
        }
        get_string_manager()->reset_caches();
    }

    // ---------------------------------------------------------------
    //  "Bake to theme" - fold token rules permanently into the SCSS
    // ---------------------------------------------------------------

    /** Absolute path of the project's source custom.scss. */
    public static function scss_path(): string {
        global $CFG;
        return dirname($CFG->dirroot, 3) . '/config/moodle/custom.scss';
    }

    /**
     * Fold every enabled token rule into config/moodle/custom.scss as a managed
     * :root block at the end of the file, push it to Boost Union's Raw SCSS,
     * purge caches, snapshot a version, then delete the now-permanent token rules.
     *
     * @return int number of tokens baked
     */
    public static function bake_scss(): int {
        global $DB;

        $tokenrules = $DB->get_records('local_uidesign_rule', ['kind' => 'token', 'enabled' => 1]);
        if (!$tokenrules) {
            return 0;
        }
        $decls = [];
        foreach ($tokenrules as $r) {
            $name = self::clean_token_name($r->selector);
            $val  = self::clean_value((string) $r->value);
            if ($name !== '' && $val !== '') {
                $decls[$name] = $val;
            }
        }
        if (!$decls) {
            return 0;
        }

        $path = self::scss_path();
        $scss = is_file($path) ? (string) file_get_contents($path) : '';

        // Drop any earlier managed block, then append a fresh one (a later :root
        // block wins for the same custom property).
        $scss = preg_replace(
            '~\n*/\* === local_uidesign baked tokens.*?end local_uidesign baked tokens === \*/\n?~s',
            '', $scss);
        $block = "\n/* === local_uidesign baked tokens (managed - Design Studio) === */\n:root{";
        foreach ($decls as $k => $v) {
            $block .= $k . ':' . $v . ';';
        }
        $block .= "}\n/* === end local_uidesign baked tokens === */\n";
        $scss = rtrim($scss) . "\n" . $block;

        if (is_file($path) ? is_writable($path) : is_writable(dirname($path))) {
            file_put_contents($path, $scss);
        }
        // Push to the live theme (Boost Union "Raw SCSS") and rebuild.
        set_config('scss', $scss, 'theme_boost_union');
        if (function_exists('theme_reset_all_caches')) {
            theme_reset_all_caches();
        }
        purge_all_caches();

        self::save_version('Baked ' . count($decls) . ' token(s) into the theme SCSS');

        foreach ($tokenrules as $r) {
            $DB->delete_records('local_uidesign_rule', ['id' => $r->id]);
        }
        self::bump_rev();
        return count($decls);
    }

    // ---------------------------------------------------------------
    //  Sanitisation - everything below feeds a <style> tag or a selector.
    // ---------------------------------------------------------------

    public static function clean_kind(string $k): string {
        $k = strtolower(trim($k));
        return in_array($k, self::KINDS, true) ? $k : '';
    }

    public static function clean_pagetype(string $p): string {
        $p = trim($p);
        if ($p === '' || $p === '*') {
            return '*';
        }
        $p = preg_replace('~[^a-z0-9\-]~i', '', $p);
        return \core_text::substr((string) $p, 0, 100) ?: '*';
    }

    /** A CSS custom property in the ITM design system: --itm-something. */
    public static function clean_token_name(string $name): string {
        $name = strtolower(trim($name));
        return preg_match('~^--itm-[a-z0-9\-]{1,60}$~', $name) ? $name : '';
    }

    /** A "component/stringid" key for a lang override. */
    public static function clean_lang_key(string $key): string {
        $key = trim($key);
        if (!preg_match('~^([a-z][a-z0-9_]{1,50})/([a-zA-Z0-9_:.\-]{1,100})$~', $key, $m)) {
            return '';
        }
        return $m[1] . '/' . $m[2];
    }

    public static function clean_prop(string $prop): string {
        $prop = strtolower(trim($prop));
        $prop = preg_replace('~[^a-z\-]~', '', $prop);
        return in_array($prop, self::ALLOWED_PROPS, true) ? $prop : '';
    }

    /**
     * Strip anything that could break out of a `prop: value` declaration or the
     * <style> element. No url(), @rules, comments, braces, semicolons, angle
     * brackets. Keeps hex, rgb()/hsl()/calc()/var(), quotes, %, units, commas.
     */
    public static function clean_value(string $v): string {
        $v = trim($v);
        $v = preg_replace('~/\*.*?\*/~s', '', $v);
        $v = preg_replace('~expression\s*\(|url\s*\(|@import|javascript:~i', '', $v);
        $v = str_replace(['<', '>', '{', '}', ';', '\\'], '', $v);
        return \core_text::substr(trim((string) $v), 0, 300);
    }

    /**
     * Property-aware value cleaner. Everything routes through clean_value() except
     * background-image, where a single safe url(...) is permitted.
     */
    public static function clean_value_for(string $prop, string $v): string {
        if ($prop !== 'background-image') {
            return self::clean_value($v);
        }
        $v = trim($v);
        // Accept exactly: url("<safe>") | url('<safe>') | url(<safe>) | none | linear-gradient(...)
        if (preg_match('~^none$~i', $v)) {
            return 'none';
        }
        if (preg_match('~^url\(\s*["\']?([^"\'()<>{}\s\\\\]+)["\']?\s*\)$~i', $v, $m)) {
            $url = $m[1];
            if (preg_match('~^(https?://[^\s"\'<>]+|/[^\s"\'<>]*|data:image/(png|jpe?g|gif|webp|svg\+xml);base64,[A-Za-z0-9+/=]+)$~i', $url)) {
                return "url('" . $url . "')";
            }
        }
        return '';
    }

    /**
     * Allow a normal CSS selector, drop declaration/style-breaking chars.
     */
    public static function clean_selector(string $sel): string {
        $sel = trim($sel);
        $sel = preg_replace('~/\*.*?\*/~s', '', $sel);
        $sel = preg_replace('~<\s*/?\s*style~i', '', $sel);
        $sel = preg_replace('~[{}<>;@\\\\]~', '', $sel);
        // Keep: letters digits . # - _ space + ~ [ ] = : ( ) , " '
        $sel = preg_replace('~[^a-z0-9\.\#\-\_\s\+\~\[\]\=\:\(\)\,"\']~i', '', $sel);
        $sel = preg_replace('~\s+~', ' ', (string) $sel);
        return \core_text::substr(trim($sel), 0, 255);
    }

    /**
     * Prefix each comma-part with `body.page-<pagetype>` unless the rule is global.
     */
    public static function scope_selector(string $selector, string $pagetype): string {
        $selector = self::clean_selector($selector);
        if ($selector === '') {
            return '';
        }
        $pagetype = self::clean_pagetype($pagetype);
        if ($pagetype === '*') {
            return $selector;
        }
        $prefix = 'body#page-' . $pagetype . ' ';
        $parts = array_filter(array_map('trim', explode(',', $selector)));
        $scoped = [];
        foreach ($parts as $p) {
            $scoped[] = $prefix . $p;
        }
        return implode(',', $scoped);
    }

    private static function autolabel(\stdClass $rec): string {
        $where = $rec->pagetype === '*' ? 'everywhere' : $rec->pagetype;
        switch ($rec->kind) {
            case 'token':
                return trim($rec->selector) . ' = ' . trim((string) $rec->value);
            case 'hide':
                return 'Hide ' . trim($rec->selector) . ' (' . $where . ')';
            case 'text':
                return 'Text: ' . \core_text::substr(trim((string) $rec->value), 0, 40) . ' (' . $where . ')';
            case 'lang':
                return 'Label ' . trim($rec->selector) . ' → "'
                    . \core_text::substr(trim((string) $rec->value), 0, 40) . '"';
            default:
                return trim($rec->property) . ' on ' . trim($rec->selector) . ' (' . $where . ')';
        }
    }
}
