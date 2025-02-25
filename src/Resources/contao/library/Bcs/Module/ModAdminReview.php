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
    protected static $template_misc = array();
    protected static $template_psychologist_names = array();
    protected static $template_totals = array();
    protected static $template_active = array();

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
        
        // Check to see if we have an active psychologist in our URL
        $active_id = 0;
        if (isset($_GET['active'])) {
            $active_id = $_GET['active'];
        }
        
        // Get the current month and current year as two digit numbers
        $current_year = date('y');
        $last_month = date('m', strtotime('-1 month'));
        $currently_reviewing = date('F, Y', strtotime('-1 month'));
        //$last_month = date('m');
        //$currently_reviewing = date('F, Y');
        

        // Get all active Psychologists
        $opt = ['order' => 'firstname ASC'];
        $psychologists = MemberModel::findBy('disable', '0', $opt);
        
        // loop through each
        $first = true;
        foreach($psychologists as $psy) {
            
            // Get Transactions
            $transactions = Transaction::findBy(['psychologist = ?', 'published = ?'], [$psy->id, 1]);
            $transactions_total = 0.00;
        
            foreach($transactions as $transaction) {
                
                $transaction_month = date('m', $transaction->date_submitted);
                $transaction_year = date('y', $transaction->date_submitted);
                
                //echo "Last Month: " . $last_month . "<br>";
                //echo "Current Year: " . $current_year . "<br>";
                //echo "Trans Month: " . $transaction_month . "<br>";
                //echo "Trans Year: " . $transaction_year . "<br>";
                
                if($transaction_year == $current_year && $transaction_month == $last_month) {
                    
                    $template_psychologist_names[$psy->id] = $psy->firstname . " " . $psy->lastname;
                    
    
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
                    $template_psychologists[$psy->id][$transaction->id]['district'] .= "<select name='district_$assignment->id' class='district' id='district_$assignment->id'>";
                    $template_psychologists[$psy->id][$transaction->id]['district'] .= "<option value='' selected disabled>Select a District</option>";
                    $districts = District::findAll();
                    foreach($districts as $district) {
                        if($assignment->district == $district->id)
                            $template_psychologists[$psy->id][$transaction->id]['district'] .= "<option value='" . $district->id . "' selected>$district->district_name</option>";
                        else
                            $template_psychologists[$psy->id][$transaction->id]['district'] .= "<option value='" . $district->id . "'>$district->district_name</option>";
                    }
                    $template_psychologists[$psy->id][$transaction->id]['district'] .= "</select>";


                    
                    // School
                    $template_psychologists[$psy->id][$transaction->id]['school'] .= "<select name='school_$assignment->id' class='school' id='school_$assignment->id'>";
                    $template_psychologists[$psy->id][$transaction->id]['school'] .= "<option value='' selected disabled>First, select a District</option>";
                    $schools = School::findAll();
                    foreach($schools as $school) {
                        if($school->pid == $assignment->district) {
                            if($assignment->school == $school->id)
                                $template_psychologists[$psy->id][$transaction->id]['school'] .= "<option value='" . $school->id . "' selected>$school->school_name</option>";
                            else
                                $template_psychologists[$psy->id][$transaction->id]['school'] .= "<option value='" . $school->id . "'>$school->school_name</option>";
                        }
                    }
                    $template_psychologists[$psy->id][$transaction->id]['school'] .= "</select>";


                    
                    // Student
                    $template_psychologists[$psy->id][$transaction->id]['student'] .= "<select name='student_$assignment->id' class='student' id='student_$assignment->id'>";
                    $template_psychologists[$psy->id][$transaction->id]['student'] .= "<option value='' selected disabled>Select a Student</option>";
                    $students = Student::findAll();
                    foreach($students as $student) {
                        if($student->district == $assignment->district) {
                            if($assignment->student == $student->id) {
                                $template_psychologists[$psy->id][$transaction->id]['student'] .= "<option value='" . $student->id . "' selected>$student->name</option>";
                                $template_psychologists[$psy->id][$transaction->id]['lasid'] = $student->lasid;
                                $template_psychologists[$psy->id][$transaction->id]['sasid'] = $student->sasid;
                            }
                            else
                                $template_psychologists[$psy->id][$transaction->id]['student'] .= "<option value='" . $student->id . "'>$student->name</option>";
                        }
                    }
                    $template_psychologists[$psy->id][$transaction->id]['student'] .= "</select>";



                    // Service - OLD
                    $service = Service::findOneBy('service_code', $transaction->service);
                    if($transaction->meeting_duration > 0) {
                        $template_psychologists[$psy->id][$transaction->id]['service'] = $service->name . " (" . $transaction->meeting_duration . " minutes)";
                    } else {
                        $template_psychologists[$psy->id][$transaction->id]['service'] = $service->name;
                    }


                    
                    // Price
                    if($transaction->price != '') {
                        
                        $template_psychologists[$psy->id][$transaction->id]['rate'] = number_format(floatval($transaction->price), 2, '.', ',');
                            
                        if($transaction->service == 1) {
                            
                            $dur = ceil(intval($transaction->meeting_duration) / 60);
                            $final_price = $dur * $transaction->price;
                            
                            //$template_psychologists[$psy->id][$transaction->id]['price'] = "<input value='".number_format(floatval($final_price), 2, '.', '')."' name='price_$transaction->id' class='price' id='price_$transaction->id' autocomplete='off'>";
                            $template_psychologists[$psy->id][$transaction->id]['price'] = number_format(floatval($final_price), 2, '.', ',');
                            
                            $transactions_total += number_format(floatval($final_price), 2, '.', '');
                        } else if($transaction->service == 19) {
                            $final_price = $transaction->meeting_duration * 0.50;
                            
                            //$template_psychologists[$psy->id][$transaction->id]['price'] = "<input value='".number_format(floatval($final_price), 2, '.', '')."' name='price_$transaction->id' class='price' id='price_$transaction->id' autocomplete='off'>";
                            $template_psychologists[$psy->id][$transaction->id]['price'] = number_format(floatval($final_price), 2, '.', ',');
                            
                            $transactions_total += number_format(floatval($final_price), 2, '.', '');
                        } else {
                            //$template_psychologists[$psy->id][$transaction->id]['price'] = "<input value='".number_format(floatval($transaction->price), 2, '.', '')."' name='price_$transaction->id' class='price' id='price_$transaction->id' autocomplete='off'>";
                            $template_psychologists[$psy->id][$transaction->id]['price'] = number_format(floatval($transaction->price), 2, '.', ',');
                            $transactions_total += number_format(floatval($transaction->price), 2, '.', '');
                        }
                        
                    }
                }
                
                
            }
            
            
            $transactions_misc = TransactionMisc::findBy(['psychologist = ?', 'published = ?'], [$psy->id, 1]);

            foreach($transactions_misc as $transaction) {
                
                $transaction_month = date('m', $transaction->date_submitted);
                $transaction_year = date('y', $transaction->date_submitted);
                
                //echo "Last Month: " . $last_month . "<br>";
                //echo "Current Year: " . $current_year . "<br>";
                //echo "Trans Month: " . $transaction_month . "<br>";
                //echo "Trans Year: " . $transaction_year . "<br>";
                
                if($transaction_year == $current_year && $transaction_month == $last_month) {
                    
                    $template_psychologist_names[$psy->id] = $psy->firstname . " " . $psy->lastname;
                    
                    $template_psychologists[$psy->id]['m_'.$transaction->id]['psychologist_name'] = $psy->firstname . " " . $psy->lastname;
                    $template_psychologists[$psy->id]['m_'.$transaction->id]['id'] = $transaction->id;
                    $template_psychologists[$psy->id]['m_'.$transaction->id]['transaction_type'] = "transaction_misc";
                    $template_psychologists[$psy->id]['m_'.$transaction->id]['date_submitted'] = date('m_d_y', $transaction->date_submitted);

                    
                    
                    // District
                    $district = District::findOneBy('id', $transaction->district);
                    $template_psychologists[$psy->id]['m_'.$transaction->id]['district'] = $district->district_name;
                    
                    // School
                    $school = School::findOneBy('id', $transaction->school);
                    $template_psychologists[$psy->id]['m_'.$transaction->id]['school'] = $school->school_name;
                    
                    $template_psychologists[$psy->id]['m_'.$transaction->id]['student'] = $transaction->student_initials;
                    // Lasid
                    $template_psychologists[$psy->id]['m_'.$transaction->id]['lasid'] = $transaction->lasid;
                    // Sasid
                    $template_psychologists[$psy->id]['m_'.$transaction->id]['sasid'] = $transaction->sasid;
                    
                    // Service
                    $service = Service::findOneBy('service_code', $transaction->service);
                    if($transaction->meeting_duration > 0) {
                        $template_psychologists[$psy->id]['m_'.$transaction->id]['service'] = $service->name . " (" . $transaction->meeting_duration . " minutes)";
                    } else {
                        $template_psychologists[$psy->id]['m_'.$transaction->id]['service'] = $service->name;
                    }
                    
                    // Price
                    if($transaction->price != '') {
                        
                        $template_psychologists[$psy->id]['m_'.$transaction->id]['rate'] = number_format(floatval($transaction->price), 2, '.', ',');
                            
                        if($transaction->service == 1) {
                            $dur = ceil(intval($transaction->meeting_duration) / 60);
                            $final_price = $dur * $transaction->price;
                            $template_psychologists[$psy->id]['m_'.$transaction->id]['price'] = number_format(floatval($final_price), 2, '.', ',');
                            $transactions_total += number_format(floatval($final_price), 2, '.', '');
                        } else if($transaction->service == 19) {
                            $final_price = $transaction->meeting_duration * 0.50;
                            $template_psychologists[$psy->id]['m_'.$transaction->id]['price'] = number_format(floatval($final_price), 2, '.', ',');
                            $transactions_total += number_format(floatval($final_price), 2, '.', '');
                        } else {
                            $template_psychologists[$psy->id]['m_'.$transaction->id]['price'] = number_format(floatval($transaction->price), 2, '.', ',');
                            $transactions_total += number_format(floatval($transaction->price), 2, '.', '');
                        }
                        
                    }
                }
                
            }
            
            
            
            
            
            
            
            
            
            $template_totals[$psy->id] = number_format(floatval($transactions_total), 2, '.', ',');
            
            if($active_id == 0 && $first == true && $transactions != null) {
                $template_active[$psy->id] = 'active';
                $first = false;
            }
            else if($psy->id == $active_id)
                $template_active[$psy->id] = 'active';
        }

        $this->Template->currently_reviewing = $currently_reviewing;
        $this->Template->totals = $template_totals;
        $this->Template->psychologist_names = $template_psychologist_names;
        $this->Template->psychologists = $template_psychologists;
        //$this->Template->transactions_misc = $template_misc;
        $this->Template->psychologists_active = $template_active;
    }
  
}
