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
	
	// Connect to the DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
	
    // Create our Dompdf Options class
    $options = new Options();
    // Set our options
    $options->set("defaultFont", "Helvetica");
    $options->set("isRemoteEnabled", "true");
    
    // Get all Districts
    $districts = array();
    $d_q =  "SELECT * FROM tl_district WHERE published='1'";
    $d_r = $dbh->query($d_q);
    if($d_r) {
        while($r = $d_r->fetch_assoc()) {
            $districts[$r['id']] = $r['district_name'];
        }
    }
    
    // Get all Schools
    $schools = array();
    $s_q =  "SELECT * FROM tl_school WHERE published='1'";
    $s_r = $dbh->query($s_q);
    if($s_r) {
        while($r = $s_r->fetch_assoc()) {
            $schools[$r['id']] = $r['school_name'];
        }
    }
    
    // Get all Students
    $students = array();
    $st_q =  "SELECT * FROM tl_student WHERE published='1'";
    $st_r = $dbh->query($st_q);
    if($st_r) {
        while($r = $st_r->fetch_assoc()) {
            $students[$r['id']]['name'] = getInitials($r['name']);
            
            if($r['lasid'] != '' && $r['sasid'] == '')
                $students[$r['id']]['number'] = $r['lasid'];
            if($r['lasid'] == '' && $r['sasid'] != '')
                $students[$r['id']]['number'] = $r['sasid'];
        }
    }
    
    // Get all Services
    $services = array();
    $se_q =  "SELECT * FROM tl_service WHERE published='1'";
    $se_r = $dbh->query($se_q);
    if($se_r) {
        while($r = $se_r->fetch_assoc()) {
            $services[$r['id']] = $r['name'];
        }
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
            //$invoice_query =  "SELECT * FROM tl_invoice WHERE pid='$request' AND invoice_url IS NULL ORDER BY id ASC";
            $invoice_query =  "SELECT * FROM tl_invoice WHERE pid='$request' ORDER BY id ASC";
            $invoice_result = $dbh->query($invoice_query);
            if($invoice_result) {
                while($db_inv = $invoice_result->fetch_assoc()) {
                    
                    // Step Three
                    // Generate a folder for this Psy if it doesn't exist already
                    $addr_folder = $_SERVER['DOCUMENT_ROOT'] . '/../files/invoices/psychologists/' . cleanName($db_inv['psychologist_name']);
                    $filename = "invoice_" . date('y_m');
                    if (!file_exists($addr_folder)) {
                        mkdir($addr_folder, 0777, true);
                    }
                    
                    $invoice_id = $db_inv['id'];
                    $psy_id = $db_inv['psychologist'];
                    $transaction_ids = $db_inv['transaction_ids'];
                    $misc_transaction_ids = $db_inv['misc_transaction_ids'];
                    
                    $invoice_folder = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/files/invoices/psychologists/' . cleanName($db_inv['psychologist_name']) . '/';
                    generateInvoice($dbh, $invoice_id, $psy_id, $invoice_folder, $addr_folder, $filename, $transaction_ids, $misc_transaction_ids, $districts, $schools, $students, $services);
                    
                    // Step Four
                    // Generate Our Invoice!
                    
                    
                    
                    
                    
                    
                }
            }
            
            
            
            
            
        }
    }
    
    
    function generateInvoice($dbh, $invoice_id, $psy_id, $invoice_folder, $addr_folder, $filename, $transaction_ids, $misc_transaction_ids, $districts, $schools, $students, $services) {
        
        
        $price_total = 0.00;
        
        // Initialize Dompdf using our just set up Options
	    $dompdf = new Dompdf($options);
        
        /*******************/
    	/* TEMPLATE STUFFS */
    	/*******************/
    	
    	$psy_query =  "SELECT * FROM tl_member WHERE id='$psy_id'";
        $psy_result = $dbh->query($psy_query);
        $psy = [];
        if($psy_result) {
            while($row = $psy_result->fetch_assoc()) {
                $psy['name'] = $row['firstname'] . " " . $row['lastname'];
                $psy['addr_1'] = $row['street'];
                $psy['addr_2'] = $row['city'] . ", " . $row['state'] . " " . $row['postal'];
            }
        }
    	
      	
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
    		    case 'psychologist':
    		        

    		        switch($explodedTag[1]) {
    		            case 'name':
    		                $html = str_replace($tag, $psy['name'], $html);
    		                break;
    		            case 'date_issued':
    		                $html = str_replace($tag, date('m/d/y'), $html);
    		                break;
    		            case 'invoice_number':
    		                $html = str_replace($tag, date('Y_m'), $html);
    		                break;
    		            case 'addr_1':
    		                $html = str_replace($tag, $psy['addr_1'], $html);
    		                break;
    		            case 'addr_2':
    		                $html = str_replace($tag, $psy['addr_2'], $html);
    		                break;
    		            default:
    		                break;
    		        }
    		        
    		    
    		    break;
    		    
    		    //colors
    		    case 'invoice':
    		        
    		        switch($explodedTag[1]) {
    		            case 'transactions':
    		                
    		                $trans_html = '';
    		                $misc_trans_html = '';
    		                
    		                if($transaction_ids != '') {
        		                $transactions = array();
        		                $transactions_query =  "SELECT * FROM tl_transaction WHERE id IN (". $transaction_ids .") ORDER BY date_submitted ASC";
                                $transactions_results = $dbh->query($transactions_query);
                                if($transactions_results) {
                                    $i = 0;
                                    while($row = $transactions_results->fetch_assoc()) {
                                        
                                        // Get the Assignment for this Transaction
                                        $a_district = '';
                                        $a_school = '';
                                        $a_student = '';
                                        $a_q =  "SELECT * FROM tl_assignment WHERE id='".$row['pid']."'";
                                        $a_r = $dbh->query($a_q);
                                        if($a_r) {
                                            while($a = $a_r->fetch_assoc()) {
                                                $a_district = $a['district'];
                                                $a_school = $a['school'];
                                                $a_student = $a['student'];
                                            }
                                        }
                                        
                                        $transactions[$i]['district'] = $districts[$a_district];
                                        $transactions[$i]['school'] = $schools[$a_school];
                                        $transactions[$i]['student'] = $students[$a_student]['name'];
                                        
                                        $transactions[$i]['number'] = $students[$a_student]['number'];
                                        
                                        
                                        
                                        if($row['service'] == 1) {
                                            $transactions[$i]['service'] = $services[$row['service']] . ' ('.$row['meeting_duration'].' mins)';
                                            
                                            $dur = ceil(intval($row['meeting_duration']) / 60);
                                            $final_price = $dur * $row['price'];
                                            $transactions[$i]['price'] = number_format(floatval($final_price), 2, '.', '');
                                            $price_total =  number_format(floatval($price_total), 2, '.', '') + number_format(floatval($final_price), 2, '.', '');
                                        } else if($row['service'] == 19) {
                                            $transactions[$i]['service'] = $services[$row['service']];
                                            $final_price = $row['meeting_duration'] * 0.50;
                                            $transactions[$i]['price'] = number_format(floatval($final_price), 2, '.', '');
                                            $price_total =  number_format(floatval($price_total), 2, '.', '') + number_format(floatval($final_price), 2, '.', '');
                                        } else {
                                            $transactions[$i]['service'] = $services[$row['service']];
                                            $transactions[$i]['price'] = $row['price'];
                                            $price_total =  number_format(floatval($price_total), 2, '.', '') + number_format(floatval($row['price']), 2, '.', '');
                                        }
                                        
                                        
                                        $i++;
                                    }
                                }
        		                
        		                foreach($transactions as $transaction) {
        		                    $trans_html .= "<tr>";
            		                $trans_html .= "<td>" . $transaction['district'] . "</td>";
            		                $trans_html .= "<td>" . $transaction['school'] . "</td>";
            		                $trans_html .= "<td>" . $transaction['student'] . "</td>";
            		                $trans_html .= "<td>" . $transaction['number'] . "</td>";
            		                $trans_html .= "<td>" . $transaction['service'] . "</td>";
            		                $trans_html .= "<td>" . $transaction['price'] . "</td>";
            		                $trans_html .= "</tr>";
        		                }
    		                }
    		                
    		                
    		                if($misc_transaction_ids != '') {
        		                $transactions = array();
        		                $transactions_query =  "SELECT * FROM tl_transaction_misc WHERE id IN (". $misc_transaction_ids .") ORDER BY date_submitted ASC";
                                $transactions_results = $dbh->query($transactions_query);
                                if($transactions_results) {
                                    $i = 0;
                                    while($row = $transactions_results->fetch_assoc()) {
                                        
                                        $transactions[$i]['student'] = $row['student_initials'];
                                        
                                        if($row['lasid'] != '' && $row['sasid'] == '')
                                            $transactions[$i]['number'] = $row['lasid'];
                                        if($row['lasid'] == '' && $row['sasid'] != '')
                                            $transactions[$i]['number'] = $row['sasid'];
                                            
                                        $transactions[$i]['district'] = $districts[$row['district']];
                                        $transactions[$i]['school'] = $schools[$row['school']];
                                       
                                        
                                        if($row['service'] == 1) {
                                            
                                            $transactions[$i]['service'] = $services[$row['service']] . ' ('.$row['meeting_duration'].' mins)';
                                            $dur = ceil(intval($row['meeting_duration']) / 60);
                                            $final_price = $dur * $row['price'];
                                            $transactions[$i]['price'] = number_format(floatval($final_price), 2, '.', '');
                                            $price_total =  number_format(floatval($price_total), 2, '.', '') + number_format(floatval($final_price), 2, '.', '');
                                            
                                        } else if($row['service'] == 19) {
                                            $transactions[$i]['service'] = $services[$row['service']] . ' ('.$row['meeting_duration'].' mins)';
                                            $transactions[$i]['service'] = $services[$row['service']];
                                            $final_price = $row['meeting_duration'] * 0.50;
                                            $transactions[$i]['price'] = number_format(floatval($final_price), 2, '.', '');
                                            $price_total =  number_format(floatval($price_total), 2, '.', '') + number_format(floatval($final_price), 2, '.', '');
                                        } else {
                                            $transactions[$i]['service'] = $services[$row['service']];
                                            $transactions[$i]['price'] = $row['price'];
                                            $price_total = number_format(floatval($price_total), 2, '.', '') + number_format(floatval($row['price']), 2, '.', '');
                                        }
                                        
                                       
                                        $i++;
                                    }
                                }
        		                
        		                foreach($transactions as $transaction) {
        		                    $misc_trans_html .= "<tr>";
            		                $misc_trans_html .= "<td>" . $transaction['district'] . "</td>";
            		                $misc_trans_html .= "<td>" . $transaction['school'] . "</td>";
            		                $misc_trans_html .= "<td>" . $transaction['student'] . "</td>";
            		                $misc_trans_html .= "<td>" . $transaction['number'] . "</td>";
            		                $misc_trans_html .= "<td>" . $transaction['service'] . "</td>";
            		                $misc_trans_html .= "<td>" . $transaction['price'] . "</td>";
            		                $misc_trans_html .= "</tr>";
        		                }
    		                }
    		                
    		                $trans_html .= $misc_trans_html;
    		                
    		                $html = str_replace($tag, $trans_html, $html);
    		                break;
    		                
    		            case 'price_total':
    		                $html = str_replace($tag, '$' . number_format(floatval($price_total), 2, '.', ''), $html);
    		                break;
    		        }
    		        
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
        
        $pdf_url = $invoice_folder . $filename . '.pdf';
        $url_query =  "UPDATE tl_invoice SET invoice_url='$pdf_url' WHERE id='$invoice_id'";
        $url_result = $dbh->query($url_query);

        
        
        
        
    }

    // Generates our "clean name" which is 'first_last' format
    function cleanName($name) {
        // Remove special characters using regular expression
        $name = preg_replace('/[^a-zA-Z0-9 ]/', '', $name);
    
        // Convert to lowercase
        $name = strtolower($name);
    
        // Replace spaces with underscores
        $name = str_replace(' ', '_', $name);
    
        return $name;
    }
    
    // Generates initials from names
    function getInitials($name) {
        $words = explode(" ", $name);
        $initials = "";
    
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    
        return $initials;
    }
