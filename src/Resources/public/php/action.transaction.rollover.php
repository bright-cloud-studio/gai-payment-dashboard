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
    $year = date('y');
    $month = date('n');
    $day = date('j');
    $hour = date("H");

    // If this is the first day of the month
    //if($day == 1) {
        
        // If this is the fifth hour of the day
        //if($hour == 5) {
            
            $query = "select * from tl_transaction WHERE published=''";
            $result = $dbh->query($query);
            if($result) {

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/../logs/transaction_rollover_' . date('m_d_y_hi') . '.txt', 'w') or die("Unable to open file!");
                
                while($row = $result->fetch_assoc()) {
                    
                    $t_year = date('y', $row['date_submitted']);
                    $t_month = date('n', $row['date_submitted']);
                    
                    // If the months don't match
                    if($t_month != $month) {
                        fwrite($myfile, "YEAR: " . $year . " MONTH: " . $month . " DAY: " . $day . " HOUR: " . $hour . "\r\n");
                        fwrite($myfile, "TYEAR: " . $t_year . " TMONTH: " . $t_month . "\r\n");
                    }
                    
                }

                fclose($myfile);
                
            }

        //}        
    //}
