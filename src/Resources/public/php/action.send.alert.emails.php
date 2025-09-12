<?php

    use Bcs\Model\AlertEmail;
    use Bcs\Model\EmailRecord;
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
    $alert_emails = AlertEmail::findAll();
    if($alert_emails) {
        foreach ($alert_emails as $alert_email) {

            // Get the Warning email date and Today's date
            $warning_date = date('m_d_y', $alert_email->warning_date);
            $final_date = date('m_d_y', $alert_email->final_date);
            $today = date('m_d_y', time());

            // Warning Last Sent will either be empty or have a value
            $warning_last_sent;
            if($alert_email->warning_last_sent == '')
                $warning_last_sent = 0;
            else
                $warning_last_sent = date('m_d_y', $alert_email->warning_last_sent);

            // Final Last Sent will either be empty or have a value
            $final_last_sent;
            if($alert_email->final_last_sent == '')
                $final_last_sent = 0;
            else
                $final_last_sent = date('m_d_y', $alert_email->final_last_sent);

            // Get the current hour, as we only want to send out at noon
            $hour = date("H");
            //$hour = 12; // fixed as noon for development purposes


            // WARNING EMAIL
            if($warning_date == $today) {
                fwrite($log, "WARNING: Send Day \r\n");

                if($warning_last_sent != $today) {
                    fwrite($log, "WARNING: Unset yet today \r\n");
                    if($hour == 12) {
                        fwrite($log, "WARNING: Correct Hour \r\n");

                        $psychologists = MemberModel::findBy('disable', '0');
                        if($psychologists) {
                            foreach ($psychologists as $psychologist) {
                                
                                // Only sending to myself for dev purposes
                                if($psychologist->id == 7) {
                                    fwrite($log, "WARNING: Emailing ". $psychologist->firstname . " " . $psychologist->lastname . "\r\n");
                                    $addr = $psychologist->email;
                        			$headers = "MIME-Version: 1.0" . "\r\n";
                        			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                        			$headers .= 'From: billing@globalassessmentsinc.com' . "\r\n";
                                    $headers .= 'Return-Path: billing@globalassessmentsinc.com' . "\r\n";
                        			$headers .= 'Cc: ed@globalassessmentsinc.com' . "\r\n";
                        			$sub = $alert_email->warning_subject;
                        			$message = "
                        				<html>
                        				<head>
                        				<title>Global Assessments, Inc. - Week Remaining Reminder</title>
                        				</head>
                        				<body>".
                                            $alert_email->warning_body
                                        ."</body>
                        				</html>
                        				";
    
                                    // TEMPLATE TAGS
                                    $message = str_replace('$firstname', $psychologist->firstname, $message);
                                    $message = str_replace('$lastname', $psychologist->lastname, $message);
                                    
                        			mail($addr, $sub, $message, $headers, "-fbilling@globalassessmentsinc.com");

                                    // Create Email Record of this email
                                    $record = new EmailRecord();
                                    $record->tstamp = time();
                                    $record->email_type = 'alert_week_remaining';
                                    $record->email_recipient = $psychologist->id;
                                    $record->email_subject = $sub;
                                    $record->email_body = $message;
                                    $record->save();
                                    
                                }
                                
                            }
                        }
                        $alert_email->warning_last_sent = time();
                        $alert_email->save();
                    }
                }
            }



            // FINAL DAY EMAIL
            if($final_date == $today) {
                fwrite($log, "FINAL: Send Day \r\n");

                if($final_last_sent != $today) {
                    fwrite($log, "FINAL: Unset yet today \r\n");
                    if($hour == 12) {
                        fwrite($log, "FINAL: Correct Hour \r\n");

                        $psychologists = MemberModel::findBy('disable', '0');
                        if($psychologists) {
                            foreach ($psychologists as $psychologist) {
                                
                                // Only sending to myself for dev purposes
                                if($psychologist->id == 7) {
                                    fwrite($log, "FINAL: Emailing ". $psychologist->firstname . " " . $psychologist->lastname . "\r\n");
                                    $addr = $psychologist->email;
                        			$headers = "MIME-Version: 1.0" . "\r\n";
                        			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                        			$headers .= 'From: billing@globalassessmentsinc.com' . "\r\n";
                                    $headers .= 'Return-Path: billing@globalassessmentsinc.com' . "\r\n";
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
    
                                    // TEMPLATE TAGS
                                    $message = str_replace('$firstname', $psychologist->firstname, $message);
                                    $message = str_replace('$lastname', $psychologist->lastname, $message);
                                    
                        			mail($addr, $sub, $message, $headers, "-fbilling@globalassessmentsinc.com");

                                    // Create Email Record of this email
                                    $record = new EmailRecord();
                                    $record->tstamp = time();
                                    $record->email_type = 'alert_final';
                                    $record->email_recipient = $psychologist->id;
                                    $record->email_subject = $sub;
                                    $record->email_body = $message;
                                    $record->save();
                                    
                                }
                                
                            }
                        }
                        $alert_email->final_last_sent = time();
                        $alert_email->save();
                    }
                }
            }




        }
    }

    // Close our log file
    fclose($log);
