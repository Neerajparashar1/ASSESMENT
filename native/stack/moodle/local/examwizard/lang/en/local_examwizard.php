<?php
// Language strings for local_examwizard.

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Exam Wizard';
$string['examwizard:use'] = 'Use the Exam Wizard to build exams and upload questions';

// Landing.
$string['landingintro'] = 'Build exams and bulk-upload questions without wrestling with Moodle\'s full quiz form.';
$string['choosecourse'] = 'Choose a subject / course';
$string['nocourses'] = 'You do not have permission to manage questions in any course yet. Ask an administrator to enrol you as a teacher.';
$string['uploadquestions'] = 'Upload questions';
$string['uploadquestions_desc'] = 'Fill a spreadsheet with your questions and import them in one go.';

// Uploader.
$string['step1'] = 'Upload file';
$string['step2'] = 'Review';
$string['step3'] = 'Import';
$string['downloadtemplate'] = 'Download the template';
$string['downloadtemplate_help'] = 'One row per question. Columns: Type, Question, A-F, Answer, Marks, Feedback.';
$string['templatecsv'] = 'CSV template';
$string['templatexlsx'] = 'Excel template';
$string['questionsfile'] = 'Questions file';
$string['questionsfile_help'] = 'A .csv or .xlsx built from the template above. GIFT and Moodle XML files are also accepted and handed to Moodle\'s own importer.';
$string['targetcategory'] = 'Save into question category';
$string['newcategoryname'] = 'New category name';
$string['newcategoryname_help'] = 'Leave blank to use the course default category. If you type a name here a new category is created under it.';
$string['alsoaddtoquiz'] = 'Also add every imported question to this quiz';
$string['alsoaddtoquiz_none'] = 'Do not add to a quiz';
$string['review'] = 'Review questions';
$string['reviewintro'] = 'Check the parsed questions below. Rows with a problem are highlighted and will be skipped.';
$string['rowsok'] = '{$a} question(s) ready to import';
$string['rowserrors'] = '{$a} row(s) have problems and will be skipped';
$string['confirmimport'] = 'Import {$a} question(s)';
$string['backtoupload'] = 'Choose a different file';
$string['importdone'] = 'Import complete';
$string['importsummary'] = '{$a->ok} question(s) imported into "{$a->category}".';
$string['importedintoquiz'] = 'They were also added to the quiz "{$a}".';
$string['gotoquestionbank'] = 'Open the question bank';
$string['createanother'] = 'Upload more questions';

// Column / parse errors.
$string['err_notype'] = 'Missing Type (use mcq, multi, truefalse or short)';
$string['err_badtype'] = 'Unknown Type "{$a}" (use mcq, multi, truefalse or short)';
$string['err_noquestion'] = 'Question text is empty';
$string['err_nooptions'] = 'Needs at least two options (columns A and B)';
$string['err_noanswer'] = 'Answer is empty';
$string['err_badanswer'] = 'Answer "{$a}" does not match the options provided';
$string['err_badtf'] = 'Answer must be True or False';
$string['err_multicount'] = 'Multiple-answer questions need between 2 and 6 options with a clean split';
$string['err_badmarks'] = 'Marks must be a positive number';
$string['err_emptyfile'] = 'The file has no data rows';
$string['err_noheader'] = 'The first row must be the column headers from the template';
$string['err_parsefail'] = 'Could not read the file: {$a}';

// Types (shown in the preview).
$string['type_mcq'] = 'Multiple choice (one answer)';
$string['type_multi'] = 'Multiple choice (several answers)';
$string['type_truefalse'] = 'True / False';
$string['type_short'] = 'Short answer';

// ===== Create Exam wizard (Phase 2) =====
$string['createexam'] = 'Create an exam';
$string['createexam_desc'] = 'A short guided form that builds the quiz with exam-ready settings.';
$string['w_title'] = 'Create an exam';
$string['w_step_basics'] = 'Basics';
$string['w_step_questions'] = 'Questions';
$string['w_step_rules'] = 'Rules';
$string['w_step_review'] = 'Review';
$string['w_next'] = 'Next';
$string['w_back'] = 'Back';
$string['w_publish'] = 'Publish exam';
$string['w_published'] = 'Exam published';
$string['w_published_msg'] = 'The exam "{$a->name}" has been created. {$a->questions}';
$string['w_pub_added'] = '{$a} question(s) were added to it.';
$string['w_pub_later'] = 'No questions yet - add them from the quiz\'s Questions tab.';
$string['w_wired_seb'] = 'Safe Exam Browser is enforced (manual config, quit link -> auto-submit).';
$string['w_wired_proctoring'] = 'AI proctoring is required for this exam.';
$string['w_openquiz'] = 'Open the exam';
$string['w_editquestions'] = 'Edit questions';
$string['w_another'] = 'Create another';
$string['w_generalsection'] = 'General (top of the course)';

