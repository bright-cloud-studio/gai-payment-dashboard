<?php
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
    
    $myfile = fopen("logs/psy_work_form_save_contact_info_parent_log_".date('m-d-Y_hia').".txt", "w") or die("Unable to open file!");
    
    // Get data from ajax
    $assignment_id = $_POST['assignment_id'];
    $contact_info_parent = $_POST['contact_info_parent'];
    $contact_info_parent_cleaned = htmlspecialchars($contact_info_parent, ENT_QUOTES, 'UTF-8');
    
    fwrite($myfile, "SAVING: Assignment ID: " . $assignment_id . "\r\n");
    fwrite($myfile, "SAVING: Contact Info Parent: " . $contact_info_parent_cleaned . "\r\n");

    $update =  "update tl_assignment set contact_info_parent='".$contact_info_parent_cleaned."' WHERE id='".$assignment_id."'";
    $result_update = $dbh->query($update);

    fwrite($myfile, "SAVING: Query Results: " . $result_update . "\r\n");
    
    fclose($myfile);
    echo "success";
