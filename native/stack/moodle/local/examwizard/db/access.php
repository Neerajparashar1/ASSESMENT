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

    // Run live exam control during an ongoing exam: pause / reopen / extend a
    // student / force-submit / resume. Deliberately separate from
    // mod/quiz:manage so a hall invigilator can steer a running exam WITHOUT
    // being able to edit the quiz's settings, questions or grades.
    'local/examwizard:control' => [
        'riskbitmask'  => RISK_DATALOSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => [
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
        ],
    ],
];
