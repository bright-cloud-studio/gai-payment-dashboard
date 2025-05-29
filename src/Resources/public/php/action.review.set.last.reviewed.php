<?php
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }

    // If our psy_id was actually passed to us
    if(isset($_POST['psy_id'])) {
    
        $psy_id = $_POST['psy_id'];
        
        $update =  "update tl_member set last_review_and_submit='".time()."' WHERE id='".$psy_id."'";
        $result_update = $dbh->query($update);
        
        echo "pass";
        
    }
