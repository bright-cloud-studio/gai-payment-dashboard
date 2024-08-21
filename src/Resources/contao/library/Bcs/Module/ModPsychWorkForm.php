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

        // Include Datatables JS library and CSS stylesheets
        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/bcspaymentdashboard/js/datatables.min.js';
        $GLOBALS['TL_CSS'][]        = 'bundles/bcspaymentdashboard/css/datatables.min.css';
        
        $member = FrontendUser::getInstance();

        // Update this to list Assignment infomation instead of Transactions
        // Get Transactions that have our selected Assignment as the parent and that belong to this Psychologist
        //$transactions = Transaction::findBy(['psychologist = ?'], [$member->id]);

        $assignments = Assignment::findBy(['psychologist = ?'], [$member->id]);
        
        foreach($assignments as $assignment) {
            $template_assignments[$assignment->id]['date_created'] = $transaction->date_created;
            $template_assignments[$assignment->id]['date_30_day'] = $transaction->date_30_day;
            $template_assignments[$assignment->id]['date_45_day'] = $transaction->date_45_day;
            $template_assignments[$assignment->id]['psychologist'] = $transaction->psychologist;
            $template_assignments[$assignment->id]['district'] = $transaction->district;
            $template_assignments[$assignment->id]['school'] = $transaction->school;
            $template_assignments[$assignment->id]['student'] = $transaction->student;
            $template_assignments[$assignment->id]['initial_reeval'] = $transaction->initial_reeval;
            $template_assignments[$assignment->id]['type_of_testing'] = $transaction->type_of_testing;
            $template_assignments[$assignment->id]['testing_date'] = $transaction->testing_date;
            $template_assignments[$assignment->id]['meeting_required'] = $transaction->meeting_required;
            $template_assignments[$assignment->id]['meeting_date'] = $transaction->meeting_date;
            $template_assignments[$assignment->id]['contact_info_parent'] = $transaction->contact_info_parent;
            $template_assignments[$assignment->id]['contact_info_teacher'] = $transaction->contact_info_teacher;
            $template_assignments[$assignment->id]['team_chair'] = $transaction->team_chair;
            $template_assignments[$assignment->id]['email'] = $transaction->email;
            $template_assignments[$assignment->id]['report_submitted'] = $transaction->report_submitted;
            $template_assignments[$assignment->id]['notes'] = $transaction->notes;
        }
        
        $this->Template->assignments = $template_assignments;
        
    }
  

}
