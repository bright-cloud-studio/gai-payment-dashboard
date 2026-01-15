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
    
    
    // Get passed in year if there is one, otherwise use the current year
    $year = date('y');
    if(isset($_GET['year']))
        $year = $_GET['year'];
    
    
    // Get all Districts
    $assignments = array();
    //$a_q =  "SELECT * FROM tl_assignment WHERE published='1'";
    $a_q = "SELECT * FROM tl_assignment WHERE RIGHT(date_created, 2) = '".$year."';";
    $a_r = $dbh->query($a_q);
    if($a_r) {
        while($a = $a_r->fetch_assoc()) {
            $assignments[$a['id']] = $a['date_created'];
            //echo "Assignment ID: ". $a['id'] ."<br>";
            //echo "Date Created: ". $a['date_created'] ."<br><br>";
        }
    }
    
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    echo json_encode([
        'labels' => $months,
        'datasets' => [
            [
                'label' => 'Meetings',
                'type' => 'bar',
                'data' => [12, 15, 11, 17, 14, 13, 16, 18, 15, 20, 21, 23],
                'backgroundColor' => 'rgba(52, 152, 219, 0.7)',
                'yAxisID' => 'yCount'
            ],
            [
                'label' => 'Psych Eval',
                'type' => 'bar',
                'data' => [10, 11, 9, 12, 13, 13, 14, 14, 13, 15, 16, 17],
                'backgroundColor' => 'rgba(46, 204, 113, 0.7)',
                'yAxisID' => 'yCount'
            ],
            [
                'label' => 'Achvmnt',
                'type' => 'bar',
                'data' => [8, 9, 7, 11, 10, 9, 12, 11, 10, 13, 14, 15],
                'backgroundColor' => 'rgba(155, 89, 182, 0.7)',
                'yAxisID' => 'yCount'
            ],
            [
                'label' => 'Parking',
                'type' => 'bar',
                'data' => [20, 22, 18, 25, 23, 21, 24, 26, 22, 28, 30, 31],
                'backgroundColor' => 'rgba(241, 196, 15, 0.7)',
                'yAxisID' => 'yCount'
            ],
            [
                'label' => 'Editing',
                'type' => 'bar',
                'data' => [6, 7, 6, 8, 7, 8, 9, 9, 8, 10, 11, 12],
                'backgroundColor' => 'rgba(231, 76, 60, 0.7)',
                'yAxisID' => 'yCount'
            ]
        ]
    ]);
    
    // Return data