// Step 1.
$string['w_examname'] = 'Exam name';
$string['w_section'] = 'Place in course section';
$string['w_timelimit'] = 'Duration (minutes)';
$string['w_timelimit_help'] = 'How long a candidate gets once they start. 0 = no limit (not recommended for a proctored exam).';
$string['w_instructions'] = 'Instructions for candidates (optional)';
$string['w_err_timelimit'] = 'Duration cannot be negative';
$string['w_err_closebeforeopen'] = 'The close time must be after the open time';

// Step 2.
$string['w_questions_intro'] = 'How do you want to add questions to this exam?';
$string['w_source'] = 'Questions source';
$string['w_source_upload'] = 'Upload a question file now (CSV / Excel / GIFT / XML)';
$string['w_source_category'] = 'Use questions already in a question category';
$string['w_source_later'] = 'I\'ll add questions later';

// Step 3.
$string['w_attempts'] = 'Attempts allowed';
$string['w_review'] = 'When can candidates see their results?';
$string['w_review_help'] = '"After the exam closes" keeps marks hidden until everyone has finished - the usual choice for a formal exam.';
$string['w_review_afterclose'] = 'Only after the exam closes';
$string['w_review_immediately'] = 'Immediately after each attempt';
$string['w_shuffle'] = 'Shuffle the order of questions';
$string['w_negative'] = 'Negative marking (-1/3 on wrong single-choice answers)';
$string['w_negative_help'] = 'Applies a one-third penalty to wrong answers on single-answer multiple-choice questions imported in this exam.';
$string['w_security'] = 'Security';
$string['w_seb'] = 'Require Safe Exam Browser';
$string['w_seb_help'] = 'Candidates must sit the exam inside SEB. Leaving SEB without submitting auto-submits the attempt. You still need to give candidates the .seb file for this quiz.';
$string['w_proctoring'] = 'Require AI proctoring (webcam)';
$string['w_proctoring_help'] = 'Records / monitors the candidate through their webcam. Do not combine with SEB unless your SEB config allows camera capture.';

// Review labels.
$string['w_minutes'] = 'minutes';
$string['w_none'] = 'none';
$string['w_no'] = 'No';
$string['w_yes'] = 'Yes';
$string['w_rev_when'] = 'Window';
$string['w_rev_opens'] = 'Opens:';
$string['w_rev_closes'] = 'Closes:';
$string['w_rev_anytime'] = 'Open any time (no dates set)';
$string['w_rev_q_upload'] = 'Upload file - {$a->valid} ready, {$a->errors} will be skipped';
$string['w_rev_q_category'] = 'From an existing question category';
$string['w_rev_q_later'] = 'To be added later';

// ===== Exam Control home (Phase 3) =====
$string['home_title'] = 'Exam Control';
$string['home_intro'] = 'Everything you need to run an exam - set up, build, monitor and mark - in one place.';
$string['g_exams'] = 'Exams';
$string['g_students'] = 'Students';
$string['g_subjects'] = 'Subjects';
$string['g_live'] = 'Live now';
$string['copied'] = 'Copied';

$string['chk_title'] = 'Getting started';
$string['chk_progress'] = '{$a->done} of {$a->total} done';
$string['chk_dismiss'] = 'Hide';
$string['chk_show'] = 'Show the getting-started checklist';
$string['chk_branding'] = 'Apply your institute branding';
$string['chk_students'] = 'Add your students';
$string['chk_exam'] = 'Create your first exam';
$string['chk_sebpw'] = 'Set the Safe Exam Browser quit password';
$string['chk_testrun'] = 'Do a test run of an exam';

$string['qa_title'] = 'Quick actions';
$string['qa_createexam'] = 'Create an exam';
$string['qa_upload'] = 'Upload questions';
$string['qa_students'] = 'Add students';
$string['qa_seb'] = 'SEB settings';

