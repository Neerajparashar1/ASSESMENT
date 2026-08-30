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

$string['privacy:metadata'] = 'The Exam Wizard plugin does not store any personal data; imported questions are stored by Moodle\'s question bank.';
