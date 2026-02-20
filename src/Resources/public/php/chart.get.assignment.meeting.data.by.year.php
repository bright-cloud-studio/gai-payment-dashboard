<?php
// data.php
header('Content-Type: application/json');

// In a real scenario, you'd fetch this from a database based on $_GET['year']
$year = isset($_GET['year']) ? $_GET['year'] : '2023';


// Sample logic to simulate database results
$data = [
    '2023' => [
        'assignments' => [12, 19, 3, 5, 2, 3, 10, 15, 8, 12, 14, 9],
        'meetings' => [8, 12, 5, 8, 4, 6, 12, 10, 7, 9, 11, 7]
    ],
    '2024' => [
        'assignments' => [15, 22, 10, 12, 8, 14, 18, 20, 15, 18, 22, 19],
        'meetings' => [10, 15, 8, 10, 6, 10, 14, 15, 12, 13, 16, 14]
    ]
];

// Return the data for the requested year, or a default if not found
echo json_encode($data[$year] ?? $data['2023']);
?>
