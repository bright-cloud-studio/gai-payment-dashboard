<?php

namespace Bcs\Backend;

use Contao\Backend;
use Contao\Image;
use Contao\Input;
use Contao\DataContainer;
use Contao\StringUtil;
use Contao\System;

use Bcs\Model\Transaction;
use Bcs\Model\Assignment;
use Bcs\Model\Student;


class TransactionBackend extends Backend
{
    // Create 'Invoice' DCAs for Psychologists
    public function createTransaction(DataContainer $dc) {
        
        // do nothing if we havent saved this record
        if (!$dc->activeRecord)
		{
			return;
		}
		

        // If we have not yet created the Invoices for this request
        if($dc->activeRecord->pid != '') {
            
            $assignment = Assignment::findOneBy('id', $dc->activeRecord->pid);
            $student = Student::findOneBy('id', $assignment->student);

            $transaction = Transaction::findOneBy('id', $dc->activeRecord->id);
            $transaction->lasid = $student->lasid;
            $transaction->sasid = $student->sasid;
            $transaction->save();

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
		$this->Database->prepare("UPDATE tl_transaction SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")->execute($intId);
		System::getContainer()->get('monolog.logger.contao.cron')->info('A new version of record "tl_transactions.id='.$intId.'" has been created'.$this->getParentEntries('tl_listing', $intId));
	}
	
	public function exportListings()
	{
		$objListing = Transactions::findAll();
		$strDelimiter = ',';
	
		if ($objListing) {
			$strFilename = "Transactions_" .(date('Y-m-d_Hi')) ."csv";
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

		$objAlias = $this->Database->prepare("SELECT id FROM tl_transaction WHERE id=? OR alias=?")->execute($dc->id, $varValue);

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






    public function getAssignmentDetails(DataContainer $dc)
    {
        $assignment_details = [];

        $assignment = Assignment::findBy('id', $dc->pid);

        $assignment_details['district']['label'] = "District";
        $assignment_details['district']['value'] = $assignment->district;
        
        $template = new BackendTemplate('be_transaction_show_assignment');
        $template->assignment_details = array_values($assignment_details);

        return $template->parse();
    }





    
    

}
