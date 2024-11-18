<?php

    // Initialize Session, include Composer
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

    // Trigger when X days before the end of the month
    $days_before = 7;
    // Calculates remaining days until the next month
    $how_many_days = date('t') - date('j');
    // Gets the current Hour in 24 hour format
    $hour = date("H");

    // If this is the desired day
    if($days_before == $how_many_days) {
        
        // If this is the desired hour
        if($hour == 12) {

            // Send Reminder Email
            /*
            $addr = 'mark@brightcloudstudio.com';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: <billing@globalassessmentsinc.com>' . "\r\n";
            $headers .= 'Cc: ed@globalassessmentsinc.com' . "\r\n";
            $sub = "Automated Reminder Email";
            $message = "
                <html>
                <head>
                <title>GAI - Invoice</title>
                </head>
                <body>
                    <p>Trigger Date: $days_before</p>
                    <p>Days Remaining: $how_many_days</p>
                    <p>Current Hour: $hour</p>
                </body>
                </html>
                ";
        
            mail($addr, $sub, $message, $headers);
            */
            
        }
        
    } else if( $days_before == 0) {
        // If this is instead the last day of the month
        /*
        $addr = 'mark@brightcloudstudio.com';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: <billing@globalassessmentsinc.com>' . "\r\n";
        $headers .= 'Cc: ed@globalassessmentsinc.com' . "\r\n";
        $sub = "Automated Reminder Email";
        $message = "
            <html>
            <head>
            <title>GAI - Invoice</title>
            </head>
            <body>
                <p>Trigger Date: $days_before</p>
                <p>Days Remaining: $how_many_days</p>
                <p>Current Hour: $hour</p>
            </body>
            </html>
            ";
    
        mail($addr, $sub, $message, $headers);
        */
    }



    // SEND DEBUG EMAIL
    $addr = 'mark@brightcloudstudio.com';
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <billing@globalassessmentsinc.com>' . "\r\n";
    $headers .= 'Cc: ed@globalassessmentsinc.com' . "\r\n";
    $sub = "Automated Reminder Email";
    $message = "
        <html>
        <head>
        <title>GAI - Invoice</title>
        </head>
        <body>
            <p>Trigger Date: $days_before</p>
            <p>Days Remaining: $how_many_days</p>
            <p>Current Hour: $hour</p>
        </body>
        </html>
        ";

    mail($addr, $sub, $message, $headers);
