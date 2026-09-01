<?php
// ITM Group of Institutions, Gwalior - Online Examination Portal.
// Exam Wizard: friendlier quiz creation + bulk question upload on top of Moodle.

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_examwizard';
$plugin->version   = 2026090100;
$plugin->requires  = 2024100700;          // Moodle 4.5.
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.9.0 (Candidate password reset: issue a fresh policy-valid login password and read it back once)';
