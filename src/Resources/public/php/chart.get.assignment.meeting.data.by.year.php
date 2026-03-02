<?php
    // data.php
    header('Content-Type: application/json');
    
    // Initialize Session, start Composer
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Includes
    use Bcs\Model\Assignment;
    use Contao\Config;
    use Contao\MemberBodel;
    
    // Connect to the DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }

    // Generate Month labels
    $month_labels = [];
    $month = date('n');
    if(isset($_GET['month']))
        $month = $_GET['month'];
    for ($i = 1; $i <= $month; $i++) {
        $month_labels[] = date('M', mktime(0, 0, 0, $i, 1));
    }
    
    // Get the Year range we are requesting data for
    $year = date('y');
    if(isset($_GET['year']))
        $year = $_GET['year'];
        
    // Stage Assignment data. Store by: Service Code > Month > Total Usage
    $assignments = array();
    $a_q = "SELECT * FROM tl_assignment WHERE date_created LIKE '%/".$year."' AND published='1'";
    $a_r = $dbh->query($a_q);
    if($a_r) {
        while($a = $a_r->fetch_assoc()) {

            $assignment_month = date('F', strtotime($a['date_created']));
            $assignment_month_number = date('m', strtotime($a['date_created']));
            
            if($assignment_month_number <= $month)
                $assignments[$assignment_month] = ($assignments[$assignment_month] ?? 0) + 1;
        }
    }
    
    // Stage Transaction data
    $t_meeting = array();
    $t_mtg_late_cancel = array();
    
    $t_q = "SELECT * FROM tl_transaction WHERE YEAR(FROM_UNIXTIME(date_submitted)) = '20" . $year . "'";
    $t_r = $dbh->query($t_q);
    if($t_r) {
        while($t = $t_r->fetch_assoc()) {
            $transaction_month = date('F', $t['date_submitted']);
            $transaction_month_number = date('m', $t['date_submitted']);
            
            if($transaction_month_number <= $month) {
                
                if($t['service'] == 1) {
                    $t_meeting[$transaction_month] = ($t_meeting[$transaction_month] ?? 0) + 1;
                } else if($t['service'] == 12) {
                    $t_mtg_late_cancel[$transaction_month] = ($t_mtg_late_cancel[$transaction_month] ?? 0) + 1;
                }
            }
        }
    }
    
    // Stage Misc. Transaction data
    $mt_meeting = array();
    $mt_mtg_late_cancel = array();
    
    $mt_q = "SELECT * FROM tl_transaction_misc WHERE YEAR(FROM_UNIXTIME(date_submitted)) = '20" . $year . "'";
    $mt_r = $dbh->query($mt_q);
    if($t_r) {
        while($mt = $mt_r->fetch_assoc()) {
            $misc_transaction_month = date('F', $mt['date_submitted']);
            $misc_transaction_month_number = date('m', $mt['date_submitted']);
            
            if($misc_transaction_month_number <= $month) {
                
                if($mt['service'] == 1) {
                    $mt_meeting[$misc_transaction_month] = ($mt_meeting[$misc_transaction_month] ?? 0) + 1;
                } else if($mt['service'] == 12) {
                    $mt_mtg_late_cancel[$misc_transaction_month] = ($mt_mtg_late_cancel[$misc_transaction_month] ?? 0) + 1;
                }
            }
        }
    }
    
    uksort($assignments, function($a, $b) {
        $monthA = strtotime($a);
        $monthB = strtotime($b);
        return $monthA - $monthB;
    });
    uksort($t_meeting, function($a, $b) {
        $monthA = strtotime($a);
        $monthB = strtotime($b);
        return $monthA - $monthB;
    });
    uksort($t_mtg_late_cancel, function($a, $b) {
        $monthA = strtotime($a);
        $monthB = strtotime($b);
        return $monthA - $monthB;
    });
    uksort($mt_meeting, function($a, $b) {
        $monthA = strtotime($a);
        $monthB = strtotime($b);
        return $monthA - $monthB;
    });
    uksort($mt_mtg_late_cancel, function($a, $b) {
        $monthA = strtotime($a);
        $monthB = strtotime($b);
        return $monthA - $monthB;
    });
    
    // Sample logic to simulate database results
    $data[$year]['assignments'] = $assignments;
    $data[$year]['transactions_meetings'] = $t_meeting;
    $data[$year]['transactions_mtg_late_cancels'] = $t_mtg_late_cancel;
    $data[$year]['misc_transactions_meetings'] = $mt_meeting;
    $data[$year]['misc_transactions_mtg_late_cancels'] = $mt_mtg_late_cancel;
    
    // Return the data for the requested year, or a default if not found
    echo json_encode($data[$year]);
?>
