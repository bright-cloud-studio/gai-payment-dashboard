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
use Bcs\Model\TransactionMisc;

use Contao\BackendTemplate;
use Contao\System;
use Contao\FrontendUser;


class ModReviewTransactions extends \Contao\Module
{

    /* Default Template */
    protected $strTemplate = 'mod_review_transactions';
    
    protected static $template_transactions = array();
    protected static $template_transactions_misc = array();
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
        
        $transactions_total = 0.00;
        
        foreach($transactions as $transaction) {
            
            // Get the current month and current year as two digit numbers
            $last_month = date('m');
            $current_year = date('y');
            $transaction_month = date('m', $transaction->date_submitted);
            $transaction_year = date('y', $transaction->date_submitted);
            
            //echo "Last Month: " . $last_month . "<br>";
            //echo "Current Year: " . $current_year . "<br>";
            //echo "Trans Month: " . $transaction_month . "<br>";
            //echo "Trans Year: " . $transaction_year . "<br>";
            
            if($transaction_year == $current_year && $transaction_month == $last_month) {
                $assignment = Assignment::findOneBy('id', $transaction->pid);
                
                $template_transactions[$transaction->id]['id'] = $transaction->id;
                $template_transactions[$transaction->id]['transaction_type'] = "transaction";
                $template_transactions[$transaction->id]['date_submitted'] = date('m_d_y', $transaction->date_submitted);
    
                // Reviewed
                if($transaction->published == '1')
                    $template_transactions[$transaction->id]['reviewed'] = 'Reviewed';
                else
                    $template_transactions[$transaction->id]['reviewed'] = 'Unreviewed';
                
                // District
                $district = District::findOneBy('id', $assignment->district);
                $template_transactions[$transaction->id]['district'] = $district->district_name;
                // School
                $school = School::findOneBy('id', $assignment->school);
                $template_transactions[$transaction->id]['school'] = $school->school_name;
                // Student
                $student = Student::findOneBy('id', $assignment->student );
                $template_transactions[$transaction->id]['student'] = $student->name;
                // Lasid
                $template_transactions[$transaction->id]['lasid'] = $student->lasid;
                // Sasid
                $template_transactions[$transaction->id]['sasid'] = $student->sasid;
                
                // Service
                $service = Service::findOneBy('service_code', $transaction->service);
                if($transaction->meeting_duration > 0) {
                    $template_transactions[$transaction->id]['service'] = $service->name . " (" . $transaction->meeting_duration . " minutes)";
                } else {
                    $template_transactions[$transaction->id]['service'] = $service->name;
                }
                
                
                // Price
                if($transaction->price != '') {
                    if($service->service_code == 1) {
                        
                        $dur = ceil(intval($transaction->meeting_duration) / 60);
                        $final_price = $dur * $transaction->price;
                        
                        $template_transactions[$transaction->id]['price'] = number_format(floatval($final_price), 2, '.', '');
                        $transactions_total += number_format(floatval($final_price), 2, '.', '');
                    } else if($service->service_code == 19) {
                        $final_price = $transaction->meeting_duration * 0.50;
                        $template_transactions[$transaction->id]['price'] = number_format(floatval($final_price), 2, '.', '');
                        $transactions_total += number_format(floatval($final_price), 2, '.', '');
                    } else {
                        $template_transactions[$transaction->id]['price'] = number_format(floatval($transaction->price), 2, '.', '');
                        $transactions_total += number_format(floatval($transaction->price), 2, '.', '');
                    }
                }
            }
            
        }
        
        
        $transactions_misc = TransactionMisc::findBy(['psychologist = ?'], [$member->id]);
        foreach($transactions_misc as $transaction) {
            
            // Get the current month and current year as two digit numbers
            $last_month = date('m');
            $current_year = date('y');
            $transaction_month = date('m', $transaction->date_submitted);
            $transaction_year = date('y', $transaction->date_submitted);
            
            //echo "Last Month: " . $last_month . "<br>";
            //echo "Current Year: " . $current_year . "<br>";
            //echo "Trans Month: " . $transaction_month . "<br>";
            //echo "Trans Year: " . $transaction_year . "<br>";
            
            if($transaction_year == $current_year && $transaction_month == $last_month) {

                $assignment = Assignment::findOneBy('id', $transaction->pid);
                
                $template_transactions_misc[$transaction->id]['id'] = $transaction->id;
                $template_transactions_misc[$transaction->id]['transaction_type'] = "transaction_misc";
                $template_transactions_misc[$transaction->id]['date_submitted'] = date('m_d_y', $transaction->date_submitted);
    
                // Reviewed
                if($transaction->published == '1')
                    $template_transactions_misc[$transaction->id]['reviewed'] = 'Reviewed';
                else
                    $template_transactions_misc[$transaction->id]['reviewed'] = 'Unreviewed';
                
                // District
                $district = District::findOneBy('id', $transaction->district);
                $template_transactions_misc[$transaction->id]['district'] = $district->district_name;
                // School
                $school = School::findOneBy('id', $transaction->school);
                $template_transactions_misc[$transaction->id]['school'] = $school->school_name;
    
                
                // Student
                //$student = Student::findOneBy('id', $transaction->student );
                
                $template_transactions_misc[$transaction->id]['student'] = $transaction->student_initials;
                // Lasid
                $template_transactions_misc[$transaction->id]['lasid'] = $transaction->lasid;
                // Sasid
                $template_transactions_misc[$transaction->id]['sasid'] = $transaction->sasid;
                
                // Service
                //$template_transactions_misc[$transaction->id]['service'] = $transaction->service_label;
                
                // Service
                $service = Service::findOneBy('service_code', $transaction->service);
                if($transaction->meeting_duration > 0) {
                    $template_transactions_misc[$transaction->id]['service'] = $service->name . " (" . $transaction->meeting_duration . " minutes)";
                } else {
                    $template_transactions_misc[$transaction->id]['service'] = $service->name;
                }
                
                // Price
                if($transaction->price != '') {
                    if($service->service_code == 1) {
                        $dur = ceil(intval($transaction->meeting_duration) / 60);
                        $final_price = $dur * $transaction->price;
                        
                        $template_transactions_misc[$transaction->id]['price'] = number_format(floatval($final_price), 2, '.', '');
                        $transactions_total += number_format(floatval($final_price), 2, '.', '');
                    } else if($service->service_code == 19) {
                        
                        
                        $final_price = $transaction->meeting_duration * 0.50;
                        $template_transactions_misc[$transaction->id]['price'] = number_format(floatval($final_price), 2, '.', '');
                        $transactions_total += number_format(floatval($final_price), 2, '.', '');
                    } else {
                        $template_transactions_misc[$transaction->id]['price'] = number_format(floatval($transaction->price), 2, '.', '');
                        $transactions_total += number_format(floatval($transaction->price), 2, '.', '');
                    }
                }
            }
            
        }
        
        $this->Template->transactions = $template_transactions;
        $this->Template->transactions_misc = $template_transactions_misc;
        $this->Template->transactions_total = number_format($transactions_total, 2, '.', '');
        
    }
  

}
