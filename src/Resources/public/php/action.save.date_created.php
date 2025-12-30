<?php
    
    date_default_timezone_set('America/New_York');
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
    
    $myfile = fopen("logs/psy_work_form_save_date_created_log_".date('m-d-Y_hia').".txt", "w") or die("Unable to open file!");
    
    // Get data from ajax
    $assignment_id = $_POST['assignment_id'];
    $date_created = $_POST['date_created'];
    
    fwrite($myfile, "SAVING: Assignment ID: " . $assignment_id . "\r\n");
    fwrite($myfile, "SAVING: Date Created: " . $date_created . "\r\n");

    $update =  "update tl_assignment set date_created='".$date_created."' WHERE id='".$assignment_id."'";
    $result_update = $dbh->query($update);

    fwrite($myfile, "SAVING: Query Results: " . $result_update . "\r\n");
    
    fclose($myfile);
    echo "success";
