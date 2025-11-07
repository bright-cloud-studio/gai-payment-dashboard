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
    $month = date('m');
    $day = date('j');
    $hour = date("H");

    // If this is the first day of the month
    if($day == 1) {
        
        // If this is the fifth hour of the day
        if($hour == 5) {
            
            // Update Transactions
            $query = "select * from tl_transaction WHERE published=''";
            $result = $dbh->query($query);
            if($result) {

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/../logs/transactions_rollover_' . date('m_d_y_hi') . '.txt', 'w') or die("Unable to open file!");
                
                while($row = $result->fetch_assoc()) {
                    
                    $date_submitted = $row['date_submitted'];
                    $t_year = date('y', $date_submitted);
                    $t_month = date('m', $date_submitted);
                    $t_id = $row['id'];
                    
                    // If the months don't match
                    if($t_month != $month) {
                        fwrite($myfile, "CURRENT - YEAR: " . $year . " MONTH: " . $month . " DAY: " . $day . " HOUR: " . $hour . "\r\n");
                        fwrite($myfile, "TRANSACTION " . $t_id . ": TYEAR: " . $t_year . " TMONTH: " . $t_month . "\r\n");
                        
                        echo "String: " . $month . '/01/' . $year . "<br>";
                        $time = strtotime($month . '/01/' . $year);
                        echo "Time: " . $time . "<br>";
                        
                        // Update the 'originally_submitted' date to our current date_submitted
                        $update = "update tl_transaction set originally_submitted='".$date_submitted."', date_submitted='".$time."' WHERE id='".$t_id."'";
                        $result_update = $dbh->query($update);
                    }
                    
                }

                fclose($myfile);
            }
            
            // Update Misc Transactions
            $query2 = "select * from tl_transaction_misc WHERE published=''";
            $result2 = $dbh->query($query2);
            if($result2) {

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/../logs/misc_transactions_rollover_' . date('m_d_y_hi') . '.txt', 'w') or die("Unable to open file!");
                
                while($row = $result2->fetch_assoc()) {
                    
                    $date_submitted = $row['date_submitted'];
                    $t_year = date('y', $date_submitted);
                    $t_month = date('m', $date_submitted);
                    $t_id = $row['id'];
                    
                    // If the months don't match
                    if($t_month != $month) {
                        fwrite($myfile, "CURRENT - YEAR: " . $year . " MONTH: " . $month . " DAY: " . $day . " HOUR: " . $hour . "\r\n");
                        fwrite($myfile, "TRANSACTION " . $t_id . ": TYEAR: " . $t_year . " TMONTH: " . $t_month . "\r\n");
                        
                        echo "String: " . $month . '/01/' . $year . "<br>";
                        $time = strtotime($month . '/01/' . $year);
                        echo "Time: " . $time . "<br>";
                        
                        // Update the 'originally_submitted' date to our current date_submitted
                        $update = "update tl_transaction_misc set originally_submitted='".$date_submitted."', date_submitted='".$time."' WHERE id='".$t_id."'";
                        $result_update = $dbh->query($update);
                    }
                    
                }

                fclose($myfile);
            }

        }        
    }
