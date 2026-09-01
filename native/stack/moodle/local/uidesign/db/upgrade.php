<?php
// local_uidesign upgrade steps.

defined('MOODLE_INTERNAL') || die();

/**
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_uidesign_upgrade($oldversion) {
    // No upgrade steps yet - the install.xml is the source of truth.
    return true;
}
