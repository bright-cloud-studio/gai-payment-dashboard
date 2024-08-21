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

use Bcs\Model\Service;
use Bcs\Model\PriceTier;
use Bcs\Model\Transaction;
use Bcs\Model\Assignment;

use Contao\BackendTemplate;
use Contao\System;
use Contao\FrontendUser;


class ModPsychWorkForm extends \Contao\Module
{

    /* Default Template */
    protected $strTemplate = 'mod_psych_work_form';
    
    protected static $template_transactions = array();
    protected static $template_assignments = array();

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
        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/bcspaymentdashboard/js/datatables.min.js';
        $GLOBALS['TL_CSS'][]        = 'bundles/bcspaymentdashboard/css/datatables.min.css';
        
        $member = FrontendUser::getInstance();

        // Update this to list Assignment infomation instead of Transactions
        // Get Transactions that have our selected Assignment as the parent and that belong to this Psychologist
        $transactions = Transaction::findBy(['psychologist = ?'], [$member->id]);
        
        foreach($transactions as $transaction) {
            $template_transactions[$transaction->id]['date_submitted'] = $transaction->date_submitted;
            $template_transactions[$transaction->id]['service'] = $transaction->service;
            $template_transactions[$transaction->id]['price'] = $transaction->price;
            $template_transactions[$transaction->id]['meeting_date'] = $transaction->meeting_date;
            $template_transactions[$transaction->id]['meeting_start'] = $transaction->meeting_start;
            $template_transactions[$transaction->id]['meeting_end'] = $transaction->meeting_end;
            $template_transactions[$transaction->id]['meeting_duration'] = $transaction->meeting_duration;
            $template_transactions[$transaction->id]['notes'] = $transaction->notes;
        }
        
        $this->Template->transactions = $template_transactions;
        
    }
  

}
