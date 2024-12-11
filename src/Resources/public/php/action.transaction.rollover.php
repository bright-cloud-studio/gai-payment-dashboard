<?php

    // Initialize Session, include Composer
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }

    // Get the current day and hour
    $day = date('j');
    $hour = date("H");


    // If this is the first day of the month
    if($day == 1) {
        
        // If this is the fifth hour of the day
        if($hour == 5) {

            
            $query = "select * from tl_member WHERE disable='0' AND email!=''";
            $result = $dbh->query($query);
            if($result) {
                while($row = $result->fetch_assoc()) {
                    $row['lastname'];
                }
            }


            

        }
        
    }
