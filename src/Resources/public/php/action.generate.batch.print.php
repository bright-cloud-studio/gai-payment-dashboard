<?php

    // Initialize Session, start Composer
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
    
    
    
    /********************/
	/* INITALIZE STUFFS */
	/********************/
	
	// Uses
    use Dompdf\Dompdf;
    use Dompdf\Options;
    
	
	// Connect to the DB
    $dbh = new mysqli("localhost", "staging_user", 'q&U,zA(+WUK$kQ!cZB', "staging_contao_5_3");
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
	
    /*****************/
	/* END INITALIZE */
	/*****************/


    // Step One
    // Find our oldest unfinished Invoice Request
    $request_query =  "SELECT * FROM tl_invoice_request WHERE generated_psys='yes' AND generated_districts='yes' and batch_url=''";
    $request_result = $dbh->query($request_query);
    if($request_result) {
        while($row = $request_result->fetch_assoc()) {
            
            $request = $row['id'];
            
            // Psy batch print
            // Create our Dompdf Options class
            $options = new Options();
            // Set our options
            $options->set("defaultFont", "Helvetica");
            $options->set("isRemoteEnabled", "true");
            
            // Initialize Dompdf using our just set up Options
        	$dompdf = new Dompdf($options);
        	$batch_queue = $_SESSION['batch_psy_queue'];
        	$dompdf->loadHtml($batch_queue);
        	
        	//$_SESSION['batch_psy_queue'] = '';
        	//$_SESSION['batch_district_queue'] = '';
        	
        	//$dompdf->loadHtml('hello world');
        	
        	// Set to standard paper size and portrait orientation
        	$dompdf->setPaper('A4', 'portrait');
        	$dompdf->render();
        	$output = $dompdf->output();
        	$addr_folder = $_SERVER['DOCUMENT_ROOT'] . '/../files/invoices/generation_' . $request . '/batch_print_psy.pdf';
            file_put_contents($addr_folder, $output);
            
            
            
            
            
            $dompdf_district = new Dompdf($options);
        	$batch_queue_district = $_SESSION['batch_district_queue'];
        	$dompdf_district->loadHtml($batch_queue_district);
        	$dompdf_district->setPaper('A4', 'portrait');
        	$dompdf_district->render();
        	$output_district = $dompdf_district->output();
        	$addr_folder_district = $_SERVER['DOCUMENT_ROOT'] . '/../files/invoices/generation_' . $request . '/batch_print_district.pdf';
            file_put_contents($addr_folder_district, $output_district);
            
            
            
            $folder_to_zip = $_SERVER['DOCUMENT_ROOT'] . '/../files/invoices/generation_' . $request . '/';
            $folder_for_batch = $_SERVER['DOCUMENT_ROOT'] . '/../files/invoices/generation_'.$request.'.zip';
            zipFolder($folder_to_zip, $folder_for_batch);

            $download_url = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/files/invoices/generation_'.$request.'.zip';
            $ir_q =  "update tl_invoice_request set batch_url='".$download_url."' WHERE id='".$request."'";
            $ir_r = $dbh->query($ir_q);

            
            
        }
    }
    
    
    
    
    function zipFolder($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }
    
        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }
    
        $source = str_replace('\\', '/', realpath($source));
    
        if (is_dir($source) === true)
        {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
    
            foreach ($files as $file)
            {
                $file = str_replace('\\', '/', $file);
    
                // Ignore "." and ".." folders
                if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                    continue;
    
                $file = realpath($file);
    
                if (is_dir($file) === true)
                {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                }
                else if (is_file($file) === true)
                {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        }
        else if (is_file($source) === true)
        {
            $zip->addFromString(basename($source), file_get_contents($source));
        }
    
        return $zip->close();
    }
