<?php

use Bcs\Model\Assignment;
use Bcs\Model\District;
use Bcs\Model\EmailRecord;
use Bcs\Model\School;
use Bcs\Model\Student;
use Contao\Config;
use Contao\MemberModel;

// Initialize Session, include Composer
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

// Connect to DB
$dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
if ($dbh->connect_error) {
    die("Connection failed: " . $dbh->connect_error);
}

// Loop through all Assignments
$assignments = Assignment::findAll();
if($assignments) {
    foreach ($assignments as $assignment) {  
    }
}
