<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = '127.0.0.1';
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'moodleuser';
$CFG->dbpass    = 'zyaW8MiddlhDGF3LYiJmmCd0J1bH';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => 3306,
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_unicode_ci',
);

$CFG->wwwroot   = 'http://localhost:8080';
$CFG->dataroot  = 'E:\ASSESMENT\moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 02777;

// ==== EAP MANAGED CONFIG (managed by Setup-MoodleNative.ps1 - do not hand edit) ==
$CFG->sslproxy          = false;
$CFG->reverseproxy      = false;
$CFG->cachejs           = true;
$CFG->enablestats       = false;
$CFG->pathtophp         = 'E:/ASSESMENT/native/stack/php/php.exe';
$CFG->autosavefrequency = 15;
$CFG->additionalhtmlfooter = '<!-- sebkiosk --><style>#page-mod-quiz-attempt .qtext,#page-mod-quiz-attempt .formulation,#page-mod-quiz-attempt .info,#page-mod-quiz-attempt #quiznavigation,#page-mod-quiz-attempt .qn_buttons{-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}#page-mod-quiz-attempt input,#page-mod-quiz-attempt textarea,#page-mod-quiz-attempt select,#page-mod-quiz-attempt [contenteditable]{-webkit-user-select:text;-moz-user-select:text;user-select:text}</style><script defer src="http://localhost:8080/local/sebkiosk/exam-ui.js?v=16"></script>';
// ==== /EAP MANAGED CONFIG ==========================================

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!