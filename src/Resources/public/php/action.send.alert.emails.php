<?php

    // Initialize Session, include Composer
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }

    // Create a log file so we can track this is working accurately
    $log = fopen($_SERVER['DOCUMENT_ROOT'] . '/../logs/'.date('m_d_y').'_reminder_emails.txt', "a+") or die("Unable to open file!");






    // Loop through all Alert emails

        // If it is "SEND DATE", and it is within the noon hour, send the email

            // mark 'last_sent' as today to prevent duplicate mailings



    

    // Close our log file
    fclose($log);
