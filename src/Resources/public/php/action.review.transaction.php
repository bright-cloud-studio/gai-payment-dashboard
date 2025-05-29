<?php
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
    
    // Create log file for debugging purposes
    $myfile = fopen("logs/review_transaction_".date('m_d_Y_H:i:s').".txt", "w") or die("Unable to open file!");


    if(isset($_POST['transaction_id']) && isset($_POST['status'])) {

        // Get our passed in Transaction ID
        $transaction_id = $_POST['transaction_id'];
        $status = $_POST['status'];
        
        // Save our POST values to confirm they came in successfully
        fwrite($myfile, "SAVING: Transaction ID: " . $transaction_id . "\r\n");
        fwrite($myfile, "SAVING: Status: " . $status . "\r\n");
        
        // Determine if Misc. Transaction or not
        if (str_contains($transaction_id, 'm_')) {
            
            $clean_id = str_replace("m_", "", $transaction_id);
            
            // Only update 'created' status, we don't want the status to go backwards
            if($status == 'created') {
                // Set this Misc. Transaction as Published and Reviewed
                $update =  "update tl_transaction_misc set published='1', status='reviewed' WHERE id='".$clean_id."'";
                $result_update = $dbh->query($update);
            } else {
                // Set this Misc. Transaction as Published and Reviewed
                $update =  "update tl_transaction_misc set published='1' WHERE id='".$clean_id."'";
                $result_update = $dbh->query($update);
            }
    
        } else {
            
            // Only update 'created' status, we don't want the status to go backwards
            if($status == 'created') {
                // Set this Transaction as Published and Reviewed
                $update =  "update tl_transaction set published='1', status='reviewed' WHERE id='".$transaction_id."'";
                $result_update = $dbh->query($update);
            } else {
                // Set this Transaction as Published and Reviewed
                $update =  "update tl_transaction set published='1' WHERE id='".$transaction_id."'";
                $result_update = $dbh->query($update);
            }
            
        }
    
        // Close our log file
        fclose($myfile);
    
        echo "fail";
    }
