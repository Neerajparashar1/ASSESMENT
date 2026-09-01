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

    // Reset a candidate's login password from the exam cell and read the new
    // one back once. Moodle only ever stores a one-way hash, so an EXISTING
    // password can never be shown - this issues a fresh, policy-valid one.
    // Deliberately manager-only (site admins bypass anyway); keep it off any
    // publicly reachable tunnel.
    'local/examwizard:resetpassword' => [
        'riskbitmask'  => RISK_DATALOSS | RISK_PERSONAL,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => [
            'manager' => CAP_ALLOW,
        ],
    ],
];
