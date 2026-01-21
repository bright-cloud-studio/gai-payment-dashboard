<?php
    
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
    for ($i = 1; $i <= $month; $i++) {
        $month_labels[] = date('M', mktime(0, 0, 0, $i, 1));
    }
    
    // Get passed in year if there is one, otherwise use the current year
    $year = date('y');
    if(isset($_GET['year']))
        $year = $_GET['year'];
    
    // Get all Services
    $services = array();
    $s_q = "SELECT * FROM tl_service";
    $s_r = $dbh->query($s_q);
    if($s_r) {
        while($s = $s_r->fetch_assoc()) {
            $services[$s['service_code']] = $s['name'];
        }
    }
    
    // Get all Assignments for the selected Year
    $assignments = array();
    $a_q = "SELECT * FROM tl_assignment WHERE RIGHT(date_created, 2) = '".$year."';";
    $a_r = $dbh->query($a_q);
    if($a_r) {
        while($a = $a_r->fetch_assoc()) {
            $month = date('M', strtotime($a['date_created']));
            $assignments[$a['type_of_testing']][$month] += 1;
            //$assignments[$a['type_of_testing']][$month]['service_name'] = $services[$a['type_of_testing']];
        }
        
    }



    echo "<pre>";
    print_r($assignments);
    echo "</pre>";




    $datasets = [];
    for($a = 0; $a != 2; $a++) {
        $datasets[] = [
            'label'           => "Test" . $a,
            'type'            => "bar",
            'data'            => [12], // Note: You'll likely want to replace 12 with actual data
            'backgroundColor' => "rgba(52, 152, 219, 0.7)",
            'yAxisID'         => "yCount"
        ];
    }
    

    


    // Return assembled data
    echo json_encode([
        'labels' => $month_labels,
        'datasets' =>
            $datasets
    ]);
    
    
