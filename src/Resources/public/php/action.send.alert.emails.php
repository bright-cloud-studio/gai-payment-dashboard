<?php

    //$debug_mode = true;
    define('DEBUG_MODE', true);
    define('DEBUG_FILE', fopen($_SERVER['DOCUMENT_ROOT'] . '/../logs/alert_emails_'.date('m_d_y').'.txt', "a+"));

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
    
    // Get Today's date and current hour
    $today = date('m_d_y', time());
    $hour = date("H");
    
    debug("[Current Day: " . $today ."]");
    debug("[Current Hour: " . $hour ."]");
    debug("\n--------------------------------------------");

    // Loop through all Alert emails
    $alert_emails = AlertEmail::findAll();
    if($alert_emails) {
        foreach ($alert_emails as $alert_email) {
            
            debug("\n[Alert Email: " . $alert_email->id . "] [Month: " . $alert_email->month . "]");
            
            $week_remaining_date = date('m_d_y', $alert_email->warning_date);
            $week_remaining_last_sent = 0;
            if($alert_email->warning_last_sent)
                $week_remaining_last_sent = date('m_d_y', $alert_email->warning_last_sent);
            
            debug("[Week Remaining: " . $week_remaining_date ."]");
            debug("[Week Remaining Last Sent: " . $week_remaining_last_sent ."]");

            // EMAIL - Week Remaining
            if($week_remaining_date == $today) {
                debug("[Week Remaining] Today is Send Day", 1);

                if($hour == 12) {
                    debug("[Week Remaining] This (".$hour.") is the Send Hour", 2);
                    
                    if($week_remaining_last_sent != $today) {
                        debug("[Week Remaining] This has NOT been sent yet today", 3);

                        $psychologists = MemberModel::findBy('disable', '0');
                        if($psychologists) {
                            foreach ($psychologists as $psychologist) {
                                debug("[Week Remaining] Emailing " . $psychologist->firstname . " " . $psychologist->lastname, 4);
    
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
                    			debug("[Week Remaining] Email sent", 4);
    
                                // Create Email Record of this email
                                $record = new EmailRecord();
                                $record->tstamp = time();
                                $record->date_created = date('m/d/y g:i a');
                                $record->status = 'resolved';
                                $record->email_type = 'alert_week_remaining';
                                $record->email_recipient = $psychologist->id;
                                $record->email_subject = $sub;
                                $record->email_body = $message;
                                $record->save();
                                debug("[Week Remaining] Email Record Created - ID: " . $record->id, 4);
                            }
                        }
                        $alert_email->warning_last_sent = time();
                        $alert_email->save();
                    } else {
                        debug("[Week Remaining] This has ALREADY been sent today", 3);
                    }
                    
                } else {
                    debug("[Week Remaining] This (".$hour.") is NOT the Send Hour", 2);
                    debug("[Current Hour: $hour] [Desired Hour: 12]", 2);
                }

            } else {
                debug("[Week Remaining] Today is NOT Send Day", 1);
            }

            $final_date = date('m_d_y', $alert_email->final_date);
            $final_last_sent = 0;
            if($alert_email->final_last_sent)
                $final_last_sent = date('m_d_y', $alert_email->final_last_sent);
            
            debug("[Final Day: " . $final_date ."]");
            debug("[Final Day Last Sent: " . $final_last_sent ."]");

            // EMAIL - Final Day
            if($final_date == $today) {
                debug("[Final Day] Today is Send Day", 1);
                
                if($hour == 12) {
                    debug("[Final Day] This (".$hour.") is the Send Hour", 2);

                    if($final_last_sent != $today) {
                        debug("[Final Day] This has NOT been sent yet today", 3);

                        $psychologists = MemberModel::findBy('disable', '0');
                        if($psychologists) {
                            foreach ($psychologists as $psychologist) {
                                debug("[Final Day] Emailing " . $psychologist->firstname . " " . $psychologist->lastname, 4);
                                $addr = $psychologist->email;
                    			$headers = "MIME-Version: 1.0" . "\r\n";
                    			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    			$headers .= 'From: billing@globalassessmentsinc.com' . "\r\n";
                                $headers .= 'Return-Path: billing@globalassessmentsinc.com' . "\r\n";
                    			$headers .= 'Cc: ed@globalassessmentsinc.com' . "\r\n";
                    			$sub = $alert_email->final_subject;
                    			$message = "
                    				<html>
                    				<head>
                    				<title>Global Assessments, Inc. - FINAL DAY Reminder</title>
                    				</head>
                    				<body>".
                                        $alert_email->final_body
                                    ."</body>
                    				</html>
                    				";

                                // TEMPLATE TAGS
                                $message = str_replace('$firstname', $psychologist->firstname, $message);
                                $message = str_replace('$lastname', $psychologist->lastname, $message);
                                
                    			mail($addr, $sub, $message, $headers, "-fbilling@globalassessmentsinc.com");
                    			debug("[Final Day] Email sent", 4);
                    			
                                // Create Email Record of this email
                                $record = new EmailRecord();
                                $record->tstamp = time();
                                $record->status = 'resolved';
                                $record->date_created = date('m/d/y g:i a');
                                $record->email_type = 'alert_final';
                                $record->email_recipient = $psychologist->id;
                                $record->email_subject = $sub;
                                $record->email_body = $message;
                                $record->save();
                                debug("[Final Day] Email Record Created - ID: " . $record->id, 4);
                            }
                        }
                        $alert_email->final_last_sent = time();
                        $alert_email->save();
                    } else {
                        debug("[Final Day] This has ALREADY been sent today", 3);
                    }
                } else {
                    debug("[Final Day] This (".$hour.") is NOT the Send Hour", 2);
                    debug("[Current Hour: $hour] [Desired Hour: 12]", 2);
                }
            } else {
                debug("[Final Day] Today is NOT Send Day", 1);
            }

        debug("\n--------------------------------------------");
        }
    }

    // Close our log file
    if(DEBUG_MODE)
        fclose(DEBUG_FILE);
    
    /** Helper Functions **/
    function debug($message, $indent_level = 0) {
        if(DEBUG_MODE) {
            $indent = str_repeat("\t", $indent_level);
            $message = $indent . $message;
            fwrite(DEBUG_FILE, $message . "\n");
            echo $message . "<br>";
            
        } else {
            fwrite(DEBUG_FILE, "DEBUG MODE not active" . "\n");
        }
    }
