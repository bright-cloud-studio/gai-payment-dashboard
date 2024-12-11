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
    if($day == 1) {
        
        // If this is the fifth hour of the day
        if($hour == 5) {
            
            $query = "select * from tl_transaction WHERE published=''";
            $result = $dbh->query($query);
            if($result) {

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/../logs/transaction_rollover_' . date('m-d-Y_hia') . '.txt', 'w') or die("Unable to open file!");
                
                while($row = $result->fetch_assoc()) {

                    write($myfile, "YEAR: " . $year . " MONTH: " . $month . " DAY: " . $day . " HOUR: " . $hour . "\r\n");

                    $t_year = date_format($row['date_submitted'], 'y');
                    $t_month = date_format($row['date_submitted'], 'n');

                    write($myfile, "TYEAR: " . $t_year . " TMONTH: " . "\r\n");
                    
                }

                fclose($myfile);
                
            }

        }        
    }
