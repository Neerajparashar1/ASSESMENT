<?php
// =====================================================================
//  Apply the ITM Group of Institutions, Gwalior branding to Moodle:
//    - site + compact logo (native\assets\itm-logo*.svg)
//    - Boost Union theme + config\moodle\custom.scss  (maroon / gold)
//    - brand colour, site name, favicon
//  Run:  E:\ASSESMENT\native\stack\php\php.exe E:\ASSESMENT\native\apply_itm_theme.php
//  Idempotent - safe to re-run after editing the SCSS or SVGs.
// =====================================================================
define('CLI_SCRIPT', true);
require(__DIR__ . '/stack/moodle/config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/adminlib.php');

function out($m) { cli_writeln('[itm-theme] ' . $m); }

$syscontext = context_system::instance();
$fs = get_file_storage();
$assets = __DIR__ . '/assets';

// ---------------------------------------------------------------
// 1. Logos -> core_admin AND theme_boost_union file areas
//    (Boost Union renders ITS OWN logo/logocompact in the navbar and
//    prefers them over core_admin's - so both must be set.)
// ---------------------------------------------------------------
// 'logo'        = full official ITM GOI logo  (login card, dashboard plate)
// 'logocompact' = the "ITM" bars mark cropped out of it (native\make_logo_mark.php)
//                 - the tagline lines are illegible at navbar size.
$logos = ['logo' => 'itm-logo.png', 'logocompact' => 'itm-logo-mark.png'];
$logotargets = [
    ['core_admin', 'setcore'],
    ['theme_boost_union', 'theme_boost_union'],
];
foreach ($logos as $area => $fname) {
    $src = "$assets/$fname";
    if (!is_readable($src)) { out("WARN missing $src - skipping $area"); continue; }
    foreach ($logotargets as [$component, $confplugin]) {
        $fs->delete_area_files($syscontext->id, $component, $area);
        $fs->create_file_from_pathname([
            'contextid' => $syscontext->id,
            'component' => $component,
            'filearea'  => $area,
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $fname,
        ], $src);
        $plugin = $confplugin === 'setcore' ? 'core_admin' : $confplugin;
        set_config($area, "/$fname", $plugin);
        out("set $plugin/$area = /$fname");
    }
}
// Boost Union navbar logo sizing. NOTE: do NOT set 'loginpagebrand' to a
// logo option - the login card already renders the logo via custom.scss
// (.loginform::before); Boost Union's own logo there would double it up.
set_config('loginpagebrand', 'heading', 'theme_boost_union');
set_config('maxcompactlogowidth', '190', 'theme_boost_union');
// favicon (use the compact mark)
if (is_readable("$assets/itm-logo-compact.svg")) {
    $fs->delete_area_files($syscontext->id, 'core_admin', 'favicon');
    $fs->create_file_from_pathname([
        'contextid' => $syscontext->id, 'component' => 'core_admin', 'filearea' => 'favicon',
        'itemid' => 0, 'filepath' => '/', 'filename' => 'itm-logo-compact.svg',
    ], "$assets/itm-logo-compact.svg");
    set_config('favicon', '/itm-logo-compact.svg', 'core_admin');
    out('set core_admin/favicon');
}

// ---------------------------------------------------------------
// 2. Theme + SCSS
// ---------------------------------------------------------------
$scssfile = getenv('EAP_CUSTOM_SCSS') ?: (dirname(__DIR__) . '/config/moodle/custom.scss');
$scss = is_readable($scssfile) ? file_get_contents($scssfile) : '';
if ($scss === '') { cli_error("cannot read SCSS at $scssfile"); }

$theme = file_exists("$CFG->dirroot/theme/boost_union/version.php") ? 'boost_union' : 'boost';
$tcomp = "theme_$theme";
set_config('theme', $theme);
set_config('themedesignermode', 0);
set_config('scss', $scss, $tcomp);          // Boost Union "Raw SCSS"
set_config('brandcolor', '#8a1b2c', $tcomp);
set_config('bootstrapcolorprimary', '#8a1b2c', $tcomp);
set_config('bootstrapcolorsuccess', '#1f7a4d', $tcomp);
out("theme = $theme  |  brandcolor = #8a1b2c  |  SCSS " . strlen($scss) . " bytes");

// ---------------------------------------------------------------
// 3. Site identity
// ---------------------------------------------------------------
$fullname  = 'ITM Group of Institutions, Gwalior';
$shortname = 'ITM GOI Exams';
$DB->set_field('course', 'fullname',  $fullname,  ['id' => SITEID]);
$DB->set_field('course', 'shortname', $shortname, ['id' => SITEID]);
set_config('supportname', 'ITM GOI Examination Cell');
out("site name -> \"$fullname\"  ($shortname)");

// web fonts via <head> (SCSS @import of a remote URL breaks the compiler)
$fontlink = '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
    . '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?'
    . 'family=Source+Serif+4:opsz,wght@8..60,500;8..60,600;8..60,700&'
    . 'family=Inter:wght@400;500;600;700&display=swap">';
$head = (string) get_config('core', 'additionalhtmlhead');
$head = preg_replace('~<link[^>]*fonts\.(googleapis|gstatic)\.com[^>]*>~i', '', $head);
set_config('additionalhtmlhead', trim($head) . "\n" . $fontlink);
out('injected Google Fonts <link> into additionalhtmlhead');

// ---------------------------------------------------------------
// 4. Bump theme revision + purge everything
// ---------------------------------------------------------------
theme_reset_static_caches();
if (function_exists('theme_purge_used_files')) { theme_purge_used_files(); }
set_config('themerev', time());
purge_all_caches();
out('caches purged, theme revision bumped');

cli_writeln('');
cli_writeln('  Done. Hard-refresh the browser (Ctrl+F5).  ' . rtrim($CFG->wwwroot, '/'));
exit(0);
