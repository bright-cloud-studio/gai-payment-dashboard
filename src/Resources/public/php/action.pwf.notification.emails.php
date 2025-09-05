<?php

    use Bcs\Model\Assignment;
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
    $log = fopen($_SERVER['DOCUMENT_ROOT'] . '/../logs/pwf_notification_emails_'.date('m_d_y').'.txt', "a+") or die("Unable to open file!");

    // Loop through all Assignments
    $assignments = Assignment::findAll();
    if($assignments) {
        foreach ($assignments as $assignment) {
            
            if($assignment->date_30_day) {

                // Get Today's, 30-day's and five days before 30-day's dates
                $today = date('m/d/y');
                $date_30_day = $assignment->date_30_day;
                $date_notice_day = date('m/d/y', strtotime('-5 days', strtotime($date_30_day)));
                
                // If today is the same as five days before 30 day
                if($today == $date_notice_day) {
                    
                    // Write in our log, it is Notification Day for this Assignment
                    fwrite($log, "Assignment: ID ". $assignment->id ."\r\n");
                    fwrite($log, "Psychologist: ID ". $assignment->psychologist ."\r\n");
                    fwrite($log, "Today: ". $today ."\r\n");
                    fwrite($log, "30 Day: ". $date_30_day ."\r\n");
                    fwrite($log, "Notice Day: ". $date_notice_day ."\r\n");
                    fwrite($log, "TODAY is 5 days before 30 day! \r\n");
                    
                    // If we have no 'Meeting Date'
                    if(!$assignment->meeting_date) {
                        
                        fwrite($log, "NO MEETING DATE - SEND NOTIFICATION! \r\n");
                        
                        $psychologist = MemberModel::findOneBy('id', $assignment->psychologist);
                        
                        //$addr = $psychologist->email;
                        $addr = 'mark@brightcloudstudio.com';
            			$headers = "MIME-Version: 1.0" . "\r\n";
            			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            			$headers .= 'From: <billing@globalassessmentsinc.com>' . "\r\n";
            			$headers .= 'Cc: ed@globalassessmentsinc.com' . "\r\n";
            			$sub = "[GA INC. Dashboard] Empty 'Meeting Date' for an Assignment notification";
            			$message = "
            				<html>
            				<head>
            				<title>Global Assessments, Inc. - Psych Work Form Notification</title>
            				</head>
            				<body>".
            				
            				"<p>USERNAME,</p>".
            				
            				"<p>You have an Assignment on your Psych Work Form that has no 'Meeting Date' filled in. Here are the details:<p>".
            				
            				"<p>Assignment ID: $assignment->id<br>".
            				"[DEV] Psychologist Email: $psychologist->email".
            				"District: $assignment->district<br>".
            				"School: $assignment->school<br>".
            				"Date Created: $assignment->date_created<br>".
            				"Date 30 Day: $assignment->date_30_day</p>"
            				
                            ."</body>
            				</html>
            				";

                        // TEMPLATE TAGS
                        $message = str_replace('$firstname', $psychologist->firstname, $message);
                        $message = str_replace('$lastname', $psychologist->lastname, $message);
                        
            			mail($addr, $sub, $message, $headers);
                        
                    }
                    
                }
                
                
                fwrite($log, "Meeting Date: ". $assignment->meeting_date ."\r\n");
                fwrite($log, "----------------------------------------------------\r\n\r\n");
            }
            
        }
    }

    // Close our log file
    fclose($log);
