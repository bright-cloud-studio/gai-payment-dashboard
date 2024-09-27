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

use Bcs\Model\Assignment;
use Bcs\Model\District;
use Bcs\Model\PriceTier;
use Bcs\Model\School;
use Bcs\Model\Service;
use Bcs\Model\Student;
use Bcs\Model\Transaction;

use Contao\BackendTemplate;
use Contao\System;
use Contao\FrontendUser;


class ModReviewTransactions extends \Contao\Module
{

    /* Default Template */
    protected $strTemplate = 'mod_review_transactions';
    
    protected static $template_transactions = array();
    protected static $template_assignments = array();
    
    public $is_admin = false;

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
        if(in_array("2", $member->groups)) {
           $is_admin = true;
        }
            
        // Include Datatables JS library and CSS stylesheets
        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/bcspaymentdashboard/js/datatables.min.js';
        $GLOBALS['TL_CSS'][]        = 'bundles/bcspaymentdashboard/css/datatables.min.css';

        $transactions = Transaction::findBy(['psychologist = ?'], [$member->id]);
        
        foreach($transactions as $transaction) {

            $assignment = Assignment::findOneBy('id', $transactiom->pid);

            // District
            $district = District::findOneBy('id', $assignment->district);
            $template_assignments[$assignment->id]['district'] = $district->name;
            // School
            $school = School::findOneBy('id', $assignment->school);
            $template_assignments[$assignment->id]['school'] = $school->school_name;
            // Student
            $student = Student::findOneBy('id', $assignment->student );
            $template_assignments[$assignment->id]['student'] = $student->name;
            // Lasid
            $template_assignments[$assignment->id]['student'] = $student->lasid;
            // Sasid
            $template_assignments[$assignment->id]['student'] = $student->sasid;
            // Service
            $service = Service::findOneBy('service_code', $transaction->service);
            $template_assignments[$assignment->id]['service'] = $service->name;
            // Price
            $template_assignments[$transaction->id]['price'] = $transaction->price;
            
            
        }
        
        $this->Template->transactions = $template_transactions;
        
    }
  

}
