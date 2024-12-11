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
                    
                    // Send "Final Reminder" Email
        			$addr = $row['email'];
        			$headers = "MIME-Version: 1.0" . "\r\n";
        			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        			$headers .= 'From: <billing@globalassessmentsinc.com>' . "\r\n";
        			$headers .= 'Cc: ed@globalassessmentsinc.com' . "\r\n";
        			$sub = "The end of the month is here, make sure to Submit and Review your Transactions";
        			$message = "
        				<html>
        				<head>
        				<title>Global Assessments, Inc. - FINAL DAY Reminder</title>
        				</head>
        				<body>
        					<p>".$row['firstname']." ".$row['lastname'].",</p>
        					<p>We are a few days away from the end of the month. Please make sure to Submit and Review your Transactions for this month. All reports must be Submitted to Anna by Midnight 24-hours prior to the end of the last business day of the month. All Invoices must be Reviewed and Submitted in the Portal by Midnight on the last day of the month.</p>
        					<p>Best,<br>Global Assessments, Inc.</p>
        				</body>
        				</html>
        				";
        			mail($addr, $sub, $message, $headers);
                }
            }

        }
        
    }
    
    
    // Send the "FINAL DAY" email
    else if($how_many_days == $final_reminder) {
        
        // If this is the desired hour
        if($hour == 12) {
            
            $query = "select * from tl_member WHERE disable='0' AND email!=''";
            $result = $dbh->query($query);
            if($result) {
                while($row = $result->fetch_assoc()) {
                  $row['lastname']
                  
                }
            }

        }
        
    }

