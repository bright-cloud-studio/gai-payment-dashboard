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
use Contao\Input;
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
    protected static $template_last_reviewed = array();

    /* Construct function */
    public function __construct($objModule, $strColumn='main')
	{
        //parent::__construct($objModule, $strColumn);
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
        
        // First, get the current month
        $current_month  = date('m');
        $current_year  = date('y');
        
        // Next, if the current month is 1, we need "current year" to actually be one year back
        $reviewing_year;
        if($current_month == 1)
            $reviewing_year = date('y') - 1;
        else
            $reviewing_year = date('y');
        
        $reviewing_month = '';
        // If the month is June and we are at the 15th day or greater into the month, review this month as it's the end of the year
        // otherwise, review the last month
        if(date('n') == 6 && date('j') >= 15)
            $reviewing_month = date('m');
        else
            $reviewing_month = date('m', strtotime('-1 month'));

        $currently_reviewing = date('F, Y', strtotime('-1 month'));
        
        // Get all active Psychologists
        $psy_name = '';
        $psy_id = 0;
        if (Input::get('psychologist') != '')
            $psy_id = Input::get('psychologist');
        $opt = ['order' => 'firstname ASC'];
        $psychologists = MemberModel::findBy('disable', '0', $opt);
        foreach($psychologists as $psy) {
            
            $transactions_total = 0.00;
            
            // Get Transactions
            $transactions = Transaction::findBy(['psychologist = ?', 'published = ?'], [$psy->id, 1]);
            foreach($transactions as $transaction) {
                
                $transaction_month = date('m', $transaction->date_submitted);
                $transaction_year = date('y', $transaction->date_submitted);
                
                if($transaction_year == $reviewing_year && $transaction_month == $reviewing_month) {
                    
                    if($psy->id != $psy_id) {
                        $template_psychologist_names[$psy->id]['name'] = $psy->firstname . " " . $psy->lastname;
                        
                        $reviewed_month = date("m", (int)$psy->last_reviewed);
                        $reviewed_year = date("y", (int)$psy->last_reviewed);
                        
                        // Change the name to green with the "reviewed" class if the Last Reviewed mm/yy matches the current mm/yy
                        if($reviewed_year == $current_year && $reviewed_month == $current_month) {
                            $template_psychologist_names[$psy->id]['class'] = "reviewed";
                        } else
                            $template_psychologist_names[$psy->id]['class'] = "";

                    } else {
                        $psy_name = $psy->firstname . " " . $psy->lastname;
                        $template_psychologist_names[$psy->id]['name'] = $psy->firstname . " " . $psy->lastname;
                        
                        $reviewed_month = date("m", (int)$psy->last_reviewed);
                        $reviewed_year = date("y", (int)$psy->last_reviewed);
                        
                        // Change the name to green with the "reviewed" class if the Last Reviewed mm/yy matches the current mm/yy
                        if($reviewed_year == $current_year && $reviewed_month == $current_month) {
                            $template_psychologist_names[$psy->id]['class'] = "reviewed";
                        } else
                            $template_psychologist_names[$psy->id]['class'] = "";

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
                        $district = District::findOneBy('id', $transaction->district);
                        $template_psychologists[$psy->id][$transaction->id]['district'] = $district->district_name;
    
                        // School
                        $school = School::findOneBy('id', $assignment->school);
                        $template_psychologists[$psy->id][$transaction->id]['school'] = $school->school_name;

                        // Student
                        $student = Student::findOneBy('id', $assignment->student);
                        $template_psychologists[$psy->id][$transaction->id]['student'] = $student->name;
                        $template_psychologists[$psy->id][$transaction->id]['lasid'] = $student->lasid;
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
                            
                            $template_psychologists[$psy->id][$transaction->id]['rate'] = number_format(floatval($transaction->price), 2, '.', ',');
                                
                            if($transaction->service == 1) {
                                $dur = ceil(intval($transaction->meeting_duration) / 60);
                                $final_price = $dur * $transaction->price;
                                $template_psychologists[$psy->id][$transaction->id]['price'] = number_format(floatval($final_price), 2, '.', ',');
                                $transactions_total += number_format(floatval($final_price), 2, '.', '');
                            } else if($transaction->service == 19) {
                                $final_price = $transaction->meeting_duration * 0.50;
                                $template_psychologists[$psy->id][$transaction->id]['price'] = number_format(floatval($final_price), 2, '.', ',');
                                $transactions_total += number_format(floatval($final_price), 2, '.', '');
                            } else {
                                $template_psychologists[$psy->id][$transaction->id]['price'] = number_format(floatval($transaction->price), 2, '.', ',');
                                $transactions_total += number_format(floatval($transaction->price), 2, '.', '');
                            }
                            
                        }
                    }
                }
                
            }
            
            $transactions_misc = TransactionMisc::findBy(['psychologist = ?', 'published = ?'], [$psy->id, 1]);
            foreach($transactions_misc as $transaction) {
                
                $transaction_month = date('m', $transaction->date_submitted);
                $transaction_year = date('y', $transaction->date_submitted);
                
                if($transaction_year == $reviewing_year && $transaction_month == $reviewing_month) {
                    
                    if($psy->id != $psy_id) {
                        $template_psychologist_names[$psy->id]['name'] = $psy->firstname . " " . $psy->lastname;
                        
                        $reviewed_month = date("m", (int)$psy->last_reviewed);
                        $reviewed_year = date("y", (int)$psy->last_reviewed);
                        
                        // Change the name to green with the "reviewed" class if the Last Reviewed mm/yy matches the current mm/yy
                        if($reviewed_year == $current_year && $reviewed_month == $current_month) {
                            $template_psychologist_names[$psy->id]['class'] = "reviewed";
                        } else
                            $template_psychologist_names[$psy->id]['class'] = "";

                    } else {
                        $psy_name = $psy->firstname . " " . $psy->lastname;
                        $template_psychologist_names[$psy->id]['name'] = $psy->firstname . " " . $psy->lastname;
                        
                        $reviewed_month = date("m", (int)$psy->last_reviewed);
                        $reviewed_year = date("y", (int)$psy->last_reviewed);
                        
                        // Change the name to green with the "reviewed" class if the Last Reviewed mm/yy matches the current mm/yy
                        if($reviewed_year == $current_year && $reviewed_month == $current_month) {
                            $template_psychologist_names[$psy->id]['class'] = "reviewed";
                        } else
                            $template_psychologist_names[$psy->id]['class'] = "";

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
                            } else if($transaction->service == 19 && $transaction->meeting_duration != '') {
                                $final_price = (int)$transaction->meeting_duration * 0.50;
                                $template_psychologists[$psy->id]['m_'.$transaction->id]['price'] = number_format(floatval($final_price), 2, '.', ',');
                                $transactions_total += number_format(floatval($final_price), 2, '.', '');
                            } else {
                                $template_psychologists[$psy->id]['m_'.$transaction->id]['price'] = number_format(floatval($transaction->price), 2, '.', ',');
                                $transactions_total += number_format(floatval($transaction->price), 2, '.', '');
                            }
                            
                        }
                    }
                }
                
            }
   
            $template_totals[$psy->id] = number_format(floatval($transactions_total), 2, '.', ',');
            $template_last_reviewed[$psy->id] = date('m/d/y', (int)$psy->last_reviewed);
        }

        $this->Template->active_psy_name = $psy_name;
        $this->Template->active_psy_id = $psy_id;

        $this->Template->currently_reviewing = $currently_reviewing;
        $this->Template->totals = $template_totals;
        $this->Template->psychologist_names = $template_psychologist_names;
        $this->Template->psychologists = $template_psychologists;
        $this->Template->psychologists_active = $template_active;
        $this->Template->last_reviewed = $template_last_reviewed;
    }
  
}
