<?php

/**
* Bright Cloud Studio's GAI Payment Dashboard
*
* Copyright (C) 2024-2025 Bright Cloud Studio
*
* @package    bright-cloud-studio/gai-payment-dashboard
* @link       https://www.brightcloudstudio.com/
* @license    http://opensource.org/licenses/lgpl-3.0.html
**/

namespace Bcs\Module;

use Bcs\Model\Invoice;

use Contao\BackendTemplate;
use Contao\System;
use Contao\FrontendUser;


class ModInvoiceHistory extends \Contao\Module
{

    /* Default Template */
    protected $strTemplate = 'mod_invoice_history';
    
    protected static $template_invoices = array();

    /* Construct function */
    public function __construct($objModule, $strColumn='main')
    {
        parent::__construct($objModule, $strColumn);
    }

    /* Generate function */
    public function generate()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
        {
            $objTemplate = new BackendTemplate('be_wildcard');
 
            $objTemplate->wildcard = '### ' . mb_strtoupper($GLOBALS['TL_LANG']['FMD']['assignments'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&table=tl_module&act=edit&id=' . $this->id;
 
            return $objTemplate->parse();
        }
 
        return parent::generate();
    }


    protected function compile()
    {
        
        $member = FrontendUser::getInstance();
            
        // Include Datatables JS library and CSS stylesheets
        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/bcspaymentdashboard/js/datatables.min.js';
        $GLOBALS['TL_CSS'][]        = 'bundles/bcspaymentdashboard/css/datatables.min.css';

        $invoices = Invoice::findBy(['psychologist = ?'], [$member->id]);
        
        foreach($invoices as $invoice) {
            $template_invoices[$invoice->id]['created'] = date("m/d/y", $invoice->tstamp);
            $template_invoices[$invoice->id]['invoice_url'] = $invoice->invoice_url;
        }
        
        $this->Template->invoices = $template_invoices;
        
    }
  

}
