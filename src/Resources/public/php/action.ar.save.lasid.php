<?php
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
    
    $myfile = fopen("logs/admin_review_save_student_log_".date('m_d_Y_H:i:s').".txt", "w") or die("Unable to open file!");
    
    
    
    
    // Get data from ajax
    $assignment_id = $_POST['assignment_id'];
    $student = $_POST['student'];
    $lasid = $_POST['lasid'];
    
    fwrite($myfile, "SAVING: Assignment ID: " . $assignment_id . "\r\n");
    fwrite($myfile, "SAVING: Student: " . $student . "\r\n");
    fwrite($myfile, "SAVING: LASID: " . $lasid . "\r\n");

    $update =  "update tl_student set lasid='".$lasid."' WHERE id='".$student."'";
    $result_update = $dbh->query($update);

    fwrite($myfile, "SAVING: Query Results: " . $result_update . "\r\n");
    
    fclose($myfile);
    echo "success";
