<?php

    use Contao\Config;
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }

    $myfile = fopen("logs/psy_work_form_hide_assignment_".$_POST['assignment_id'] . "_" .date('m_d_Y_H:i:s').".txt", "w") or die("Unable to open file!");

    $assignment_id = str_replace("assignment_", "", $_POST['assignment_id']);
    $psy_id = $_POST['psy_id'];
    $cutoff_date = date('m/d/y', $_POST['cutoff_date']);
    

    fwrite($myfile, "Assignment ID: " . $assignment_id . "\r\n");
    fwrite($myfile, "Psy ID: " . $psy_id . "\r\n");
    
    // Validate Assignment is from previous years
    
    $valid_year = false;
    $query_assignment =  "SELECT * FROM tl_assignment WHERE id='$assignment_id'";
    $result_assignment = $dbh->query($query_assignment);
    if($result_assignment) {
        while($db_assignment = $result_assignment->fetch_assoc()) {
            $assignment_date = date('m/d/y', $db_assignment['date_created']);

            fwrite($myfile, "Assignment Date: " . $db_assignment['date_created'] . "\r\n");
            fwrite($myfile, "Cutoff Date: " . $_POST['cutoff_date'] . "\r\n");
            
            $date_1 = \DateTime::createFromFormat('m/d/y', $assignment_date);
            $date_2 = \DateTime::createFromFormat('m/d/y', $cutoff_date);
            
            if($date_1 < $date_2) {
                $valid_year = true;
            }
        }
    }
    
    if($valid_year) {
        
        fwrite($myfile, "Is from previous year! \r\n");
        
        // Get our Member
        fwrite($myfile, "Getting Member... \r\n");
        $member = [];
        $query_member =  "SELECT * FROM tl_member WHERE id='$psy_id'";
        $result_member = $dbh->query($query_member);
        if($result_member) {
            while($db_member = $result_member->fetch_assoc()) {
                $member = $db_member;
            }
        }
        fwrite($myfile, "Member Name: " . $member['firstname'] . " " . $member['lastname'] . "\r\n");
        
        // add our ID to the list
        $hidden_assignments = [];
        if($member['pwf_hidden_assignments']) {
            $hidden_assignments = unserialize($member['pwf_hidden_assignments']);
        }
        $hidden_assignments[] = $assignment_id;
        
        fwrite($myfile, "Adding ID to list of hidden Assignments... \r\n");
        
        $update =  "update tl_member set pwf_hidden_assignments='".serialize($hidden_assignments)."' WHERE id='".$psy_id."'";
        $result_update = $dbh->query($update);
        
        fwrite($myfile, "Updating Member... \r\n");
    
        echo "ID added to Member's hidden Assignments list";
    }

