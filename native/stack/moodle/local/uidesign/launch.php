<?php
// local_uidesign - launcher. Reached from Site administration; bounces the
// admin to the dashboard with the Design Studio overlay auto-opened.

require(__DIR__ . '/../../config.php');

require_login();
require_capability('local/uidesign:manage', context_system::instance());

redirect(new moodle_url('/my/', ['uidstudio' => 1]));
