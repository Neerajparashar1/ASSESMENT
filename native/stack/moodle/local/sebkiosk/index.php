<?php
require(__DIR__ . '/../../config.php');
require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/sebkiosk/index.php');
$PAGE->set_title('SEB kiosk auto-submit');
echo $OUTPUT->header();
echo $OUTPUT->box('This plugin auto-submits an in-progress quiz attempt when the '
    . 'candidate leaves Safe Exam Browser. There is nothing to configure.');
echo $OUTPUT->footer();
