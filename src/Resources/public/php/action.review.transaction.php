<?php
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }

    // Get our passed in Transaction ID
    $transaction_id = $_POST['transaction_id'];
    
    // Determine if Misc. Transaction or not
    if (str_contains($transaction_id, 'm_')) {
        $clean_id = $onlyconsonants = str_replace("m_", "", $transaction_id);
        
        // Set this Misc. Transaction as Published and Reviewed
        $update =  "update tl_transaction_misc set published='1', status='reviewed' WHERE id='".$clean_id."'";
        $result_update = $dbh->query($update);
    
    } else {
        // Set this Transaction as Published and Reviewed
        $update =  "update tl_transaction set published='1', status='reviewed' WHERE id='".$transaction_id."'";
        $result_update = $dbh->query($update);
    }

    echo "fail";
