<?php

    use Bcs\Model\EmailRecordModel;
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
    
    $myfile = fopen("logs/pwf_update_email_record_status".date('m-d-Y_hia').".txt", "w") or die("Unable to open file!");
    
    // Get data from ajax
    $assignment_id = $_POST['assignment_id'];
    $psychologist_id = $_POST['psychologist_id'];
    $email_type = $_POST['email_type'];
    
    fwrite($myfile, "SAVING: Assignment ID: " . $assignment_id . "\r\n");
    fwrite($myfile, "SAVING: Psychologist ID: " . $psychologist_id . "\r\n");
    fwrite($myfile, "SAVING: Email Type: " . $email_type . "\r\n");

    $email_record = EmailRecord::findOneBy(['psychologist', 'assignment', 'email_type'], [$assignment_id, $psychologist_id, $email_type]);
    if($email_record) {
        fwrite($myfile, "FOUND EMAIL RECORD: " . $email_record->id . "\r\n");
    } else {
        fwrite($myfile, "NOT FOUND EMAIL RECORD: " . $email_record->id . "\r\n");
    }

    
    fclose($myfile);
    echo "success";
