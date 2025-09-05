<?php

    use Bcs\Model\Assignment;
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

    // Loop through all Alert emails
    $assignments = Assignment::findAll();
    if($assignments) {
        foreach ($assignments as $assignment) {
            fwrite($log, "Assignment: ID ". $assignment->id ."\r\n");
            fwrite($log, "Psychologist: ID ". $assignment->psychologist ."\r\n");
            fwrite($log, "30 Day: ". $assignment->date_30_day ."\r\n");
            fwrite($log, "Meeting Date: ". $assignment->meeting_date ."\r\n");
            fwrite($log, "----------------------------------------------------\r\n\r\n");
        }
    }

    // Close our log file
    fclose($log);
