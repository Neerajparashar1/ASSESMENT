<?php
// Capabilities for local_uidesign.

defined('MOODLE_INTERNAL') || die();

$capabilities = [

    // Use the visual Design Studio (create/edit/delete UI override rules that
    // apply site-wide). System context, no archetypes -> only a deliberate
    // role assignment grants it; site admins bypass anyway. The feature
    // injects CSS/markup on every page, hence RISK_XSS | RISK_CONFIG.
    'local/uidesign:manage' => [
        'riskbitmask'  => RISK_XSS | RISK_CONFIG,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => [],
    ],
];
