<?php
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
    
    $myfile = fopen("logs/remove_log_".date('m-d-Y_hia').".txt", "w") or die("Unable to open file!");
    
    
    // Get data from ajax
    $psy_id = $_POST['psychologist'];
    $assignments = json_decode($_POST['assignments']);
    
    fwrite($myfile, "PSY ID: " . $psy_id . "\r\n");

    // Loop through selected assignments by id
    foreach($assignments as $assignment) {
        
        // get the assignment from the db
        $query =  "select * from tl_assignment WHERE id='".$assignment."'";
        $result = $dbh->query($query);
        if($result) {
            while($row = $result->fetch_assoc()) {
                
                // write what assignment we are on
                fwrite($myfile, "Assignment: " . $assignment . "\r\n");

                
                // If in Shared or not
                
                if($row['psychologist'] != $psy_id) {
                    // if Shared psy
                    
                    fwrite($myfile, "Shared Assignment!\r\n");
                    // remove from shared list and save in db
                    $shared = unserialize($row['psychologists_shared']);
                    if (($key = array_search($psy_id, $shared)) !== false) {
                        unset($shared[$key]);
                    }
                    
                    $update =  "update tl_assignment set psychologists_shared='".serialize($shared)."' WHERE id='".$assignment."'";
                    $result_update = $dbh->query($update);
                } else {
                    // if Primary psy
                    
                    fwrite($myfile, "Primary Assignment!\r\n");
                    // primary so unpublish
                    $update =  "update tl_assignment set published='0' WHERE id='".$assignment."'";
                    $result_update = $dbh->query($update);
                }
                
                
                
            }
        }
        
        
        
        
        
    }
    
    fclose($myfile);
    echo "success";

