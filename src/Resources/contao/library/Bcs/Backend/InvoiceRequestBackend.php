<?php

namespace Bcs\Backend;

use DateTime;

use Bcs\Model\Invoice;
use Bcs\Model\InvoiceRequest;
use Bcs\Model\Transaction;

use Contao\Backend;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\MemberModel;
use Contao\StringUtil;


class InvoiceRequestBackend extends Backend
{

    // Create 'Invoice' DCAs for each Invoice we want to create
    public function createInvoiceDCAs(DataContainer $dc) {
        
        // do nothing if we havent saved this record
        if (!$dc->activeRecord)
		{
			return;
		}
        
        // Build an array with Psy ID as the first key and Transaction IDs as the second
        $arrTransactions = array();
		$transactions = $this->Database->query("SELECT * FROM tl_transaction WHERE date_submitted BETWEEN '".$this->convertDateToTimestamp("09/01/24")."' AND '".$this->convertDateToTimestamp("09/30/24")."' and published='1' ORDER BY date_submitted ASC");
		while ($transactions->next())
		{
		    $arrTransactions[$transactions->psychologist][] = $transactions->id;
		}

        // If we have not yet created the Invoices for this request
        if($dc->activeRecord->created_invoice_dcas != 'yes') {

    		
    		// Build arrays of IDs for which Psys and Schools to skip
    		$exclude_psys = array();
    		$exclude_schools = array();
    		if(is_array($dc->activeRecord->exclude_psychologists))
    		    $exclude_psys = unserialize($dc->activeRecord->exclude_psychologists);
		    if(is_array($dc->activeRecord->exclude_districts))
    		$exclude_schools = unserialize($dc->activeRecord->exclude_districts);
    		
    		
    		// Loop through all active Psychologists
    		$options = ['order' => 'firstname ASC'];
    		$psychologists = MemberModel::findBy('disable', '', $options);
    		foreach($psychologists as $psy) {
    
                // Only continue if this Member is fully filled out
    		    if($psy->firstname != '') {
    		        
    		        // Only continue if we arent excluding this PSY
        		    if(!in_array($psy->id, $exclude_psys)) {
        		        
        		        // Create a new Invoice child for this request, set our datam save and move on
        		        $invoice = new Invoice();
        		        $invoice->pid = $dc->activeRecord->id;
        		        $invoice->psychologist = $psy->id;
        		        $invoice->psychologist_name = $psy->firstname . " " . $psy->lastname;
        		        
        		        // Build a csv string of the transaction ids for this invoice
        		        $first = true;
        		        foreach($arrTransactions[$psy->id] as $id) {
        		            if($first) {
        		                $first = false;
        		                $invoice->transaction_ids .= $id;
        		            } else {
        		                $invoice->transaction_ids .= "," . $id;
        		            }
        		        }

                        $addr_folder = $_SERVER['DOCUMENT_ROOT'] . '/../files/invoices/' . $this->cleanName($invoice->psychologist_name);
                        $filename = 'invoice_' . date('yy_mm') . '.pdf';
                        $invoice->invoice_url = $add_folder . $filename;
        		        
        		        // Only save if we have Transactions attached to this Invoice
        		        if($invoice->transaction_ids != '')
        		            $invoice->save();
        		        
        		    }
    		    }
    		    
    		}
            
            // We have just created our Invoice children, mark as such so we don't do this over and over
            $ir = InvoiceRequest::findOneBy('id', $dc->activeRecord->id);
            $ir->created_invoice_dcas = 'yes';
            $ir->save();
        }
        

        
    }


    public function deleteInvoiceRequest(DataContainer $dc) {
        // do nothing if we don't have an ID
        if (!$dc->activeRecord?->id)
		{
			return;
		}

        // Find all Invoice children
        $invoices = Invoice::findBy(['pid = ?'], [$dc->activeRecord->id]);
        foreach($invoices as $invoice) {
            // Delete the PDF file if it exists
            if (file_exists($invoice->invoice_url)) {
                unlink($invoice->invoice_url);
            }
            // Delete the Invoice itself
            $invoice->delete();
        }
        
    }



    
    
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
        if (strlen(Input::get('tid')))
		{
			$this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
			$this->redirect($this->getReferer());
		}

