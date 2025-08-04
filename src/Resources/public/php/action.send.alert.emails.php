<?php

    use Bcs\Model\AlertEmail;

    // Initialize Session, include Composer
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }

    // Create a log file so we can track this is working accurately
    $log = fopen($_SERVER['DOCUMENT_ROOT'] . '/../logs/alert_emails_'.date('m_d_y').'.txt', "a+") or die("Unable to open file!");


    // Loop through all Alert emails
    fwrite($log, "Looping through Alert Emails \r\n");
    $alert_emails = AlertEmail::findAll();
    if($alert_emails) {
        foreach ($alert_emails as $alert_email) {

            $warning_date = date('m_d_y', $alert_email->warning_date);
            $today = date('m_d_y', time());

            if($warning_date == $today) {
                fwrite($log, "Today is the SEND day! \r\n");
            } else {
                fwrite($log, "Today IS NOT the SEND day!: \r\n");
            }


        }
    }

        // If it is "SEND DATE", and it is within the noon hour, send the email

            // mark 'last_sent' as today to prevent duplicate mailings



    

    // Close our log file
    fclose($log);
