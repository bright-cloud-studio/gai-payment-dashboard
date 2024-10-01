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
    $html = file_get_contents('../templates/invoice_psy.html', true);
    
    // Replace our tags with the proper values
    //$html = str_replace("{{img_src}}", $img_src, $html);
    
    // Find all instances of our tag brackets '{{tag}}' and store them in the $tags array
    preg_match_all('/\{{2}(.*?)\}{2}/is', $html, $tags);
    
    // Loop through those tags and replace them with the correct product data
    foreach($tags[0] as $tag) {
        
        // Remove brackets from our tag
        $cleanTag = str_replace("{{","",$tag);
        $cleanTag = str_replace("}}","",$cleanTag);
        
        // Explode our tag into two parts
	    $explodedTag = explode("::", $cleanTag);
	    
	    // Do different things based on the first part of our tag
	    switch($explodedTag[0]) {

		    case 'site_url':
		        $buffer = "https://" . $_SERVER['SERVER_NAME'];
                $html = str_replace($tag, $buffer, $html);
		        
		        break;
		        
		  
	    }
        
    }
    
    
    /***********************/
	/* GENERATE PDF STUFFS */
	/***********************/
	
    // Load our HTML into dompdf
	$dompdf->loadHtml($html);
	
	// Set our paper size and orientation
	$dompdf->setPaper('A4', 'portrait');
	
	// Render our PDF using the loaded HTML
	$dompdf->render();
	
	$output = $dompdf->output();
    $file_addr = $_SERVER['DOCUMENT_ROOT'] . '/../files/invoices/';
    file_put_contents($file_addr . 'Test.pdf', $output);

