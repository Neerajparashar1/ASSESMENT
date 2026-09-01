<?php
// local_uidesign upgrade steps.

defined('MOODLE_INTERNAL') || die();

/**
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_uidesign_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2026090140) {

        // Draft / publish: new "published" column on the rule table.
        $table = new xmldb_table('local_uidesign_rule');
        $field = new xmldb_field('published', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'enabled');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            // Everything that existed before draft/publish was already live.
            $DB->set_field('local_uidesign_rule', 'published', 1, []);
        }
        $index = new xmldb_index('published', XMLDB_INDEX_NOTUNIQUE, ['published', 'enabled']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Version history table.
        $vtable = new xmldb_table('local_uidesign_version');
        if (!$dbman->table_exists($vtable)) {
            $vtable->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $vtable->add_field('note', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, '');
            $vtable->add_field('rulecount', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $vtable->add_field('snapshot', XMLDB_TYPE_TEXT, null, null, null, null, null);
            $vtable->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $vtable->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $vtable->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $vtable->add_index('timecreated', XMLDB_INDEX_NOTUNIQUE, ['timecreated']);
            $dbman->create_table($vtable);
        }

        upgrade_plugin_savepoint(true, 2026090140, 'local', 'uidesign');
    }

    return true;
}
