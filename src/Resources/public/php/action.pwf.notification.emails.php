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

// Create a log file so we can track this is working accurately
$log = fopen($_SERVER['DOCUMENT_ROOT'] . '/../logs/pwf_notification_emails_'.date('m_d_y').'.txt', "a+") or die("Unable to open file!");

// Loop through all Assignments
$assignments = Assignment::findAll();
if($assignments) {
    foreach ($assignments as $assignment) {
        
        // If this Assignment has a Psychologist applied
        if($assignment->psychologist) {

            ///////////////////////////
            // NOTIFICATION - 30 DAY //
            ///////////////////////////
            
            if($assignment->date_30_day) {
    
                // Get Today's, 30-day's and five days before 30-day's dates
                $today = date('m/d/y');
                $date_30_day = $assignment->date_30_day;
                $date_notice_day = date('m/d/y', strtotime('-5 days', strtotime($date_30_day)));
                
                // If today is the same as five days before 30 day
                if($today == $date_notice_day) {
                    
                    // Write in our log, it is Notification Day for this Assignment
                    fwrite($log, "NOTICE DAY FOR 30-DAY \r\n");
                    fwrite($log, "Assignment: ID ". $assignment->id ."\r\n");
                    fwrite($log, "Psychologist: ID ". $assignment->psychologist ."\r\n");
                    fwrite($log, "Today: ". $today ."\r\n");
                    fwrite($log, "30 Day: ". $date_30_day ."\r\n");
                    fwrite($log, "Notice Day: ". $date_notice_day ."\r\n");
                    fwrite($log, "TODAY is 5 days before 30 day! \r\n");
                    
                    // If we have no 'Meeting Date'
                    if(!$assignment->meeting_date) {
    
                        // if it is 7am
                        $hour = date("H");
                        if($hour == 7) {
                            
                            fwrite($log, "NO MEETING DATE - SEND NOTIFICATION! \r\n");
                            
                            $psychologist = MemberModel::findOneBy('id', $assignment->psychologist);
                            $district = District::findOneBy('id', $assignment->district);
                            $school = School::findOneBy('id', $assignment->school);
                            $student = Student::findOneBy('id', $assignment->student);
    
                            // For development purposes, the recipient email can be overridden with my email
                            //$addr = 'mark@brightcloudstudio.com';
                            $addr = $psychologist->email;
                			$headers = "MIME-Version: 1.0" . "\r\n";
                			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                			$headers .= 'From: billing@globalassessmentsinc.com' . "\r\n";
                			$headers .= 'Return-Path: billing@globalassessmentsinc.com' . "\r\n";
                			$headers .= 'Cc: susan@globalassessmentsinc.com, anna@globalassessmentsinc.com' . "\r\n";
                			$sub = Config::get('pwf_notice_30_day_subject'); 
                			$message = "
                				<html>
                				<head>
                				<title>Global Assessments, Inc. - Psych Work Form - Notice - 30 Day</title>
                				</head>
                				<body>".
                                    Config::get('pwf_notice_30_day_body')
                                ."</body>
                				</html>
                				";
        
                            // TEMPLATE TAGS - Psychologist
                            $message = str_replace('$firstname', $psychologist->firstname, $message);
                            $message = str_replace('$lastname', $psychologist->lastname, $message);
                            // TEMPLATE TAGS - Assignment
                            $message = str_replace('$date_created', date('m/d/y', $assignment->date_created), $message);
                            $message = str_replace('$date_30_day', $assignment->date_30_day, $message);
                            // TEMPLATE TAGS - District
                            $message = str_replace('$district', $district->district_name, $message);
                            // TEMPLATE TAGS - School
                            $message = str_replace('$school', $school->school_name, $message);
                            // TEMPLATE TAGS - Student Initials
                            $message = str_replace('$student_initials', getPWFInitials($student->name), $message);
                            // TEMPLATE TAGS - LASID
                            $message = str_replace('$lasid', $student->lasid, $message);
                            // TEMPLATE TAGS - SASID
                            $message = str_replace('$sasid', $student->sasid, $message);
                            
                			mail($addr, $sub, $message, $headers, "-fbilling@globalassessmentsinc.com");
        
                            // Create Email Record of this email
                            $record = new EmailRecord();
                            $record->tstamp = time();
                            $record->date_created = date('m/d/y g:i a');
                            $record->assignment = $assignment->id;
                            $record->email_type = 'pwf_no_meeting_date_entered';
                            $record->email_recipient = $psychologist->id;
                            $record->email_subject = $sub;
                            $record->email_body = $message;
                            $record->save();
                            
                        }
                    }
                    
                }
                
                
                fwrite($log, "Meeting Date: ". $assignment->meeting_date ."\r\n");
                fwrite($log, "----------------------------------------------------\r\n\r\n");
            }
    
            /////////////////////////////////////
            // NOTIFICATION - Report Submitted //
            /////////////////////////////////////
            if($assignment->meeting_date) {
    
                // Get Today's, 30-day's and five days before 30-day's dates
                $today = date('m/d/y');
                $meeting_date = $assignment->meeting_date;
                $date_notice_day = date('m/d/y', strtotime('-4 days', strtotime($meeting_date)));
                
                if($today == $date_notice_day) {
                    
                    fwrite($log, "TODAY is 4 days before Meeting Date! \r\n");
                    
                    if($assignment->report_submitted != 'yes') {
    
                         // if it is 7am
                        $hour = date("H");
                        if($hour == 7) {
                        
                            fwrite($log, "NOTICE DAY FOR NO MEETING DATE \r\n");
                            fwrite($log, "Assignment: ID ". $assignment->id ."\r\n");
                            fwrite($log, "Psychologist: ID ". $assignment->psychologist ."\r\n");
                            fwrite($log, "Today: ". $today ."\r\n");
                            fwrite($log, "Meeting Date: ". $meeting_date ."\r\n");
                            fwrite($log, "Notice Day: ". $date_notice_day ."\r\n");
                            fwrite($log, "NO MEETING DATE - SEND NOTIFICATION! \r\n");
                            
                            $psychologist = MemberModel::findOneBy('id', $assignment->psychologist);
                            $district = District::findOneBy('id', $assignment->district);
                            $school = School::findOneBy('id', $assignment->school);
                            $student = Student::findOneBy('id', $assignment->student);
    
                            // For development purposes, the recipient email can be overridden with my email
                            //$addr = 'mark@brightcloudstudio.com';
                            $addr = $psychologist->email;
                			$headers = "MIME-Version: 1.0" . "\r\n";
                			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                			$headers .= 'From: billing@globalassessmentsinc.com' . "\r\n";
                			$headers .= 'Return-Path: billing@globalassessmentsinc.com' . "\r\n";
                			$headers .= 'Cc: susan@globalassessmentsinc.com, anna@globalassessmentsinc.com' . "\r\n";
                			$sub = Config::get('pwf_notice_report_submitted_subject'); 
                			$message = "
                				<html>
                				<head>
                				<title>Global Assessments, Inc. - Psych Work Form - Notice - Report Submitted</title>
                				</head>
                				<body>".
                                    Config::get('pwf_notice_report_submitted_body')
                                ."</body>
                				</html>
                				";
        
                            // TEMPLATE TAGS - Psychologist
                            $message = str_replace('$firstname', $psychologist->firstname, $message);
                            $message = str_replace('$lastname', $psychologist->lastname, $message);
                            // TEMPLATE TAGS - Assignment
                            $message = str_replace('$date_created', date('m/d/y', $assignment->date_created), $message);
                            $message = str_replace('$meeting_date', $assignment->meeting_date, $message);
                            // TEMPLATE TAGS - District
                            $message = str_replace('$district', $district->district_name, $message);
                            // TEMPLATE TAGS - School
                            $message = str_replace('$school', $school->school_name, $message);
                            // TEMPLATE TAGS - Student Initials
                            $message = str_replace('$student_initials', getPWFInitials($student->name), $message);
                            // TEMPLATE TAGS - LASID
                            $message = str_replace('$lasid', $student->lasid, $message);
                            // TEMPLATE TAGS - SASID
                            $message = str_replace('$sasid', $student->sasid, $message);
                            
                			mail($addr, $sub, $message, $headers, "-fbilling@globalassessmentsinc.com");
        
                            // Create Email Record of this email
                            $record = new EmailRecord();
                            $record->tstamp = time();
                            $record->date_created = date('m/d/y g:i a');
                            $record->assignment = $assignment->id;
                            $record->email_type = 'pwf_no_report_submitted';
                            $record->email_recipient = $psychologist->id;
                            $record->email_subject = $sub;
                            $record->email_body = $message;
                            $record->save();
                        
                        }
                    }
                    
                    fwrite($log, "----------------------------------------------------\r\n\r\n");
                }
    
            }
        }
        
    }
}

// Close our log file
fclose($log);


// Simple custom function to take in a $name as a string and return back the capital letters.
// Explode will split the $name string into separate words using space as a divider
// strtoupper will strip out all characters for each word extept the first character
// leaving us with only the initials
function getPWFInitials($name) {
    $words = explode(" ", $name);
    $initials = "";

    foreach ($words as $word) {
        $initials .= strtoupper(substr($word, 0, 1));
    }

    return $initials;
}