		$href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
	}	
	

	public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
	{
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_listing']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_listing']['fields']['published']['save_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, ($dc ?: $this));
				}
				elseif (is_callable($callback))
				{
					$blnVisible = $callback($blnVisible, ($dc ?: $this));
				}
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_invoice_request SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")->execute($intId);
		//$this->log('A new version of record "tl_invoice_request.id='.$intId.'" has been created'.$this->getParentEntries('tl_listing', $intId), __METHOD__, TL_GENERAL);
	}
	
	public function exportListings()
	{
		$objListing = Transactions::findAll();
		$strDelimiter = ',';
	
		if ($objListing) {
			$strFilename = "Assignments_" .(date('Y-m-d_Hi')) ."csv";
			$tmpFile = fopen('php://memory', 'w');
			
			$count = 0;
			while($objListing->next()) {
				$row = $objListing->row();
				if ($count == 0) {
					$arrColumns = array();
					foreach ($row as $key => $value) {
						$arrColumns[] = $key;
					}
					fputcsv($tmpFile, $arrColumns, $strDelimiter);
				}
				$count ++;
				fputcsv($tmpFile, $row, $strDelimiter);
			}
			
			fseek($tmpFile, 0);
			
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="' . $strFilename . '";');
			fpassthru($tmpFile);
			exit();
		} else {
			return "Nothing to export";
		}
	}
	
	public function generateAlias($varValue, DataContainer $dc)
	{
		$autoAlias = false;
		
		// Generate an alias if there is none
		if ($varValue == '')
		{
			$autoAlias = true;
			$varValue = standardize(StringUtil::restoreBasicEntities($dc->activeRecord->name));
		}

		$objAlias = $this->Database->prepare("SELECT id FROM tl_assignment WHERE id=? OR alias=?")->execute($dc->id, $varValue);

		// Check whether the page alias exists
		if ($objAlias->numRows > 1)
		{
			if (!$autoAlias)
			{
				throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
			}

			$varValue .= '-' . $dc->id;
		}

		return $varValue;
	}


    // Get Psychologists as select menu
    public function getPsychologists(DataContainer $dc) { 

        // Hold the psys
        $psychologists = array();

        // Use the DB to grab all of our enabled members, aka our psychologists
		$this->import('Database');
		$result = $this->Database->prepare("SELECT * FROM tl_member WHERE disable=0 ORDER BY firstname ASC")->execute();
		while($result->next())
		{
            // Add ti array with ID as the value and firstname lastname as the label
            $psychologists = $psychologists + array($result->id => ($result->firstname . " " . $result->lastname));   
		}

		return $psychologists;
	}

    // Get Districts as select menu
    public function getDistricts(DataContainer $dc) { 

        // Hold the psys
        $districts = array();

        // Use the DB to grab all of our enabled members, aka our psychologists
		$this->import('Database');
		$result = $this->Database->prepare("SELECT * FROM tl_district WHERE published=1 ORDER BY district_name ASC")->execute();
		while($result->next())
		{
            // Add ti array with ID as the value and firstname lastname as the label
            $districts = $districts + array($result->id => $result->district_name);   
		}

		return $districts;
	}

    // Get Schools as select menu
    public function getSchools(DataContainer $dc) { 

        // Hold the psys
        $schools = array();

        // Use the DB to grab all of our enabled members, aka our psychologists
		$this->import('Database');
		$result = $this->Database->prepare("SELECT * FROM tl_school WHERE published=1 ORDER BY school_name ASC")->execute();
		while($result->next())
		{
            // Add ti array with ID as the value and firstname lastname as the label
            $schools = $schools + array($result->id => $result->school_name);   
		}

		return $schools;
	}

    // Get Services as select menu
    public function getServices(DataContainer $dc) { 

        // Hold the psys
        $services = array();

        // Use the DB to grab all of our enabled members, aka our psychologists
		$this->import('Database');
		$result = $this->Database->prepare("SELECT * FROM tl_service WHERE published=1 ORDER BY name ASC")->execute();
		while($result->next())
		{
            // Add ti array with ID as the value and firstname lastname as the label
            $services = $services + array($result->service_code => $result->name);   
		}

		return $services;
	}
	
	
	public function convertDateToTimestamp($dateString) {
        // Assuming the date string is in the format "m/d/y"
        $dateObject = DateTime::createFromFormat('m/d/y', $dateString);
        if ($dateObject) {
            return $dateObject->getTimestamp();
        } else {
            return false; // Invalid date format
        }
    }
    public function cleanName($name) {
        // Remove special characters using regular expression
        $name = preg_replace('/[^a-zA-Z0-9 ]/', '', $name);
    
        // Convert to lowercase
        $name = strtolower($name);
    
        // Replace spaces with underscores
        $name = str_replace(' ', '_', $name);
    
        return $name;
    }
    

}
