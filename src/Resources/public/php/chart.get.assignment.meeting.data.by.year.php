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
                $assignments[0][$assignment_month] = ($assignments[0][$assignment_month] ?? 0) + 1;
        }
    }
    
    // Stage Transaction data. Store by: Service Code > Month > Total Usage
    $transactions = array();
    
    $t_q = "SELECT * FROM tl_transaction 
        WHERE YEAR(FROM_UNIXTIME(date_submitted)) = '20" . $year . "' 
        AND service='1' 
        AND published='1'";
    $t_r = $dbh->query($t_q);
    if($t_r) {
        while($t = $t_r->fetch_assoc()) {
            $transaction_month = date('F', $t['date_submitted']);
            $transaction_month_number = date('m', $t['date_submitted']);
            
            if($transaction_month_number <= $month)
                $transactions[0][$transaction_month] = ($transactions[0][$transaction_month] ?? 0) + 1;
        }
    }
    
    
    // Sample logic to simulate database results
    
    $data[$year]['assignments'] = $assignments[0];
    $data[$year]['meetings'] = $transactions[0];
    
    /*
    $data = [
        '26' => [
            'assignments' => [102, 19, 3, 5, 2, 3, 10, 15, 8, 12, 14, 9],
            'meetings' => [8, 12, 5, 8, 4, 6, 12, 10, 7, 9, 11, 7]
        ],
        '25' => [
            'assignments' => [15, 22, 10, 12, 8, 14, 18, 20, 15, 18, 22, 19],
            'meetings' => [10, 15, 8, 10, 6, 10, 14, 15, 12, 13, 16, 14]
        ],
        '24' => [
            'assignments' => [99, 22, 10, 12, 8, 14, 18, 20, 15, 18, 22, 19],
            'meetings' => [44, 15, 8, 10, 6, 10, 14, 15, 12, 13, 16, 14]
        ]
    ];
    */

    
    
    // Return the data for the requested year, or a default if not found
    echo json_encode($data[$year]);
?>
