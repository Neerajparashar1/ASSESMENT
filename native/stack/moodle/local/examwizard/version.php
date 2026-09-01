<?php
// ITM Group of Institutions, Gwalior - Online Examination Portal.
// Exam Wizard: friendlier quiz creation + bulk question upload on top of Moodle.

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_examwizard';
$plugin->version   = 2026090103;
$plugin->requires  = 2024100700;          // Moodle 4.5.
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.9.3 (Batches: block enrolling a batch into the site front page)';
