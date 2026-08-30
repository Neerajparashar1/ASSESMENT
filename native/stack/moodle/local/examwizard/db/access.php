<?php
// Capabilities for local_examwizard.

defined('MOODLE_INTERNAL') || die();

$capabilities = [

    'local/examwizard:use' => [
        'riskbitmask'  => RISK_SPAM,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => [
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
        ],
    ],
];
