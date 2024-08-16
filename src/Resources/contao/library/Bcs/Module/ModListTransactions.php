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

use Contao\BackendTemplate;
use Contao\System;
use Contao\FrontendUser;


class ModListTransactions extends \Contao\Module
{

    /* Default Template */
    protected $strTemplate = 'mod_list_transactions';
    
    protected static $service_prices = array();

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

        // Get Transactions that have our selected Assignment as the parent and that belong to this Psychologist
        $transactions = Transaction::findBy(['pid = ?', 'psychologist = ?'], [$_SESSION['assignment_uuid'], $member->id]);
        
        foreach($transactions as $transaction) {
            $service_prices[$transaction->id]['date_submitted'] = $transaction->psychologist;
            $service_prices[$transaction->id]['service'] = $transaction->psychologist;
            $service_prices[$transaction->id]['price'] = $transaction->psychologist;
            $service_prices[$transaction->id]['meeting_date'] = $transaction->psychologist;
            $service_prices[$transaction->id]['meeting_start'] = $transaction->psychologist;
            $service_prices[$transaction->id]['meeting_end'] = $transaction->psychologist;
            $service_prices[$transaction->id]['meeting_duration'] = $transaction->psychologist;
            $service_prices[$transaction->id]['notes'] = $transaction->psychologist;
        }
        
        $this->Template->service_prices = $service_prices;

        
        
    }
  

}
