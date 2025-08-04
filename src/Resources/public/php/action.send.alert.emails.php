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

            // Get the Warning email date and Today's date
            $warning_date = date('m_d_y', $alert_email->warning_date);
            $today = date('m_d_y', time());
            // Get the current hour, as we only want to send out at noon
            $hour = date("H");
            $hour = 12;

            // If today is the day listed in the Alert Email
            if($warning_date == $today) {
                fwrite($log, "Today is the SEND day! \r\n");

                if($hour == 12) {
                    fwrite($log, "IT IS the sending hour! \r\n");

                    // Send the email
                    $addr = 'mark@brightcloudstudio.com';
        			$headers = "MIME-Version: 1.0" . "\r\n";
        			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        			$headers .= 'From: <billing@globalassessmentsinc.com>' . "\r\n";
        			$headers .= 'Cc: ed@globalassessmentsinc.com' . "\r\n";
        			$sub = $alert_email->warning_subject;
        			$message = "
        				<html>
        				<head>
        				<title>Global Assessments, Inc. - FINAL DAY Reminder</title>
        				</head>
        				<body>".
                            $alert_email->warning_body
                        ."</body>
        				</html>
        				";
        			mail($addr, $sub, $message, $headers);

                    // Stamp as sent so we dont do this again today
                    
                    
                } else
                    fwrite($log, "NOT YET the sending hour! \r\n");
                
            }


        }
    }

        // If it is "SEND DATE", and it is within the noon hour, send the email

            // mark 'last_sent' as today to prevent duplicate mailings



    

    // Close our log file
    fclose($log);
