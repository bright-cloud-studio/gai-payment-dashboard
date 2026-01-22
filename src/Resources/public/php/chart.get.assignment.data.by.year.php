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
    for ($i = 1; $i <= $month; $i++) {
        $month_labels[] = date('M', mktime(0, 0, 0, $i, 1));
    }
    
    // Get the Year range we are requesting data for
    $year = date('y');
    
    // Get all Services
    $services = array();
    $s_q = "SELECT * FROM tl_service";
    $s_r = $dbh->query($s_q);
    if($s_r) {
        while($s = $s_r->fetch_assoc()) {
            $services[$s['service_code']]['service_name'] = $s['name'];
            $services[$s['service_code']]['graph_color'] = $s['graph_color'];
        }
    }
    
    
    // Stage Assignment data. Store by: Service Code > Month > Total Usage
    $assignments = array();
    $a_q = "SELECT * FROM tl_assignment WHERE date_created LIKE '%/".$year."' AND published='1'";
    $a_r = $dbh->query($a_q);
    if($a_r) {
        while($a = $a_r->fetch_assoc()) {

            $month = date('M', strtotime($a['date_created']));
            $assignments[$a['type_of_testing']][$month] = ($assignments[$a['type_of_testing']][$month] ?? 0) + 1;
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
        
        $label = "N/A";
        if($service_code != 0)
            $label = $services[$service_code]['service_name'];
        
        $datasets[] = [
            'label'           => $label,
            'type'            => "bar",
            'data'            => $totals_by_month,
            'backgroundColor' => hex2rgba($services[$service_code]['graph_color'], 0.7),
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

    /* Convert our hexidecimal color to RGBA */
    function hex2rgba($hex, $alpha = 1.0) {
        $hex = str_replace("#", "", $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return "rgba($r, $g, $b, $alpha)";
    }
