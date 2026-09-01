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

    /** CSS properties an "element" rule may set (Phase 1a). */
    const ALLOWED_PROPS = [
        'color', 'background-color', 'font-size', 'font-family', 'font-weight', 'font-style',
        'text-align', 'text-transform', 'letter-spacing', 'line-height', 'text-decoration',
        'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
        'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
        'border-radius', 'border', 'border-color', 'border-width', 'opacity',
    ];

    const KINDS = ['token', 'element', 'hide', 'text'];

    // ---------------------------------------------------------------
    //  Read / compile
    // ---------------------------------------------------------------

    /**
     * Build the CSS that belongs in <style id="uid-live"> for a given page.
     *
     * @param string $pagetype $PAGE->pagetype
     * @return string CSS (no <style> wrapper)
     */
    public static function compile_css(string $pagetype): string {
        global $DB;

        $pagetype = self::clean_pagetype($pagetype);
        [$insql, $params] = $DB->get_in_or_equal(['*', $pagetype], SQL_PARAMS_NAMED, 'pt');
        $rows = $DB->get_records_select('local_uidesign_rule',
            "enabled = 1 AND pagetype $insql", $params, 'sortorder ASC, id ASC');

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
                $val  = self::clean_value((string) $r->value);
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
    public static function text_map(string $pagetype): array {
        global $DB;
        $pagetype = self::clean_pagetype($pagetype);
        [$insql, $params] = $DB->get_in_or_equal(['*', $pagetype], SQL_PARAMS_NAMED, 'pt');
        $rows = $DB->get_records_select('local_uidesign_rule',
            "enabled = 1 AND kind = 'text' AND pagetype $insql", $params, 'sortorder ASC, id ASC');
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
        if ($rec->kind === 'token' || $rec->kind === 'element') {
            $rec->value = self::clean_value((string) $rec->value);
        } else if ($rec->kind === 'text') {
            $rec->value = \core_text::substr(str_replace(['<', '>'], '', (string) $rec->value), 0, 2000);
        }
        if ($rec->kind !== 'hide' && trim((string) $rec->value) === '') {
            throw new \moodle_exception('err_invalidrule', 'local_uidesign');
        }
        if ($rec->kind === 'token') {
            $rec->selector = self::clean_token_name($rec->selector);
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
        return (int) $rec->id;
    }

    public static function set_enabled(int $id, bool $on): void {
        global $DB, $USER;
        $DB->set_field('local_uidesign_rule', 'enabled', $on ? 1 : 0, ['id' => $id]);
        $DB->set_field('local_uidesign_rule', 'timemodified', time(), ['id' => $id]);
        $DB->set_field('local_uidesign_rule', 'usermodified', $USER->id, ['id' => $id]);
        self::bump_rev();
    }

    public static function delete(int $id): void {
        global $DB;
        $DB->delete_records('local_uidesign_rule', ['id' => $id]);
        self::bump_rev();
    }

    public static function reset_all(): void {
        global $DB;
        $DB->delete_records('local_uidesign_rule');
        self::bump_rev();
    }

    public static function export_json(): string {
        global $DB;
        $out = [];
        foreach ($DB->get_records('local_uidesign_rule', null, 'kind, pagetype, sortorder, id') as $r) {
            $out[] = [
                'kind' => $r->kind, 'pagetype' => $r->pagetype, 'selector' => $r->selector,
                'property' => $r->property, 'value' => $r->value, 'label' => $r->label,
                'enabled' => (int) $r->enabled, 'sortorder' => (int) $r->sortorder,
            ];
        }
        return json_encode(['plugin' => 'local_uidesign', 'version' => 1, 'rules' => $out],
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
            default:
                return trim($rec->property) . ' on ' . trim($rec->selector) . ' (' . $where . ')';
        }
    }
}
