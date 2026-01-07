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
	
	
	$price_total = 0;
	
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
            
            if($r['lasid'] != '')
                $students[$r['id']]['number'] = $r['lasid'];
            else
                $students[$r['id']]['number'] = $r['sasid'];
        }
    }
    
    // Get all Services
    $services = array();
    $se_q =  "SELECT * FROM tl_service WHERE published='1'";
    $se_r = $dbh->query($se_q);
    if($se_r) {
        while($r = $se_r->fetch_assoc()) {
            $services[$r['service_code']] = $r['name'];
        }
    }

    
    
    
    /*****************/
	/* END INITALIZE */
	/*****************/



    // Step One
    // Find our oldest unfinished Invoice Request
    $request_query =  "SELECT * FROM tl_invoice_request WHERE generated_psys='no' ORDER BY id ASC";
    $request_result = $dbh->query($request_query);
    if($request_result) {
        while($row = $request_result->fetch_assoc()) {
            
            $request = $row['id'];
            $session_id = $session_id . $request;
            $date_start = $row['date_start'];

            // Step Two
            // Find Invoices without a PDF link
            $invoice_query =  "SELECT * FROM tl_invoice WHERE pid='$request' AND invoice_url IS NULL ORDER BY id ASC";
            //$invoice_query =  "SELECT * FROM tl_invoice WHERE pid='$request' ORDER BY id ASC";
            $invoice_result = $dbh->query($invoice_query);
            if($invoice_result) {
                while($db_inv = $invoice_result->fetch_assoc()) {
                    
                    // Step Three
                    // Generate a folder for this Psy if it doesn't exist already
                    $addr_folder = $_SERVER['DOCUMENT_ROOT'] . '/../files/invoices/generation_' . $request . '/psychologists/' . cleanName($db_inv['psychologist_name']);
                    $filename = "invoice_" . date('y_m', strtotime($date_start));
                    
                    if (!file_exists($addr_folder)) {
                        mkdir($addr_folder, 0777, true);
                    }
                    
                    $invoice_id = $db_inv['id'];
                    $psy_id = $db_inv['psychologist'];
                    $transaction_ids = $db_inv['transaction_ids'];
                    $misc_transaction_ids = $db_inv['misc_transaction_ids'];
                    
                    $invoice_folder = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/files/invoices/generation_' . $request . '/psychologists/' . cleanName($db_inv['psychologist_name']) . '/';
                    generateInvoice($dbh, $invoice_id, $psy_id, $invoice_folder, $addr_folder, $filename, $transaction_ids, $misc_transaction_ids, $districts, $schools, $students, $services, $date_start);

                }
            }
            
            $ir_q =  "update tl_invoice_request set generated_psys='yes' WHERE id='".$request."'";
            $ir_r = $dbh->query($ir_q);
            
            
        }
    }
    
    // Generates the actual PDF file
    function generateInvoice($dbh, $invoice_id, $psy_id, $invoice_folder, $addr_folder, $filename, $transaction_ids, $misc_transaction_ids, $districts, $schools, $students, $services, $date_start) {
        
        // Get this Psy's data
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
        
        // Get Transaction/Misc Transaction data and merge into a single array
        $transactions = array();
        $transactions_misc = array();
        $transactions_normal = generateTransactions($dbh, $districts, $schools, $students, $services, $transaction_ids);
        $transactions_misc = generateMiscTransactions($dbh, $districts, $schools, $students, $services, $misc_transaction_ids);
        
        if($transactions_normal != null)
            $transactions = array_merge($transactions, $transactions_normal);
        if($transactions_misc != null)
            $transactions = array_merge($transactions, $transactions_misc);
            
        // Tracks our final total for the end of the last sheet
        global $price_total;
        
        // Initialize Dompdf using our just set up Options
	    $dompdf = new Dompdf($options);
    	
        // Load our HTML template
        //$html = file_get_contents('bundles/bcspaymentdashboard/templates/invoice_psy.html', true);
        
        // Holds HTML for each page
        $template = array();
        
        // How many Transactions can we fit per page
        $transactions_per_page = 54;
        
        // Calculate the total number of Transactions this Invoice has
        $total_transactions = 0;
        if($transactions)
            $total_transactions += count($transactions);
        
        // Calculate how many pages we need to fit this many Transactions
        $total_pages = ceil($total_transactions / $transactions_per_page);
        
        // Holds the HTML we will push into the PDF
        $render_html = '';

        // First, setup everything but Transactions
        for($x = 0; $x < $total_pages; $x++) {
            
            // Load our HTML template
            $template[$x] = file_get_contents('bundles/bcspaymentdashboard/templates/invoice_psy.html', true);
            
            // Find all instances of our tag brackets '{{tag}}' and store them in the $tags array
            preg_match_all('/\{{2}(.*?)\}{2}/is', $template[$x], $tags);
            
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
            					$template[$x] = str_replace($tag, $psy['name'], $template[$x]);
            					break;
            				case 'date_issued':
            					$template[$x] = str_replace($tag, date('m/d/Y'), $template[$x]);
            					break;
            				case 'invoice_number':
            					$template[$x] = str_replace($tag, date('Y_m', strtotime($date_start)), $template[$x]);
            					break;
            				case 'addr_1':
            					$template[$x] = str_replace($tag, $psy['addr_1'], $template[$x]);
            					break;
            				case 'addr_2':
            					$template[$x] = str_replace($tag, $psy['addr_2'], $template[$x]);
            					break;
            				default:
            					break;
            			}
            		
            		break;
            		
            		//colors
            		case 'invoice':
            			switch($explodedTag[1]) { 
            				case 'price_total':
            				    if($x < ($total_pages-1))
            				        $template[$x] = str_replace($tag, 'VIEW FINAL SHEET', $template[$x]);
            				    else
            					    $template[$x] = str_replace($tag, '$' . number_format(floatval($price_total), 2, '.', ','), $template[$x]);
        					break;
            			}
        			break;
            	}
            }
        }
        
        
        // Now, do the Transactions
        $transaction_index = 0;
        
        for($x = 0; $x < $total_pages; $x++) {
            
            // Find all instances of our tag brackets '{{tag}}' and store them in the $tags array
            preg_match_all('/\{{2}(.*?)\}{2}/is', $template[$x], $tags);
            
            // Loop through those tags and replace them with the correct product data
            foreach($tags[0] as $tag) {
            	
            	// Remove brackets from our tag
            	$cleanTag = str_replace("{{","",$tag);
            	$cleanTag = str_replace("}}","",$cleanTag);
            	
            	// Explode our tag into two parts
            	$explodedTag = explode("::", $cleanTag);
            	
            	// Do different things based on the first part of our tag
            	switch($explodedTag[0]) {
            		
            		case 'invoice':
            			
            			switch($explodedTag[1]) { 
            			    case 'transactions':
            			        
            			        $trans_html = '';
            			        $misc_trans_html = '';
            			        
            			        for($t = 0; $t < $transactions_per_page; $t++) {
            			        
        		                    $trans_html .= "<tr>";
            		                $trans_html .= "<td>" . $transactions[$transaction_index]['district'] . "</td>";
            		                $trans_html .= "<td>" . $transactions[$transaction_index]['school'] . "</td>";
            		                $trans_html .= "<td>" . $transactions[$transaction_index]['student'] . "</td>";
            		                $trans_html .= "<td>" . $transactions[$transaction_index]['number'] . "</td>";
            		                $trans_html .= "<td>" . $transactions[$transaction_index]['service'] . "</td>";
            		                $trans_html .= "<td>" . $transactions[$transaction_index]['price'] . "</td>";
            		                $trans_html .= "</tr>";
            		                $transaction_index++;
            		                
            		                if($transaction_index == count($transactions))
            		                    break;
        		                }
        		                
        		                //$trans_html .= $misc_trans_html;
            			        $template[$x] = str_replace($tag, $trans_html, $template[$x]);
            			        break;
            			}
            			break;
            	}
            	
            }
            
            // Add our modified template to the HTML we are rendering into a PDF
            $render_html .= $template[$x];
            
        }
        
        // Load our modified HTML into the pdf generator
    	$dompdf->loadHtml($render_html);
    	//$dompdf->loadHtml('hello world');
    	
    	// Set to standard paper size and portrait orientation
    	$dompdf->setPaper('A4', 'portrait');
    	
    	// Render and save PDF file
    	$dompdf->render();
    	$output = $dompdf->output();
        file_put_contents($addr_folder . '/' . $filename . '.pdf', $output);
        $pdf_url = $invoice_folder . $filename . '.pdf';
        $url_query =  "UPDATE tl_invoice SET invoice_url='$pdf_url', invoice_html='".$dbh->real_escape_string($render_html)."'  WHERE id='$invoice_id'";
        $url_result = $dbh->query($url_query);
        
        // Reset our global price total
        $price_total = 0;
    }

    
    
    
    // Assembles Transaction data into a php array
    function generateTransactions($dbh, $districts, $schools, $students, $services, $transaction_ids) {
        global $price_total;
        
        if($transaction_ids != '') {
            $transactions = array();
            $transactions_query =  "SELECT * FROM tl_transaction WHERE id IN (". $transaction_ids .") ORDER BY date_submitted ASC";
            $transactions_results = $dbh->query($transactions_query);
            if($transactions_results) {
                $i = 0;
                while($row = $transactions_results->fetch_assoc()) {
                    
                    // Update the status of this transaction
                    $update =  "update tl_transaction set status='invoiced' WHERE id='".$row['id']."'";
                    $result_update = $dbh->query($update);
                    
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
                    
                    $transactions[$i]['id'] = $row['id'];
                    $transactions[$i]['district'] = $districts[$a_district];
                    $transactions[$i]['school'] = $schools[$a_school];
                    $transactions[$i]['student'] = $students[$a_student]['name'];
                    
                    $transactions[$i]['number'] = $students[$a_student]['number'];

                    if($row['service'] == 1) {
                        $transactions[$i]['service'] = $services[$row['service']] . ' ('.$row['meeting_duration'].' mins)';
                        
                        $dur = ceil(intval($row['meeting_duration']) / 60);
                        $final_price = $dur * $row['price'];
                        $transactions[$i]['price'] = number_format(floatval($final_price), 2, '.', ',');
                        $price_total =  number_format(floatval($price_total), 2, '.', '') + number_format(floatval($final_price), 2, '.', '');
                    } else if($row['service'] == 19) {
                        $transactions[$i]['service'] = $services[$row['service']];
                        $final_price = $row['meeting_duration'] * 0.50;
                        $transactions[$i]['price'] = number_format(floatval($final_price), 2, '.', ',');
                        $price_total =  number_format(floatval($price_total), 2, '.', '') + number_format(floatval($final_price), 2, '.', '');
                    } else {
                        $transactions[$i]['service'] = $services[$row['service']];
                        $transactions[$i]['price'] = number_format(floatval($row['price']), 2, '.', ',');
                        $price_total =  number_format(floatval($price_total), 2, '.', '') + number_format(floatval($row['price']), 2, '.', '');
                    }
                    
                    $i++;
                }
            }

            return $transactions;
        }
    }
    
    
    
    // Assemble Misc. Transaction data into a php array
    function generateMiscTransactions($dbh, $districts, $schools, $students, $services, $misc_transaction_ids) {
        global $price_total;
        
        if($misc_transaction_ids != '') {
            $transactions = array();
            $transactions_query =  "SELECT * FROM tl_transaction_misc WHERE id IN (". $misc_transaction_ids .") ORDER BY date_submitted ASC";
            $transactions_results = $dbh->query($transactions_query);
            if($transactions_results) {
                $i = 0;
                while($row = $transactions_results->fetch_assoc()) {
                    
                    // Update the status of this transaction
                    $update =  "update tl_transaction_misc set status='invoiced' WHERE id='".$row['id']."'";
                    $result_update = $dbh->query($update);
                    
                    $transactions[$i]['student'] = $row['student_initials'];
                    
                    if($row['lasid'] != '')
                        $transactions[$i]['number'] = $row['lasid'];
                    else
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
            
            return $transactions;
        }
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
