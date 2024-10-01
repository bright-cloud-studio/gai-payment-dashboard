<?php

    // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

    use Dompdf\Dompdf;
    use Dompdf\Options;
    
    // Connect to DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
    
    //$update =  "INSERT INTO tl_invoice_request (date_start, date_end) VALUES ('123', '456')";
    //$result_update = $dbh->query($update);



    /********************/
	/* INITALIZE STUFFS */
	/********************/
	
    // Create our Dompdf Options class
    $options = new Options();
    // Set our options
    $options->set("defaultFont", "Helvetica");
    $options->set("isRemoteEnabled", "true");

	// Initialize Dompdf using our just set up Options
	$dompdf = new Dompdf($options);




    /*******************/
	/* TEMPLATE STUFFS */
	/*******************/
  	
    // Load our HTML template
    $html = file_get_contents('/../templates/invoice_psy.html', true);
    
    
    
    
    /***********************/
	/* GENERATE PDF STUFFS */
	/***********************/
	
    // Load our HTML into dompdf
	$dompdf->loadHtml($html);
	//$dompdf->loadHtml('hello world');
	
	// Set our paper size and orientation
	$dompdf->setPaper('A4', 'portrait');
	
	// Render our PDF using the loaded HTML
	$dompdf->render();
	
	$output = $dompdf->output();
    $file_addr = $_SERVER['DOCUMENT_ROOT'] . '/../files/invoices/';
    //file_put_contents($file_addr . 'Test_' . time() . '.pdf', $output);
    file_put_contents($file_addr . 'LOG_' . time() . '.html', $html);

