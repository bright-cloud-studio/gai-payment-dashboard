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

		$transactions = $this->Database->query("SELECT * FROM tl_transaction WHERE date_submitted BETWEEN '".$this->convertDateToTimestamp("09/01/24")."' AND '".$this->convertDateToTimestamp("09/30/24")."' ORDER BY date_submitted ASC");
		while ($transactions->next())
		{
		    echo "Transaction: " . $transactions->id . "<br>";
		    echo "Date Submitted: " . date("m/d/y", $transactions->date_submitted) . "<br><br>";
		}
		
		
		die();
		
		

        /*
        if($dc->activeRecord->created_invoice_dcas != 'yes') {
    		// Arrays of IDs for the excluded selections
    		$exclude_psys = unserialize($dc->activeRecord->exclude_psychologists);
    		$exclude_schools = unserialize($dc->activeRecord->exclude_districts);
    		
    		$options = [
                'order' => 'firstname ASC'
            ];
    		$psychologists = MemberModel::findBy('disable', '', $options);
    		
    		foreach($psychologists as $psy) {
    
                // Only continue if this Member is fully filled out
    		    if($psy->firstname != '') {
    		        // Only continue if we arent excluding this PSY
        		    if(!in_array($psy->id, $exclude_psys)) {
        		        
        		        // Generate children "Invoice" DCAs for each PSY we want to generate
        		        $invoice = new Invoice();
        		        $invoice->pid = $dc->activeRecord->id;
        		        $invoice->psychologist = $psy->id;
        		        $invoice->save();
        		        
        		    }
    		    }
    		    
    		}
    
            $ir = InvoiceRequest::findOneBy('id', $dc->activeRecord->id);
            $ir->created_invoice_dcas = 'yes';
            $ir->save();
        }
        */

        
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
    

}
