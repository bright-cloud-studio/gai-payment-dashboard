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

    // Get our opening and closing wrappers
    $invoice_psy_start = file_get_contents('bundles/bcspaymentdashboard/templates/batch_invoice_psy_start.html', true);
    $invoice_psy_end = file_get_contents('bundles/bcspaymentdashboard/templates/batch_invoice_psy_end.html', true);
    
    $invoice_district_start = file_get_contents('bundles/bcspaymentdashboard/templates/batch_invoice_district_start.html', true);
    $invoice_district_end = file_get_contents('bundles/bcspaymentdashboard/templates/batch_invoice_district_end.html', true);

    // Step One
    // Find our oldest unfinished Invoice Request
    $request_query =  "SELECT * FROM tl_invoice_request WHERE generated_psys='yes' AND generated_districts='yes' and batch_url=''";
    $request_result = $dbh->query($request_query);
    if($request_result) {
        while($row = $request_result->fetch_assoc()) {
            
            $request = $row['id'];
            
            $options = new Options();
            // Set our options
            $options->set("defaultFont", "Helvetica");
            $options->set("isRemoteEnabled", "true");

            // Generate Psychologist's Batch Print file from their saved Invoice HTML
            $psy_html = '';
            $psy_query =  "SELECT * FROM tl_invoice WHERE pid='$request'";
            $psy_result = $dbh->query($psy_query);
            if($psy_result) {
                while($psy = $psy_result->fetch_assoc()) {
                    $psy_html = $psy_html . $invoice_psy_start . $psy['invoice_html'] . $invoice_psy_end;
                }
            }

            
            $dompdf_psychologist = new Dompdf($options);
        	$dompdf_psychologist->loadHtml($psy_html);
        	$dompdf_psychologist->setPaper('A4', 'portrait');
        	$dompdf_psychologist->render();
        	$output_psychologist = $dompdf_psychologist->output();
        	$addr_folder_district = $_SERVER['DOCUMENT_ROOT'] . '/../files/invoices/generation_' . $request . '/batch_print_psychologists.pdf';
            file_put_contents($addr_folder_district, $output_psychologist);

            // Generate District Batch Print file from their saved Invoice HTML
            $district_html = '';
            $district_query =  "SELECT * FROM tl_invoice_district WHERE pid='$request'";
            $district_result = $dbh->query($district_query);
            if($district_result) {
                while($district = $district_result->fetch_assoc()) {
                    $district_html = $district_html . $invoice_district_start . $district['invoice_html'] . $invoice_district_end;
                }
            }
            $dompdf_district = new Dompdf($options);
        	$dompdf_district->loadHtml($district_html);
        	$dompdf_district->setPaper('A4', 'portrait');
        	$dompdf_district->render();
        	$output_district = $dompdf_district->output();
        	$addr_folder_district = $_SERVER['DOCUMENT_ROOT'] . '/../files/invoices/generation_' . $request . '/batch_print_districts.pdf';
            file_put_contents($addr_folder_district, $output_district);
            
            // ZIP Folder
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
