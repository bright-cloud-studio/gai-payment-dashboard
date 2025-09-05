<?php

    use Bcs\Model\Assignment;
    use Bcs\Model\District;
    use Bcs\Model\School;
    use Contao\Config;
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
                        $district = District::findOneBy('id', $assignment->district);
                        $school = School::findOneBy('id', $assignment->school);
                        
                        //$addr = $psychologist->email;
                        $addr = 'mark@brightcloudstudio.com';
            			$headers = "MIME-Version: 1.0" . "\r\n";
            			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            			$headers .= 'From: <billing@globalassessmentsinc.com>' . "\r\n";
            			$headers .= 'Cc: ed@globalassessmentsinc.com' . "\r\n";
            			$sub = Config::get('pwf_notice_30_day_subject'); 
            			$message = "
            				<html>
            				<head>
            				<title>Global Assessments, Inc. - Psych Work Form - Notice - 30 Day</title>
            				</head>
            				<body>".
                                Config::get('pwf_notice_30_day_body')
                            ."</body>
            				</html>
            				";

                        // TEMPLATE TAGS - Psychologist
                        $message = str_replace('$firstname', $psychologist->firstname, $message);
                        $message = str_replace('$lastname', $psychologist->lastname, $message);
                        // TEMPLATE TAGS - Assignment
                        $message = str_replace('$date_created', $assignment->date_created, $message);
                        $message = str_replace('$date_30_day', $assignment->date_30_day, $message);
                        // TEMPLATE TAGS - District
                        $message = str_replace('$district', $distruct->district_name, $message);
                        // TEMPLATE TAGS - School
                        $message = str_replace('$district', $school->school_name, $message);
                        // TEMPLATE TAGS - DEV
                        $message = str_replace('$psy_email', $psychologist->email, $message);
                        
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
