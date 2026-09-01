<?php
// local_uidesign - privacy provider.

namespace local_uidesign\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * The plugin stores only site-wide presentation rules. The single user
 * reference is `usermodified` on {local_uidesign_rule} - an audit note of
 * which administrator last edited a rule. No data about learners is held.
 */
class provider implements \core_privacy\local\metadata\null_provider {

    /**
     * @return string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
