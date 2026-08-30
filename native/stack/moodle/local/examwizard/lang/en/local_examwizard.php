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

$string['privacy:metadata'] = 'The Exam Wizard plugin does not store any personal data; imported questions are stored by Moodle\'s question bank.';
