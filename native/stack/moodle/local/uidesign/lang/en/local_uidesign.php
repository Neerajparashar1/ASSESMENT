<?php
// Language strings for local_uidesign.

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'UI Design Studio';
$string['uidesign:manage'] = 'Use the visual Design Studio to override site UI (colours, fonts, sizes, hidden elements)';

// Settings.
$string['enabled'] = 'Enable the Design Studio';
$string['enabled_desc'] = 'When off, no overrides are applied and the "Design" button is hidden. Saved rules are kept.';

// Admin entry points.
$string['launchtitle'] = 'Open Design Studio';
$string['managetitle'] = 'Design Studio - rules';
$string['manageintro'] = 'Every override you have saved from the Design Studio. This page is never affected by the rules, so you can always recover here.';
$string['col_target'] = 'Target';
$string['col_kind'] = 'Type';
$string['col_where'] = 'Where';
$string['col_value'] = 'Value';
$string['col_on'] = 'On';
$string['col_actions'] = '';
$string['norules'] = 'No overrides yet. Open the Design Studio from the navbar to start.';
$string['resetall'] = 'Reset everything';
$string['resetall_confirm'] = 'Delete every saved override and return the site to its default look?';
$string['export'] = 'Export (JSON)';
$string['import'] = 'Import (JSON)';
$string['import_done'] = '{$a} rule(s) imported.';
$string['reset_done'] = 'All overrides removed.';
$string['deleted'] = 'Override removed.';

// Errors.
$string['err_invalidrule'] = 'Invalid rule: a kind and a selector are required.';
$string['err_invalidprop'] = 'Invalid rule: an element override needs an allowed CSS property.';
$string['err_badimport'] = 'That does not look like a Design Studio export file.';

$string['privacy:metadata'] = 'The UI Design Studio stores only site-wide presentation rules. It records which administrator last edited each rule, and no information about any other user.';
