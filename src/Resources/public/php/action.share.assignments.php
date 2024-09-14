<?php
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
    
    $query =  "select * from tl_school WHERE published='1' AND pid='".$_POST['selected_district']."'";
    $result = $dbh->query($query);
    if($result) {
        while($row = $result->fetch_assoc()) {
            
             $options[] = array (
                'value' => $row['id'],
                'label' => $row['school_name']
            );
        }
    }
    
    // encode our PHP array into a JSON format and send that puppy back to the AJAX call
    echo json_encode($options);
