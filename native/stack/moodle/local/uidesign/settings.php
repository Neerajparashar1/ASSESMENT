<?php
// local_uidesign - admin settings + entry points.

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    // Master on/off.
    $settings = new admin_settingpage('local_uidesign',
        get_string('pluginname', 'local_uidesign'));
    $settings->add(new admin_setting_configcheckbox('local_uidesign/enabled',
        get_string('enabled', 'local_uidesign'),
        get_string('enabled_desc', 'local_uidesign'), 1));
    $ADMIN->add('localplugins', $settings);

    // Launch the visual Design Studio (bounces to the dashboard with the
    // overlay open). This is the ONLY entry point - no navbar button.
    $ADMIN->add('localplugins', new admin_externalpage('local_uidesign_launch',
        get_string('launchtitle', 'local_uidesign'),
        new moodle_url('/local/uidesign/launch.php'),
        'local/uidesign:manage'));

    // Plain rule list / recovery page.
    $ADMIN->add('localplugins', new admin_externalpage('local_uidesign_manage',
        get_string('managetitle', 'local_uidesign'),
        new moodle_url('/local/uidesign/manage.php'),
        'local/uidesign:manage'));
}
