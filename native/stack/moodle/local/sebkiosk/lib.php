<?php
// local_sebkiosk - small server-side front-end helpers for the exam portal.

defined('MOODLE_INTERNAL') || die();

/**
 * Put the signed-in candidate's full name + username in the navbar.
 *
 * The stock user menu on this theme is just a two-letter initials disc, which
 * is no use to an invigilator checking who is logged in on a hall machine.
 * This renders a compact "Abhay Yadav @s2026123" chip into the top bar on
 * every page, for every real logged-in user.
 *
 * @param renderer_base $renderer
 * @return string HTML added to the navbar
 */
function local_sebkiosk_render_navbar_output(renderer_base $renderer): string {
    global $USER;

    if (!isloggedin() || isguestuser()) {
        return '';
    }

    $name = fullname($USER);
    $user = s($USER->username);

    return html_writer::div(
        html_writer::span($name, 'itm-idchip-name') .
        html_writer::span($user, 'itm-idchip-user'),
        'itm-idchip',
        ['title' => $name . '  ·  ' . $user]
    );
}
