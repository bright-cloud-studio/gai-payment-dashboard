<?php

    // Initialize Session, start Composer
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Uses
    use Dompdf\Dompdf;
    use Dompdf\Options;
    
    // Connect to the DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
    
    
    
    
    // Step One
    
    // Find our oldest unfinished Invoice Request
    $request_query =  "SELECT * FROM tl_invoice_request WHERE generation_completed='no' SORT BY id ASC";
    $request_result = $dbh->query($request_query);
    if($request_result) {
        while($row = $request_result->fetch_assoc()) {
            
            $request = $row['id'];
            
            
            // Step Two
            
            // Find Invoices without a PDF link
            $invoice_query =  "SELECT * FROM tl_invoice WHERE pid='$request' AND invoice_url='' SORT BY id ASC";
            $invoice_result = $dbh->query($invoice_query);
            if($invoice_result) {
                while($row = $invoice_result->fetch_assoc()) {
                    
                    
                    
                    // Step Three
                    
                    // Generate a folder for this Psy if it doesn't exist already
                    if (!file_exists('path/to/directory')) {
                        mkdir('path/to/directory', 0777, true);
                    }
                    
                    
                    
                }
            }
            
            
            
            
            
        }
    }
    
    
    
    
    
    
    
    
    
    
    /*
    $update =  "INSERT INTO tl_invoice_request (date_start, date_end) VALUES ('123', '456')";
    $result_update = $dbh->query($update);
    */
