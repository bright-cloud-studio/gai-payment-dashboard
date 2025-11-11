<?php
    
    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }

    // If our psy_id was actually passed to us
    if(isset($_POST['psy_id'])) {
    
        // Get our variables
        $tstamp = time();
        $date_reviewed = time();
        $date_month = strtolower(date('F'));
        $date_year = date('Y');
        $psy_id = $_POST['psy_id'];
        
        // Get number of Assignments for this Psy
        $total_assignments = 0;
        $a_q =  "SELECT * FROM tl_assignment WHERE psychologist='".$psy_id."'";
        $a_r = $dbh->query($a_q);
        if($a_r){
            while($a = $a_r->fetch_assoc()) {
                $total_assignments++;
            }
        }
        
        // Get the total number of Transactions for this Psy
        $transactions_total = 0;
        $t_q =  "SELECT * FROM tl_transaction WHERE psychologist='".$psy_id."'";
        $t_r = $dbh->query($t_q);
        if($t_r){
            while($t = $t_r->fetch_assoc()) {
                $transactions_total++;
            }
        }
        
        // Get the total number of reviewed Transactions for this Psy
        $transactions_total_reviewed = 0;
        $tr_q =  "SELECT * FROM tl_transaction WHERE psychologist='".$psy_id."' AND published='1'";
        $tr_r = $dbh->query($tr_q);
        if($tr_r){
            while($tr = $tr_r->fetch_assoc()) {
                $transactions_total_reviewed++;
            }
        }
        
        // Get the percentage of reviewed Transactions
        $transactions_percentage_reviewed = ($transactions_total_reviewed / $transactions_total) * 100;
        
        // Get the total number of Misc. Transactions for this Psy
        $misc_transactions_total = 0;
        $mt_q =  "SELECT * FROM tl_transaction_misc WHERE psychologist='".$psy_id."'";
        $mt_r = $dbh->query($mt_q);
        if($mt_r){
            while($mt = $mt_r->fetch_assoc()) {
                $misc_transactions_total++;
            }
        }
        
        // Get the total number of reviewed Transactions for this Psy
        $misc_transactions_total_reviewed = 0;
        $mtr_q =  "SELECT * FROM tl_transaction_misc WHERE psychologist='".$psy_id."' AND published='1'";
        $mtr_r = $dbh->query($mtr_q);
        if($mtr_r){
            while($mtr = $mtr_r->fetch_assoc()) {
                $misc_transactions_total_reviewed++;
            }
        }
        
        // Get the percentage of reviewed Misc. Transactions
        $misc_transactions_percentage_reviewed = ($misc_transactions_total_reviewed / $misc_transactions_total) * 100;

        $query = "INSERT INTO tl_review_record (tstamp, psychologist, total_assignments, transactions_total, transactions_total_reviewed, transactions_percentage_reviewed, misc_transactions_total, misc_transactions_total_reviewed, misc_transactions_percentage_reviewed, date_month, date_year, date_reviewed)
                    VALUES ($tstamp, $psy_id, $total_assignments, $transactions_total, $transactions_total_reviewed, $transactions_percentage_reviewed, $misc_transactions_total, $misc_transactions_total_reviewed, $misc_transactions_percentage_reviewed, '$date_month', $date_year, $date_reviewed)";
        $result = $dbh->query($query);
        
        echo "pass";
        
    }
