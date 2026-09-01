<?php
// SEB kiosk helper: auto-submit an in-progress quiz attempt when the
// candidate leaves Safe Exam Browser (or the exam page) without submitting.
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_sebkiosk';
$plugin->version   = 2026090110;
$plugin->requires  = 2024100700;   // Moodle 4.5
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.1.0 (navbar shows the signed-in user name + username)';
