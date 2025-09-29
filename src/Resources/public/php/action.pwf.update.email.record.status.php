<?php

use Bcs\Model\EmailRecord;
    
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

$email_record_query =  "select * from tl_email_record WHERE assignment='".$assignment_id."' AND email_recipient='".$psychologist_id."' AND email_type='".$email_type."'";
$email_record = $dbh->query($email_record_query);

if($email_record) {
    fwrite($myfile, "Email Record: FOUND!\r\n");
    while($row = $email_record->fetch_assoc()) {
        $update_query =  "UPDATE tl_email_record SET status='resolved' WHERE id='".$row['id']."'";
        $update_record = $dbh->query($update_query);
        fwrite($myfile, "Updating Status for Email Record ID: ".$row['id']."\r\n");
    }
}


fclose($myfile);
echo "success";
