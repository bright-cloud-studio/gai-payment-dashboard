<?php
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }

    

    $transaction_id = $_POST['transaction_id'];
    
    if (str_contains($transaction_id, 'm_')) {
        $clean_id = $onlyconsonants = str_replace("m_", "", $transaction_id);
        //$update =  "update tl_transaction_misc set published='1' WHERE id='".$clean_id."'";
        $update =  "delete from tl_transaction_misc  WHERE id='".$clean_id."'";
        $result_update = $dbh->query($update);
    
    } else {
        //$update =  "update tl_transaction set published='1' WHERE id='".$transaction_id."'";
        $update =  "delete from tl_transaction WHERE id='".$transaction_id."'";
        $result_update = $dbh->query($update);
    }
    

    echo "fail";

