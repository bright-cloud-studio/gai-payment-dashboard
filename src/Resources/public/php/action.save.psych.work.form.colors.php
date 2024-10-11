<?php
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
    
    $myfile = fopen("logs/psy_work_form_save_colors_".date('m-d-Y_hia').".txt", "w") or die("Unable to open file!");
    
    // Get data from ajax
    $psych_id = $_POST['psych_id'];
    $color_ids = $_POST['color_ids'];
    
    fwrite($myfile, "SAVING: Psych ID: " . $psych_id . "\r\n");
    fwrite($myfile, "SAVING: Row IDs: " . $color_ids . "\r\n");

    $update =  "update tl_member set psych_work_form_colors='".$color_ids."' WHERE id='".$psych_id."'";
    $result_update = $dbh->query($update);

    fwrite($myfile, "SAVING: Query Results: " . $result_update . "\r\n");
    
    fclose($myfile);
    echo "success";
