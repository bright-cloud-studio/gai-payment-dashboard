<?php

    // Initialize Session, start Composer
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

    // if our date is X days before end of month
    $days_before = 7;
    // The number of days left in the month
    $how_many_days = date('t') - date('j');

    $hour = date("H");


    // If today is Weekly Reminder Day!
    if($days_before == $how_many_days) {
    }


    $addr = 'mark@brightcloudstudio.com';

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    
    // More headers
    $headers .= 'From: <billing@globalassessmentsinc.com>' . "\r\n";
    //$headers .= 'Cc: ed@globalassessmentsinc.com' . "\r\n";
    
    
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





    // Check if passed correct hour

    // If so, send Reminder emails

  
