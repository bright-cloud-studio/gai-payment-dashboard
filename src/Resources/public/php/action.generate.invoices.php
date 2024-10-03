<?php

    // Initialize Session, start Composer
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    // Uses
    use Dompdf\Dompdf;
    use Dompdf\Options;
    
    
    
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
    
    // Connect to the DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }

    
    // Step One
    // Find our oldest unfinished Invoice Request
    $request_query =  "SELECT * FROM tl_invoice_request WHERE generation_completed='no' ORDER BY id ASC";
    $request_result = $dbh->query($request_query);
    if($request_result) {
        while($row = $request_result->fetch_assoc()) {
            
            $request = $row['id'];


            // Step Two
            // Find Invoices without a PDF link
            $invoice_query =  "SELECT * FROM tl_invoice WHERE pid='$request' AND invoice_url IS NULL ORDER BY id ASC";
            $invoice_result = $dbh->query($invoice_query);
            if($invoice_result) {
                while($db_inv = $invoice_result->fetch_assoc()) {
                    
                    // Step Three
                    // Generate a folder for this Psy if it doesn't exist already
                    $addr_folder = $_SERVER['DOCUMENT_ROOT'] . '/../files/invoices/' . cleanName($db_inv['psychologist_name']);
                    $filename = "invoice_" . date('y_m');
                    if (!file_exists($addr_folder)) {
                        mkdir($addr_folder, 0777, true);
                    }
                    
                    generateInvoice($dompdf, $addr_folder, $filename);
                    
                    // Step Four
                    // Generate Our Invoice!
                    
                    
                    
                    
                    
                    
                }
            }
            
            
            
            
            
        }
    }
    
    
    function generateInvoice($dompdf, $addr_folder, $filename) {
        
        
        /*******************/
    	/* TEMPLATE STUFFS */
    	/*******************/
      	
        // Load our HTML template
        $html = file_get_contents('bundles/bcspaymentdashboard/templates/invoice_psy.html', true);
    
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
    		    
    		    // If the first part of our exploded tag is "product" we are looking for an attribute
    		    case 'product':
    		        
    		        // Get the product attribute that is based on our second half of the tag
    		        // This thing is super powerful. Trying to reference the class data typically required knowing the name ahead of time
    		        // But now this way by closing our variable in "{ }" brackets it acts as if it's a fixed name, so we can get any product
    		        // data without having to write each tag manually. Just put in a products attribute name in the template and we will get
    		        // back the correct data. Super happy with this, I did not know it was possible.
    		        switch($explodedTag[1]) {
    		            case 'name':
    		                $html = str_replace($tag, $product_data->{$explodedTag[1]}, $html);
    		                break;
    		            case 'description':
    		                $html = str_replace($tag, $product_data->{$explodedTag[1]}, $html);
    		                break;
    		            default:
    		                
    		                $title = ucwords($explodedTag[1]);
    		                $title = str_replace("_"," ",$title);
    		                
    		                $buffer = '';
    		                $buffer .= "<div class='attribute " . $explodedTag[1] . "'>";
    		                $buffer .= "<h3>" . $title . "</h3>";
    		                $buffer .= $product_data->{$explodedTag[1]};
    		                $buffer .= "</div>";
    		                
    		                $html = str_replace($tag, $buffer, $html);
    		                
    		                break;
    		        }
    		        
    		    
    		    break;
    		    
    		    //colors
    		    case 'options':
    		        
    		        $buffer = '';
    	            foreach($product_options as $thing) {
                        $buffer .= $thing[$explodedTag[1]];
                    }
                    $html = str_replace($tag, $buffer, $html);
    		        
    		        break;
    		        
    		    case 'year':
    		        $buffer = date('Y');
                    $html = str_replace($tag, $buffer, $html);
    		        
    		        break;
    		        
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
    	//$dompdf->loadHtml('hello world');
    	
    	// Set our paper size and orientation
    	$dompdf->setPaper('A4', 'portrait');
    	
    	// Render our PDF using the loaded HTML
    	$dompdf->render();
    	
    	$output = $dompdf->output();
        file_put_contents($addr_folder . '/' . $filename . '.pdf', $output);
        //file_put_contents($file_addr . 'LOG_' . time() . '.html', $html);
        
        
        
        
    }

    function cleanName($name) {
        // Remove special characters using regular expression
        $name = preg_replace('/[^a-zA-Z0-9 ]/', '', $name);
    
        // Convert to lowercase
        $name = strtolower($name);
    
        // Replace spaces with underscores
        $name = str_replace(' ', '_', $name);
    
        return $name;
    }
