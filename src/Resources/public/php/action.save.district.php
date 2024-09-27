<?php
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
    
    $myfile = fopen("logs/psy_work_form_save_date_of_birth_log_".date('m-d-Y_hia').".txt", "w") or die("Unable to open file!");
    
    // Get data from ajax
    $student_id = $_POST['student_id'];
    $date_of_birth = $_POST['date_of_birth'];
    
    fwrite($myfile, "SAVING: Student ID: " . $student_id . "\r\n");
    fwrite($myfile, "SAVING: Date of Birth: " . $date_of_birth . "\r\n");

    $update =  "update tl_student set date_of_birth='".$date_of_birth."' WHERE id='".$student_id."'";
    $result_update = $dbh->query($update);

    fwrite($myfile, "SAVING: Query Results: " . $result_update . "\r\n");
    
    fclose($myfile);
    echo "success";
