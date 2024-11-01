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
use Contao\MemberModel;
use Contao\System;
use Contao\FrontendUser;


class ModAdminReview extends \Contao\Module
{

    /* Default Template */
    protected $strTemplate = 'mod_admin_review';
    
    /* Stores our table data per Psychologist */    
    protected static $template_psychologists = array();
    protected static $template_totals = array();

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


        // Get all active Psychologists
        $opt = ['order' => 'firstname ASC'];
        $psychologists = MemberModel::findBy('disable', '0', $opt);
        
        // loop through each
        foreach($psychologists as $psy) {
            
            // Get Transactions
            $transactions = Transaction::findBy(['psychologist = ?'], [$psy->id]);
            $transactions_total = 0.00;
        
            foreach($transactions as $transaction) {
                
                // Get the current month and current year as two digit numbers
                $last_month = date('m', strtotime('-1 month'));
                $current_year = date('y');
                $transaction_month = date('m', $transaction->date_submitted);
                $transaction_year = date('y', $transaction->date_submitted);
                
                //echo "Last Month: " . $last_month . "<br>";
                //echo "Current Year: " . $current_year . "<br>";
                //echo "Trans Month: " . $transaction_month . "<br>";
                //echo "Trans Year: " . $transaction_year . "<br>";
                
                if($transaction_year == $current_year && $transaction_month == $last_month) {
    
                    $assignment = Assignment::findOneBy('id', $transaction->pid);
                    
                    $template_psychologists[$psy->id][$transaction->id]['psychologist_name'] = $psy->firstname . " " . $psy->lastname;
                    $template_psychologists[$psy->id][$transaction->id]['id'] = $transaction->id;
                    $template_psychologists[$psy->id][$transaction->id]['transaction_type'] = "transaction";
                    $template_psychologists[$psy->id][$transaction->id]['date_submitted'] = date('m_d_y', $transaction->date_submitted);
        
                    // Reviewed
                    if($transaction->published == '1')
                        $template_psychologists[$psy->id][$transaction->id]['reviewed'] = 'Reviewed';
                    else
                        $template_psychologists[$psy->id][$transaction->id]['reviewed'] = 'Unreviewed';
                    
                    // District
                    $district = District::findOneBy('id', $assignment->district);
                    $template_psychologists[$psy->id][$transaction->id]['district'] = $district->district_name;
                    // School
                    $school = School::findOneBy('id', $assignment->school);
                    $template_psychologists[$psy->id][$transaction->id]['school'] = $school->school_name;
                    // Student
                    $student = Student::findOneBy('id', $assignment->student );
                    $template_psychologists[$psy->id][$transaction->id]['student'] = $student->name;
                    // Lasid
                    $template_psychologists[$psy->id][$transaction->id]['lasid'] = $student->lasid;
                    // Sasid
                    $template_psychologists[$psy->id][$transaction->id]['sasid'] = $student->sasid;
                    
                    // Service
                    $service = Service::findOneBy('service_code', $transaction->service);
                    if($transaction->meeting_duration > 0) {
                        $template_psychologists[$psy->id][$transaction->id]['service'] = $service->name . " (" . $transaction->meeting_duration . " minutes)";
                    } else {
                        $template_psychologists[$psy->id][$transaction->id]['service'] = $service->name;
                    }
                    
                    
                    // Price
                    if($transaction->price != '') {
                        if($service->service_code == 1) {
                            
                            $dur = ceil(intval($transaction->meeting_duration) / 60);
                            $final_price = $dur * $transaction->price;
                            
                            $template_psychologists[$psy->id][$transaction->id]['price'] = number_format(floatval($final_price), 2, '.', '');
                            $transactions_total += number_format(floatval($final_price), 2, '.', '');
                        } else if($service->service_code == 19) {
                            $final_price = $transaction->meeting_duration * 0.50;
                            $template_psychologists[$psy->id][$transaction->id]['price'] = number_format(floatval($final_price), 2, '.', '');
                            $transactions_total += number_format(floatval($final_price), 2, '.', '');
                        } else {
                            $template_psychologists[$psy->id][$transaction->id]['price'] = number_format(floatval($transaction->price), 2, '.', '');
                            $transactions_total += number_format(floatval($transaction->price), 2, '.', '');
                        }
                    }
                }
                
                
            }
            $template_totals[$psy->id] = $transactions_total;
            
        }

        $this->Template->totals = $template_totals;
        $this->Template->psychologists = $template_psychologists;
        
    }
  

}
