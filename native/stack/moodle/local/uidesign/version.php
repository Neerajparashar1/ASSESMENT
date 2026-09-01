<?php
// local_uidesign - admin-only visual "Design Studio" for the ITM exam portal.
// Stores presentation overrides as data and injects them as a live <style>
// on every page (no SCSS recompile).

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_uidesign';
$plugin->version   = 2026090150;
$plugin->requires  = 2024100700;          // Moodle 4.5.
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.4.0 (Phase 2b: Bake-to-SCSS, background-image toolbar, rename-a-fixed-label string overrides)';
