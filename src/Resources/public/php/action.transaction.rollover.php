<?php

    // Initialize Session, include Composer
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }

    // Trigger when X days before the end of the month
    $weekly_reminder = 7;
    $final_reminder = 0;
    // Calculates remaining days until the next month
    $how_many_days = date('t') - date('j');
    // Gets the current Hour in 24 hour format
    $hour = date("H");


    // Send the "Week Remaining" email
    if($how_many_days == $weekly_reminder) {
        
        // If this is the desired hour
        if($hour == 12) {
            
            $query = "select * from tl_member WHERE disable='0' AND email!=''";
            $result = $dbh->query($query);
            if($result) {
                while($row = $result->fetch_assoc()) {
                    $row['lastname'];
                }
            }

        }
        
    }
