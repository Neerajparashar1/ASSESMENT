<?php
// Hook callbacks for local_uidesign.

defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook'     => \core\hook\output\before_standard_head_html_generation::class,
        'callback' => \local_uidesign\hook_callbacks::class . '::before_standard_head_html_generation',
        'priority' => 0,
    ],
];
