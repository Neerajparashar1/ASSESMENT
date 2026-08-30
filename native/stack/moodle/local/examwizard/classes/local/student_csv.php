<?php
// Parse a friendly student roster (CSV / XLSX rows) for bulk create + enrol.

namespace local_examwizard\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Reads a roster spreadsheet and normalises + validates each row.
 * Columns (synonym tolerant): firstname, lastname, username, email, password.
 */
class student_csv {

    public static function parse_csv(string $text): array {
        $text = preg_replace('~^\xEF\xBB\xBF~', '', $text);
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $rows = [];
        foreach (explode("\n", $text) as $line) {
            if ($line !== '') {
                $rows[] = str_getcsv($line);
            }
        }
        return self::parse_rows($rows);
    }

    public static function parse_rows(array $rows): array {
        $rows = array_values(array_filter($rows, static function ($r) {
            foreach ((array) $r as $c) {
                if (trim((string) $c) !== '') {
                    return true;
                }
            }
            return false;
        }));
        if (!$rows) {
            return ['fatal' => get_string('err_emptyfile', 'local_examwizard'), 'rows' => [], 'valid' => 0, 'errors' => 0];
        }

        $syn = [
            'firstname' => 'firstname', 'first name' => 'firstname', 'first' => 'firstname', 'givenname' => 'firstname',
            'lastname' => 'lastname', 'last name' => 'lastname', 'last' => 'lastname', 'surname' => 'lastname',
            'username' => 'username', 'user name' => 'username', 'login' => 'username', 'user id' => 'username',
            'userid' => 'username', 'rollno' => 'username', 'roll no' => 'username', 'roll number' => 'username',
            'email' => 'email', 'e-mail' => 'email', 'mail' => 'email',
            'password' => 'password', 'pass' => 'password', 'pwd' => 'password',
            'name' => 'fullname', 'full name' => 'fullname', 'student name' => 'fullname',
        ];
        $map = [];
        foreach (array_shift($rows) as $i => $label) {
            $k = preg_replace('~\s+~', ' ', strtolower(trim((string) $label)));
            if (isset($syn[$k]) && !isset($map[$syn[$k]])) {
                $map[$syn[$k]] = $i;
            }
        }
        if (!isset($map['username']) || (!isset($map['firstname']) && !isset($map['fullname']))) {
            return ['fatal' => get_string('st_err_header', 'local_examwizard'), 'rows' => [], 'valid' => 0, 'errors' => 0];
        }
        if (!$rows) {
            return ['fatal' => get_string('err_emptyfile', 'local_examwizard'), 'rows' => [], 'valid' => 0, 'errors' => 0];
        }

        $out = [];
        $valid = 0;
        $errors = 0;
        $seen = [];
        $n = 0;
        foreach ($rows as $raw) {
            $n++;
            $get = static function ($key) use ($raw, $map) {
                return isset($map[$key], $raw[$map[$key]]) ? trim((string) $raw[$map[$key]]) : '';
            };
            $first = $get('firstname');
            $last = $get('lastname');
            if ($first === '' && $get('fullname') !== '') {
                $parts = preg_split('~\s+~', $get('fullname'), 2);
                $first = $parts[0];
                $last = $parts[1] ?? '';
            }
            $row = [
                'n' => $n,
                'username' => \core_text::strtolower($get('username')),
                'firstname' => $first,
                'lastname' => $last !== '' ? $last : '.',
                'email' => \core_text::strtolower($get('email')),
                'password' => $get('password'),
                'errors' => [],
            ];

            $e = [];
            if ($row['username'] === '') {
                $e[] = get_string('st_err_nousername', 'local_examwizard');
            } else if (!preg_match('~^[a-z0-9._@-]+$~', $row['username'])) {
                $e[] = get_string('st_err_baduser', 'local_examwizard', s($row['username']));
            } else if (isset($seen[$row['username']])) {
                $e[] = get_string('st_err_dupuser', 'local_examwizard', s($row['username']));
            }
            $seen[$row['username']] = true;
            if ($first === '') {
                $e[] = get_string('st_err_noname', 'local_examwizard');
            }
            if ($row['email'] !== '' && !validate_email($row['email'])) {
                $e[] = get_string('st_err_bademail', 'local_examwizard', s($row['email']));
            }

            $row['errors'] = $e;
            $e ? $errors++ : $valid++;
            $out[] = $row;
        }
        return ['fatal' => null, 'rows' => $out, 'valid' => $valid, 'errors' => $errors];
    }

    /**
     * Create any missing users and enrol every valid row into $courseid as students.
     *
     * @return array ['created' => int, 'existing' => int, 'enrolled' => int, 'defaultpassword' => string|null]
     */
    public static function apply(array $rows, int $courseid, string $defaultpassword): array {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->libdir . '/enrollib.php');

        $course = get_course($courseid);
        $manual = enrol_get_plugin('manual');
        $instance = $DB->get_record('enrol', ['courseid' => $courseid, 'enrol' => 'manual']);
        if (!$instance && $manual) {
            $instance = $DB->get_record('enrol',
                ['id' => $manual->add_instance($course)]);
        }
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);

        $created = 0;
        $existing = 0;
        $enrolled = 0;
        foreach ($rows as $r) {
            if (!empty($r['errors'])) {
                continue;
            }
            $user = $DB->get_record('user', ['username' => $r['username'], 'mnethostid' => $CFG->mnet_localhost_id]);
            if (!$user) {
                $new = new \stdClass();
                $new->username = $r['username'];
                $new->firstname = $r['firstname'];
                $new->lastname = $r['lastname'];
                $new->email = $r['email'] !== '' ? $r['email'] : ($r['username'] . '@example.invalid');
                $new->auth = 'manual';
                $new->confirmed = 1;
                $new->mnethostid = $CFG->mnet_localhost_id;
                $new->password = $r['password'] !== '' ? $r['password'] : $defaultpassword;
                $new->id = user_create_user($new, true, false);
                $user = $DB->get_record('user', ['id' => $new->id]);
                $created++;
            } else {
                $existing++;
            }
            if ($instance && !is_enrolled(\context_course::instance($courseid), $user)) {
                $manual->enrol_user($instance, $user->id, $studentroleid);
                $enrolled++;
            }
        }
        return ['created' => $created, 'existing' => $existing, 'enrolled' => $enrolled,
            'defaultpassword' => $defaultpassword];
    }

    public static function template_rows(): array {
        return [
            ['firstname', 'lastname', 'username', 'email', 'password'],
            ['Aarav', 'Sharma', 's2026101', 'aarav.sharma@example.com', 'Exam@2026'],
            ['Diya', 'Verma', 's2026102', 'diya.verma@example.com', 'Exam@2026'],
            ['Kabir', 'Singh', 's2026103', '', 'Exam@2026'],
        ];
    }
}
