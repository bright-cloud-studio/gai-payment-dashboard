<?php
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
    
    // Get our data
    $psy_name = $_POST['psychologist'];
    $psy_id = -1;
    
    $myfile = fopen("logs/share_log_".date('m-d-Y_hia').".txt", "w") or die("Unable to open file!");
    fwrite($myfile, "Psy Name: " . $psy_name . "\r\n");
    
    $query_psy =  "select * from tl_member WHERE disable='0'";
    $result_psy = $dbh->query($query_psy);
    if($result_psy) {
        while($row = $result_psy->fetch_assoc()) {
            
            $db_name = $row['firstname'] . " " . $row['lastname'];
            //fwrite($myfile, "DB Name: " . $db_name . "\r\n");
            if($db_name == $psy_name)
                $psy_id = $row['id'];
        }
    }
    
    
    $assignments = json_decode($_POST['assignments']);
    
 
    
    fwrite($myfile, "Psy ID: " . $psy_id . "\r\n");


    if($psy_id != -1) {
        foreach($assignments as $assignment) {
            $query =  "select * from tl_assignment WHERE id='".$assignment."'";
            $result = $dbh->query($query);
            if($result) {
                while($row = $result->fetch_assoc()) {
                    
                    
                    fwrite($myfile, "Assignment: " . $assignment . "\r\n");
                    //fwrite($myfile, $shared. "\r\n");
                    
                    $shared = array();
                    
                    if($row['psychologists_shared'] != null)
                        $shared[] = unserialize($row['psychologists_shared']);
                        
                    $shared[] = $psy_id;
                    
                    $update =  "update tl_assignment set psychologists_shared='".serialize($shared)."' WHERE id='".$assignment."'";
                    $result_update = $dbh->query($update);
                    
                }
            }
            echo "success";
        }
    }
    
    fclose($myfile);
    echo "fail";

