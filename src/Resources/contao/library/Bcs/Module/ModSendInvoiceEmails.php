<?php

/**
* Bright Cloud Studio's GAI Invoices
*
* Copyright (C) 2023-2024 Bright Cloud Studio
*
* @package    bright-cloud-studio/modal-gallery
* @link       https://www.brightcloudstudio.com/
* @license    http://opensource.org/licenses/lgpl-3.0.html
**/

  
namespace Bcs\Module;

use Google;
 
class ModSendInvoiceEmails extends \Contao\Module
{
 
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_send_invoice_emails';
  
    // our google api stuffs
    protected $client;
    protected $service;
    public static $spreadsheetId;
 
	protected $arrStates = array();
 
	/**
	 * Initialize the object
	 *
	 * @param \ModuleModel $objModule
	 * @param string       $strColumn
	 */
	public function __construct($objModule, $strColumn='main')
	{
        parent::__construct($objModule, $strColumn);

        // Create a client connection to Google
        $this->$client = new Google\Client();
        // Load our auth key
        $this->$client->setAuthConfig('key.json');
        // Set our scope to use the Sheets service
        $this->$client->addScope(Google\Service\Sheets::SPREADSHEETS);
        // Assign our client to a service
        $this->$service = new \Google_Service_Sheets($this->$client);
        // Set the ID for our specific spreadsheet
        ModSendInvoiceEmails::$spreadsheetId = '1PEJN5ZGlzooQrtIEdeo4_nZH73W0aJTUbRIoibzl3Lo';
		
	}
	
    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');
 
            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['send_invoice_emails'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&table=tl_module&act=edit&id=' . $this->id;
 
            return $objTemplate->parse();
        }
 
        return parent::generate();
    }
 
    /* Generate the module */
    protected function compile()
    {
        $rand_ver = rand(1,9999);
        $GLOBALS['TL_BODY']['send_invoice_emails'] = '<script src="system/modules/gai_invoices/assets/js/gai_invoice.js?v='.$rand_ver.'"></script>';
        
        // get the user and build their name
        $objUser = \FrontendUser::getInstance();
        $user = $objUser->firstname . " " . $objUser->lastname;
        
        // Get this user's unprocessed listings from Sheets
        $spreadsheet = $this->$service->spreadsheets->get(ModSendInvoiceEmails::$spreadsheetId);
        
        // an array to store this users entries
        $entryPsy = array();
        $psys = array();
        $entrySchool = array();
        $schools = array();
        $objUser = \FrontendUser::getInstance();
        
        // get the current month
        $today = date('F');
        $month = date("n", strtotime ( '-1 month' , strtotime ( $today ) )) ;
        $year = date("Y",time());
        
        
        // Get every psy so we can get their email
        $rangePsys = 'Psychologists';
        $responsePsys = $this->$service->spreadsheets_values->get(ModSendInvoiceEmails::$spreadsheetId, $rangePsys);
        $valuesPsys = $responsePsys->getValues();
        $entry_id = 0;
        foreach($valuesPsys as $entry) {
            if($entry_id != 0) {
                $psys[$entry[1]]    = $entry[7];
            }
            $entry_id++;
        }
        
        // Get every school so we can get their email
        $rangeSchools = 'Schools';
        $responseSchools = $this->$service->spreadsheets_values->get(ModSendInvoiceEmails::$spreadsheetId, $rangeSchools);
        $valuesSchools = $responseSchools->getValues();
        $entry_id = 0;
        foreach($valuesSchools as $entry) {
            if($entry_id != 0) {
                $schools[$entry[2]][$entry[3]]['em']    = $entry[6];
                $schools[$entry[2]][$entry[3]]['cc']    = $entry[7];
            }
            $entry_id++;
        }
        
        
        
        // get all of our unarchived Transactions
        $range = 'Invoices - Psy';
        $response = $this->$service->spreadsheets_values->get(ModSendInvoiceEmails::$spreadsheetId, $range);
        $values = $response->getValues();
        
        
        $lastMonth = date('m', strtotime('-1 month'));
        
        $entry_id = 1;
        $psy_total = 1;
        foreach($values as $entry) {
            
            // if the id matches this entry, it is related to our user
            if($entry_id != 1) {
                
                if($entry[1] == $lastMonth) {
                    if($entry[0] == $year) {
    
                        $arrData = array();
                        $arrData['row_id']          = $entry_id;
                        $arrData['id']              = $psy_total;
                        $arrData['billing_year']    = $entry[0];
                        $arrData['billing_month']   = $entry[1];
                        $arrData['invoice_number']  = $entry[2];
                        $arrData['psychologist']    = $entry[3];
                        $arrData['date_issued']     = $entry[4];
                        $arrData['date_due']        = $entry[5];
                        $arrData['invoice_link']    = $entry[6];
                        $arrData['email_sent']      = $entry[7];
                        $arrData['invoice_total']   = $entry[8];
                        $arrData['email']           = $psys[$entry[3]];
                        
                        // Generate as "List"
                        $strListTemplate = ($this->entry_customItemTpl != '' ? $this->entry_customItemTpl : 'send_invoice_emails_list');
                        $objListTemplate = new \FrontendTemplate($strListTemplate);
                        $objListTemplate->setData($arrData);
                        $entryPsy[$entry_id] = $objListTemplate->parse();
                        $psy_total++;
                    }
                }
            }
            
            $entry_id++;
        }
        $this->Template->psy_total = $psy_total-1;
        
        
        // get all of our unarchived Transactions
        $range = 'Invoices - School';
        $response = $this->$service->spreadsheets_values->get(ModSendInvoiceEmails::$spreadsheetId, $range);
        $values = $response->getValues();
        
        $entry_id = 1;
        $school_total = 1;
        foreach($values as $entry) {
            
            // if the id matches this entry, it is related to our user
            if($entry_id != 1) {
                
                if($entry[1] == $lastMonth) {
                    if($entry[0] == $year) {
                        $arrData = array();
                        $arrData['row_id']          = $entry_id;
                        $arrData['id']              = $school_total;
                        $arrData['billing_year']    = $entry[0];
                        $arrData['billing_month']   = $entry[1];
                        $arrData['invoice_number']  = $entry[2];
                        $arrData['district_name']   = $entry[3];
                        $arrData['school_name']     = $entry[4];
                        $arrData['date_issued']     = $entry[5];
                        $arrData['date_due']        = $entry[6];
                        $arrData['invoice_link']    = $entry[7];
                        $arrData['email_sent']      = $entry[8];
                        $arrData['invoice_total']   = $entry[9];
                        $arrData['email']           = $schools[$entry[3]][$entry[4]]['em'];
                        $arrData['cc']           = $schools[$entry[3]][$entry[4]]['cc'];
                        
                        // Generate as "List"
                        $strListTemplate = ($this->entry_customItemTpl != '' ? $this->entry_customItemTpl : 'send_invoice_emails_school_list');
                        $objListTemplate = new \FrontendTemplate($strListTemplate);
                        $objListTemplate->setData($arrData);
                        $entrySchool[$entry_id] = $objListTemplate->parse();
                        $school_total++;
                    }
                }

            }
            
            $entry_id++;
        }
        $this->Template->school_total = $school_total-1;
        
        
        // set this users entries to the template
        $this->Template->invoicesPsychologists = $entryPsy;
        $this->Template->invoicesSchools = $entrySchool;
  
	}

} 
