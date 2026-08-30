<?php
// =====================================================================
//  PHASE 4 - INSTANT GRADE / RESULT EXPORT  (Moodle CLI -> .xlsx)
//  Exports the full gradebook of a course to a real Excel workbook using
//  Moodle's bundled dataformat_excel writer (no external libraries).
//  Falls back to CSV if the Excel dataformat is unavailable.
//
//  Run inside the container:
//    php /opt/eap/scripts/export_grades.php --course=EXAM101 --out=/tmp/r.xlsx
//    php /opt/eap/scripts/export_grades.php --courseid=4
//    php /opt/eap/scripts/export_grades.php --list           (list course ids)
// =====================================================================
define('CLI_SCRIPT', true);

require('/var/www/html/moodle/config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->dirroot . '/grade/querylib.php');

list($opt, $unrec) = cli_get_params(
    ['help' => false, 'list' => false, 'course' => '', 'courseid' => 0, 'out' => ''],
    ['h' => 'help']
);

if ($opt['help']) {
    cli_writeln("Export a course gradebook to Excel.\n"
        . "  --course=SHORTNAME | --courseid=ID   choose the course\n"
        . "  --out=/path/file.xlsx               output path (default /tmp/grades-<course>.xlsx)\n"
        . "  --list                             list all courses and exit\n");
    exit(0);
}

if ($opt['list']) {
    $courses = $DB->get_records('course', null, 'id', 'id,shortname,fullname');
    foreach ($courses as $c) {
        if ($c->id == SITEID) continue;
        cli_writeln(sprintf("  id=%-4d  %-20s  %s", $c->id, $c->shortname, $c->fullname));
    }
    exit(0);
}

// ---- resolve course ---------------------------------------------
if ($opt['courseid']) {
    $course = $DB->get_record('course', ['id' => $opt['courseid']], '*', MUST_EXIST);
} else if ($opt['course'] !== '') {
    $course = $DB->get_record('course', ['shortname' => $opt['course']], '*', MUST_EXIST);
} else {
    cli_error("Specify --course=SHORTNAME or --courseid=ID  (or --list).");
}
$context = context_course::instance($course->id);
cli_writeln("Course: {$course->shortname} - {$course->fullname} (id {$course->id})");

// ---- gather grade items ---------------------------------------
$items = grade_item::fetch_all(['courseid' => $course->id]);
usort($items, function ($a, $b) {
    if ($a->itemtype === 'course') return 1;
    if ($b->itemtype === 'course') return -1;
    return ($a->sortorder <=> $b->sortorder);
});

$itemcols = [];
foreach ($items as $gi) {
    $name = $gi->itemtype === 'course' ? 'Course total' : $gi->get_name();
    $itemcols[$gi->id] = $name;
}

$columns = array_merge(
    ['ID number', 'Username', 'First name', 'Last name', 'Email'],
    array_values($itemcols)
);

// ---- enrolled learners --------------------------------------
$users = get_enrolled_users($context, 'mod/quiz:attempt', 0, 'u.*', 'u.lastname, u.firstname', 0, 0, true);
if (!$users) {
    // fall back to everyone enrolled
    $users = get_enrolled_users($context, '', 0, 'u.*', 'u.lastname, u.firstname');
}
cli_writeln(count($users) . " learner(s) found.");

// ---- build rows --------------------------------------------
$records = [];
foreach ($users as $u) {
    $row = [
        $u->idnumber,
        $u->username,
        $u->firstname,
        $u->lastname,
        $u->email,
    ];
    foreach ($items as $gi) {
        $g = grade_grade::fetch(['itemid' => $gi->id, 'userid' => $u->id]);
        if ($g && $g->finalgrade !== null) {
            $row[] = grade_format_gradevalue($g->finalgrade, $gi, true, GRADE_DISPLAY_TYPE_REAL, 2);
        } else {
            $row[] = '-';
        }
    }
    $records[] = $row;
}

// ---- pick output format -----------------------------------
$out = $opt['out'] ?: ('/tmp/grades-' . $course->shortname . '-' . date('Ymd-His'));
$excelclass = '\\dataformat_excel\\writer';

if (class_exists($excelclass) && method_exists($excelclass, 'start_output_to_file')) {
    if (!preg_match('/\.xlsx$/i', $out)) $out .= '.xlsx';
    $writer = new $excelclass();
    $writer->set_filename(basename($out, '.xlsx'));
    $writer->start_output_to_file($out);
    $writer->start_sheet($columns);
    $i = 0;
    foreach ($records as $rec) {
        $writer->write_record($rec, $i++);
    }
    $writer->close_sheet($columns);
    $writer->close_output_to_file();
    cli_writeln("Excel workbook written: $out  (" . count($records) . " rows)");
} else {
    if (!preg_match('/\.csv$/i', $out)) $out .= '.csv';
    $fh = fopen($out, 'w');
    fputcsv($fh, $columns);
    foreach ($records as $rec) fputcsv($fh, $rec);
    fclose($fh);
    cli_writeln("Excel dataformat unavailable - wrote CSV instead: $out");
}
exit(0);
