<?php
// local_uidesign - hook callbacks.

namespace local_uidesign;

defined('MOODLE_INTERNAL') || die();

use local_uidesign\local\rules;

/**
 * Injects the live override <style> on every page, plus (for admins) the
 * Design Studio editor assets.
 */
class hook_callbacks {

    /**
     * @param \core\hook\output\before_standard_head_html_generation $hook
     */
    public static function before_standard_head_html_generation(
            \core\hook\output\before_standard_head_html_generation $hook): void {
        global $PAGE, $CFG, $USER;

        // Nothing to do on embedded / maintenance / install output.
        if (during_initial_install() || CLI_SCRIPT) {
            return;
        }
        $layout = $PAGE->pagelayout ?? '';
        if ($layout === 'embedded' || $layout === 'maintenance' || $layout === 'print') {
            return;
        }
        if ((int) get_config('local_uidesign', 'enabled') === 0
                && get_config('local_uidesign', 'enabled') !== false) {
            return;
        }

        $pagetype = (string) ($PAGE->pagetype ?? '');
        $assetver = (int) get_config('local_uidesign', 'version');   // cache-bust editor.js/.css
        $html = '';

        // Admins see draft (unpublished) rules too, so they can preview their
        // work before it goes live for everyone.
        $isadmin = isloggedin() && !isguestuser()
            && (is_siteadmin() || has_capability('local/uidesign:manage', \context_system::instance()));

        // 1. The live overrides.
        $css = rules::compile_css($pagetype, $isadmin);
        if ($css !== '') {
            $html .= '<style id="uid-live">' . $css . '</style>';
        }

        // 2. Text-swap shim, only if there are text rules to apply here.
        $textmap = rules::text_map($pagetype, $isadmin);
        if ($textmap) {
            $json = json_encode($textmap, JSON_UNESCAPED_SLASHES);
            $html .= '<script>(function(){var m=' . $json . ';function a(){for(var s in m){'
                . 'try{document.querySelectorAll(s).forEach(function(e){e.textContent=m[s];});}catch(x){}}}'
                . 'if(document.readyState!=="loading"){a();}else{document.addEventListener("DOMContentLoaded",a);}'
                . 'setTimeout(a,600);})();</script>';
        }

        // 3. The editor itself - admins only.
        if ($isadmin && $pagetype !== 'local-uidesign-manage') {
            $base = '/local/uidesign/';
            $cfg = [
                'wwwroot'   => rtrim($CFG->wwwroot, '/'),
                'sesskey'   => sesskey(),
                'pagetype'  => $pagetype,
                'bodyclass' => 'page-' . $pagetype,
                'saveurl'   => $base . 'save.php',
                'manageurl' => $base . 'manage.php',
                'pending'   => rules::pending_count(),
                'rules'     => array_values(array_map(static function ($r) {
                    return [
                        'id' => (int) $r->id, 'kind' => $r->kind, 'pagetype' => $r->pagetype,
                        'selector' => $r->selector, 'property' => $r->property,
                        'value' => $r->value, 'label' => $r->label,
                        'enabled' => (int) $r->enabled, 'published' => (int) $r->published,
                    ];
                }, rules::all())),
            ];
            $html .= '<script>window.UIDESIGN=' . json_encode($cfg, JSON_UNESCAPED_SLASHES) . ';</script>';
            $html .= '<link rel="stylesheet" href="' . $base . 'editor.css?v=' . $assetver . '">';
            $html .= '<script defer src="' . $base . 'editor.js?v=' . $assetver . '"></script>';
        }

        $hook->add_html($html);
    }
}
