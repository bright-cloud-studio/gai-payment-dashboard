<?php

    use Bcs\Model\AlertEmail;

    use Contao\MemberModel;

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
            $warning_last_sent = date('m_d_y', $alert_email->warning_last_sent);
            $today = date('m_d_y', time());

            fwrite($log, "Warning Date: " . $warning_date . "\r\n");
            fwrite($log, "Warning Last Sent: " . $warning_last_sent . "\r\n");
            fwrite($log, "Today: " . $today . "\r\n");

            // Get the current hour, as we only want to send out at noon
            $hour = date("H");
            $hour = 12;
            
            // WARNING EMAIL
            if($warning_date == $today) {
                fwrite($log, "Today is the SEND day! \r\n");


                if($warning_last_sent != $today) {
                    fwrite($log, "HAVENT sent yet today! \r\n");
                    if($hour == 12) {
                        fwrite($log, "IT IS the sending hour! \r\n");


                        // Loop through all Members then email them
                        fwrite($log, "Looping through Psychologists\r\n");
                        $psychologists = MemberModel::findBy('disable', '0');
                        if($psychologists) {
                            foreach ($psychologists as $psychologist) {

                                fwrite($log, "PSY ID: ". $psychologist->id ." \r\n");
                                
                            }
                        }

                        fwrite($log, "Saving Last Sent for Warning! \r\n");
                        $alert_email->warning_last_sent = date("m_d_y", time());
                        $alert_email->save();
                        
                    } else
                        fwrite($log, "NOT YET the sending hour! \r\n");
                } else
                     fwrite($log, "ALREADY sent today! \r\n");
            }




        }
    }

        // If it is "SEND DATE", and it is within the noon hour, send the email

            // mark 'last_sent' as today to prevent duplicate mailings



    

    // Close our log file
    fclose($log);