$string['ye_title'] = 'Your exams';
$string['ye_empty'] = 'No exams yet. Use "Create an exam" above to build your first one.';
$string['ye_exam'] = 'Exam';
$string['ye_status'] = 'Status';
$string['ye_attempts'] = 'Attempts';
$string['ye_monitor'] = 'Monitor';
$string['ye_grades'] = 'Grades';
$string['ye_seb'] = 'Get .seb';
$string['ye_attcount'] = '{$a->live} in progress / {$a->done} submitted';

$string['stat_live'] = 'Live';
$string['stat_open'] = 'Open';
$string['stat_scheduled'] = 'Scheduled';
$string['stat_closed'] = 'Closed';
$string['ye_results'] = 'Results';

// Live now spotlight.
$string['live_title'] = 'Live now';
$string['live_refresh'] = 'This card refreshes every 30 seconds.';
$string['live_inprogress'] = 'in progress';
$string['live_submitted'] = 'submitted';
$string['live_notstarted'] = 'not started';
$string['live_monitor'] = 'Open monitor';

// Recent results.
$string['rr_title'] = 'Recent results';
$string['rr_submitted'] = 'Submitted';
$string['rr_export'] = 'Export CSV';

// Results page.
$string['rs_title'] = 'Exam results';
$string['rs_intro'] = 'A quick summary and a per-candidate breakdown. Use "Download CSV" to open the marks in Excel.';
$string['rs_download'] = 'Download CSV';
$string['rs_fullreport'] = 'Full Moodle report';
$string['rs_attempted'] = 'Attempted';
$string['rs_average'] = 'Average score';
$string['rs_inprogress'] = 'In progress';
$string['rs_notstarted'] = 'Not started';
$string['rs_passrate'] = 'Pass rate';
$string['rs_score'] = 'Score';
$string['rs_percent'] = 'Percent';
$string['rs_result'] = 'Result';
$string['rs_duration'] = 'Time taken';
$string['rs_state'] = 'State';
$string['rs_st_finished'] = 'Submitted';
$string['rs_st_inprogress'] = 'In progress';
$string['rs_st_abandoned'] = 'Abandoned';
$string['rs_st_notstarted'] = 'Not started';

$string['help_title'] = 'Handy to know';
$string['help_sebpw'] = 'Safe Exam Browser quit password';
$string['help_reveal'] = 'show';
$string['help_copy'] = 'Copy';
$string['help_setsebpw'] = 'Not set yet - set it in SEB settings';
$string['help_studentlogin'] = 'How students sign in';
$string['help_studentlogin_d'] = 'They open {$a}/login and use the username and password from the roster you uploaded.';
$string['help_distribute'] = 'Giving out the .seb file';
$string['help_distribute_d'] = 'Download the .seb from an exam row and email it to candidates. If you change any SEB setting on that exam, send the file again - the old one stops working.';

// ===== Bulk add students (Phase 3) =====
$string['st_title'] = 'Add students';
$string['st_intro'] = 'Upload a roster to create the student accounts and enrol them into this subject in one go.';
$string['st_file'] = 'Student roster (CSV or Excel)';
$string['st_defaultpw'] = 'Default password';
$string['st_defaultpw_help'] = 'Used for any row that leaves the Password column blank. Give this to those students so they can sign in.';
$string['st_err_header'] = 'The first row must have the column headers - at least "username" and "firstname" (or "name").';
$string['st_err_nousername'] = 'Username is empty';
$string['st_err_baduser'] = 'Username "{$a}" has spaces or symbols that are not allowed';
$string['st_err_dupuser'] = 'Username "{$a}" appears more than once in the file';
$string['st_err_noname'] = 'First name / name is empty';
$string['st_err_bademail'] = '"{$a}" is not a valid email address';
$string['st_ready'] = '{$a} student(s) ready';
$string['st_name'] = 'Name';
$string['st_username'] = 'Username';
$string['st_confirm'] = 'Create & enrol {$a} student(s)';
$string['st_done'] = 'Roster processed';
$string['st_done_msg'] = '{$a->created} new account(s) created, {$a->existing} already existed, {$a->enrolled} enrolled into this subject.';
$string['st_viewparticipants'] = 'View participants';
$string['st_addmore'] = 'Add more students';

$string['privacy:metadata'] = 'The Exam Wizard plugin does not store any personal data; imported questions are stored by Moodle\'s question bank.';
