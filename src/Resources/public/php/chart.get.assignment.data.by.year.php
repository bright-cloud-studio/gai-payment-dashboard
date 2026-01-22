<?php
    
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
    $month = 12;
    for ($i = 1; $i <= $month; $i++) {
        $month_labels[] = date('M', mktime(0, 0, 0, $i, 1));
    }
    
    // Get the Year range we are requesting data for
    $year = date('y');
    $year = 25;
    
    // Get all Services
    $services = array();
    $s_q = "SELECT * FROM tl_service";
    $s_r = $dbh->query($s_q);
    if($s_r) {
        while($s = $s_r->fetch_assoc()) {
            $services[$s['service_code']] = $s['name'];
        }
    }
    
    // Stage Assignment data. Store by: Service Code > Month > Total Usage
    $assignments = array();
    $a_q = "SELECT * FROM tl_assignment WHERE RIGHT(date_created, 2) = '".$year."';";
    $a_r = $dbh->query($a_q);
    if($a_r) {
        while($a = $a_r->fetch_assoc()) {
            $month = date('M', strtotime($a['date_created']));
            $assignments[$a['type_of_testing']][$month] += 1;
        }
    }
    
    
    $datasets = [];
    // Loop through staged Assignment data and stage Chartjs data
    foreach($assignments as $service_code => $months) {
        
        // Build array of totals by month
        $totals_by_month = [];
        foreach($months as $month => $total_usage) {
            $totals_by_month[] = $total_usage;
        }
        
        $datasets[] = [
            'label'           => $services[$service_code],
            'type'            => "bar",
            'data'            => $totals_by_month,
            'backgroundColor' => "rgba(".rand(1, 255).",".rand(1, 255).",".rand(1, 255).", 0.7)",
            'yAxisID'         => "yCount"
        ];
        
    }
    
    // Spit out encoded json data
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'labels' => $month_labels,
        'datasets' =>
            $datasets
    ]);
