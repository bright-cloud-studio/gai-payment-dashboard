<?php

namespace Bcs\Hooks;

use Bcs\Model\Assignment;
use Bcs\Model\District;
use Bcs\Model\ReviewRecord;
use Bcs\Model\School;
use Bcs\Model\Student;
use Bcs\Model\Service;
use Bcs\Model\Transaction;
use Bcs\Model\TransactionMisc;

use Contao\Controller;
use Contao\Database;
use Contao\Environment;
use Contao\FrontendUser;
use Contao\Input;
use Contao\MemberModel;
use Contao\PageModel;
use Contao\Template;

class TemplateHooks
{
    protected static $arrUserOptions = array();
    
    public $transactions_today = array();

    // When a form is submitted
    public function onParseTemplate($template)
    {
        if ('be_welcome' === $template->getName()) {
            
            // Set 'Today' total
            $template->total_day = $this->calculateDay();
            $template->total_day_last_year = $this->calculateDayLastYear();
            
            // Set 'This Month' total
            $template->total_this_month = $this->calculateThisMonth();
            $template->total_this_month_last_year = $this->calculateThisMonthLastYear();
            
            // Set 'Last Month' total
            $template->total_last_month = $this->calculateLastMonth();
            $template->total_last_month_last_year = $this->calculateLastMonthLastYear();
            
            // Set 'Year' total
            $template->total_year = $this->calculateYear();
            $template->total_year_last_year = $this->calculateLastYear();
            
            $template->transactions_today = $transactions_today;
            
            $template->last_review_and_submit = $this->getLastReviewedAndSubmitted();
            $template->review_status_this_month =  $this->getReviewStatuses("this_month");
            $template->review_status_last_month =  $this->getReviewStatuses("last_month");
            
            $template->assignments_this_month = $this->calculateAssignmentsMonth("this_month");
            $template->assignments_last_month = $this->calculateAssignmentsMonth("last_month");
            $template->assignments_this_year = $this->calculateAssignmentsYear("this_year");
            $template->assignments_last_year = $this->calculateAssignmentsYear("last_year");
            
        }
    }
    
    
    // Calculates Transaction totals for "Today"
    public function calculateDay() {
        
        // Tracks the total price of the Transactions
        $total_price_psychologists = 0.00;
        $total_price_districts = 0.00;
        // Tracks the total number of Transactions
        $total_transactions = 0;
        // Tracks the total number of Misc. Transactions
        $total_transactions_misc = 0;
        // Get Today's Date
        $today = date('m/d/y');
    
        // Loop through Transactions
        $transactions_today = array();
        $options = [
    		'order' => 'psychologist ASC'
    	];
        $transactions = Transaction::findAll($options);
        while ($transactions->next())
		{
		    // If 'date_submitted' is today
            $transaction_date = date('m/d/y', $transactions->date_submitted);
            if($transaction_date == $today) {
                
                // Update our Total Tranactions
                $total_transactions += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                $price = $service->{$psychologist->price_tier};
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
			    $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    $total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    $total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
                
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_today[$transactions->id]['service'] = $service->service_code . " - " .$service->name;
    		    $transactions_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}

		// Loop through Misc. Transactions
		$transactions_misc_today = array();
        $transactions = TransactionMisc::findAll();
        while ($transactions->next())
		{
		    // If 'date_submitted' is today
            $transaction_date = date('m/d/y', $transactions->date_submitted);
            if($transaction_date == $today) {
                
                // Update our Total Tranactions
                $total_transactions_misc += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                
                $price = 0.00;
    			if($transactions->price != '')
    				$price = $transactions->price;
    			else {
    				$price = $service->{$psychologist->price_tier};
    			}
    			
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
			    $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    $total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 14) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    $total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
            
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_misc_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_misc_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_misc_today[$transactions->id]['service'] = $service->name;
    		    $transactions_misc_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_misc_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}

        // Return our template values for 'total_day'
        return array(
            'transactions_today' => $transactions_today,
            'transactions_misc_today' => $transactions_misc_today,
            'total_psycholigists' => number_format($total_price_psychologists, 2, '.', ','),
            'total_districts' => number_format(floatval($total_price_districts), 2, '.', ','),
            'transactions' => $total_transactions,
            'transactions_misc' => $total_transactions_misc
        );
    }
    
    // Calculates Transaction Totals for "today" but last year
    public function calculateDayLastYear() {
        
        // Tracks the total price of the Transactions
        $total_price_psychologists = 0.00;
        $total_price_districts = 0.00;
        // Tracks the total number of Transactions
        $total_transactions = 0;
        // Tracks the total number of Misc. Transactions
        $total_transactions_misc = 0;
        // Get Today's Date
        $today = date('m/d/y', strtotime('-1 year'));
    
        // Loop through Transactions
        $transactions_today = array();
        $options = [
    		'order' => 'psychologist ASC'
    	];
        $transactions = Transaction::findAll($options);
        while ($transactions->next())
		{
		    // If 'date_submitted' is today
            $transaction_date = date('m/d/y', $transactions->date_submitted);
            if($transaction_date == $today) {
                
                // Update our Total Tranactions
                $total_transactions += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                $price = $service->{$psychologist->price_tier};
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
			    $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    $total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    $total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
                
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_today[$transactions->id]['service'] = $service->service_code . " - " .$service->name;
    		    $transactions_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}

		// Loop through Misc. Transactions
		$transactions_misc_today = array();
        $transactions = TransactionMisc::findAll();
        while ($transactions->next())
		{
		    // If 'date_submitted' is today
            $transaction_date = date('m/d/y', $transactions->date_submitted);
            if($transaction_date == $today) {
                
                // Update our Total Tranactions
                $total_transactions_misc += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                
                $price = 0.00;
    			if($transactions->price != '')
    				$price = $transactions->price;
    			else {
    				$price = $service->{$psychologist->price_tier};
    			}
    			
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
			    $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    $total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 14) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    $total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
            
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_misc_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_misc_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_misc_today[$transactions->id]['service'] = $service->name;
    		    $transactions_misc_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_misc_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}

        // Return our template values for 'total_day'
        return array(
            'transactions_today' => $transactions_today,
            'transactions_misc_today' => $transactions_misc_today,
            'total_psycholigists' => number_format($total_price_psychologists, 2, '.', ','),
            'total_districts' => number_format(floatval($total_price_districts), 2, '.', ','),
            'transactions' => $total_transactions,
            'transactions_misc' => $total_transactions_misc
        );
    }
    
    
    
    
    
    // Returns totals for Transactions and Misc. Transactions matching the current month
    public function calculateThisMonth() {
        
        // Tracks the total price of the Transactions
        $total_price_psychologists = 0.00;
        $total_price_districts = 0.00;
        // Tracks the total number of Transactions
        $total_transactions = 0;
        // Tracks the total number of Misc. Transactions
        $total_transactions_misc = 0;
        // Get the Month
        $month = date('m');
        // Get the Year
        $year = date('Y');
    
        // Loop through Transactions
        $transactions_today = array();
        
        $options = [
    		'order' => 'psychologist ASC'
    	];
	
        $transactions = Transaction::findAll();
        while ($transactions->next())
		{
		    // If 'date_submitted' is today
            $transaction_month = date('m', $transactions->date_submitted);
            $transaction_year = date('Y', $transactions->date_submitted);
            
            if($transaction_month == $month  && $transaction_year == $year) {
                
                // Update our Total Tranactions
                $total_transactions += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                
                $price = $service->{$psychologist->price_tier};
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
			    $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    $total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    $total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
                
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_today[$transactions->id]['service'] = $service->service_code . " - " .$service->name;
    		    $transactions_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}

		// Loop through Misc. Transactions
		$transactions_misc_today = array();
        $transactions = TransactionMisc::findAll($options);
        while ($transactions->next())
		{
            $transaction_month = date('m', $transactions->date_submitted);
            $transaction_year = date('Y', $transactions->date_submitted);
            if($transaction_month == $month && $transaction_year == $year) {
                
                // Update our Total Tranactions
                $total_transactions_misc += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                
                //$price = $service->{$psychologist->price_tier};
                $price = 0.00;
    			if($transactions->price != '')
    				$price = $transactions->price;
    			else {
    				$price = $service->{$psychologist->price_tier};
    			}
                
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
			    $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    $total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 14) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    $total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
            
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_misc_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_misc_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_misc_today[$transactions->id]['service'] = $service->name;
    		    $transactions_misc_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_misc_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}

        // Return our template values for 'total_day'
        return array(
            'transactions_today' => $transactions_today,
            'transactions_misc_today' => $transactions_misc_today,
            'total_psycholigists' => number_format($total_price_psychologists, 2, '.', ','),
            'total_districts' => number_format(floatval($total_price_districts), 2, '.', ','),
            'transactions' => $total_transactions,
            'transactions_misc' => $total_transactions_misc
        );
    }
    
    // Returns totals for Transactions and Misc. Transactions matching the current month
    public function calculateThisMonthLastYear() {
        
        // Tracks the total price of the Transactions
        $total_price_psychologists = 0.00;
        $total_price_districts = 0.00;
        // Tracks the total number of Transactions
        $total_transactions = 0;
        // Tracks the total number of Misc. Transactions
        $total_transactions_misc = 0;
        // Get the Month
        $month = date('m');
        // Get the Year
        $year = date('Y') - 1;
        
        // Loop through Transactions
        $transactions_today = array();
        
        $options = [
    		'order' => 'psychologist ASC'
    	];
	
        $transactions = Transaction::findAll();
        while ($transactions->next())
		{
		    // If 'date_submitted' is today
            $transaction_month = date('m', $transactions->date_submitted);
            $transaction_year = date('Y', $transactions->date_submitted);
            
            if($transaction_month == $month && $transaction_year == $year) {
                
                // Update our Total Tranactions
                $total_transactions += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                
                $price = $service->{$psychologist->price_tier};
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
			    $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    $total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    $total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
                
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_today[$transactions->id]['service'] = $service->service_code . " - " .$service->name;
    		    $transactions_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}

		// Loop through Misc. Transactions
		$transactions_misc_today = array();
        $transactions = TransactionMisc::findAll($options);
        while ($transactions->next())
		{
            $transaction_month = date('m', $transactions->date_submitted);
            $transaction_year = date('Y', $transactions->date_submitted);
            
            if($transaction_month == $month && $transaction_year == $year) {
                
                // Update our Total Tranactions
                $total_transactions_misc += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                
                //$price = $service->{$psychologist->price_tier};
                $price = 0.00;
    			if($transactions->price != '')
    				$price = $transactions->price;
    			else {
    				$price = $service->{$psychologist->price_tier};
    			}
                
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
			    $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    $total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 14) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    $total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
            
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_misc_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_misc_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_misc_today[$transactions->id]['service'] = $service->name;
    		    $transactions_misc_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_misc_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}

        // Return our template values for 'total_day'
        return array(
            'transactions_today' => $transactions_today,
            'transactions_misc_today' => $transactions_misc_today,
            'total_psycholigists' => number_format($total_price_psychologists, 2, '.', ','),
            'total_districts' => number_format(floatval($total_price_districts), 2, '.', ','),
            'transactions' => $total_transactions,
            'transactions_misc' => $total_transactions_misc
        );
    }
    
    
    
    
    
    
    
    
    
    // Returns totals for Transactions and Misc. Transactions matching the current month
    public function calculateLastMonth() {
        
        // Tracks the total price of the Transactions
        $total_price_psychologists = 0.00;
        $total_price_districts = 0.00;
        // Tracks the total number of Transactions
        $total_transactions = 0;
        // Tracks the total number of Misc. Transactions
        $total_transactions_misc = 0;
        // Get the Last Month
        $currentDate = new \DateTime();
        $lastMonth = $currentDate->modify('first day of last month');
        $last_month = $lastMonth->format('m/y');
    
        // Loop through Transactions
        $transactions_today = array();
        
        $options = [
            'order' => 'psychologist ASC'
        ];
        
        $transactions = Transaction::findAll($options);
        while ($transactions->next())
		{
		    // If 'date_submitted' is today
            $transaction_month = date('m/y', $transactions->date_submitted);
            if($transaction_month == $last_month) {
                
                // Update our Total Tranactions
                $total_transactions += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);

                $assignment = Assignment::findOneBy('id', $transactions->pid);
                $district = District::findOneBy('id', $assignment->district);
                $school = School::findOneBy('id', $assignment->school);
                $student = Student::findOneBy('id', $assignment->student);
                
                $price = $service->{$psychologist->price_tier};
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
                $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    //$total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    //$total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    //$total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    //$total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
                
                $clean_price_d = str_replace(',', '', $add_to_total_district);
                $total_price_districts = $total_price_districts + $clean_price_d;
                
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_today[$transactions->id]['service'] = $service->service_code . " - " .$service->name;
    		    
    		    $transactions_today[$transactions->id]['district'] = $district->district_name;
    		    $transactions_today[$transactions->id]['school'] = $school->school_name;
    		    $transactions_today[$transactions->id]['student'] = $student->name;
    		    $transactions_today[$transactions->id]['lasid'] = $student->lasid;
    		    $transactions_today[$transactions->id]['sasid'] = $student->sasid;
    		    
    		    
    		    $transactions_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_today[$transactions->id]['price_district'] = $add_to_total_district;
    		    
    		    
    		    
            }
		}

		// Loop through Misc. Transactions
		$transactions_misc_today = array();
        $transactions = TransactionMisc::findAll($options);
        while ($transactions->next())
		{
            $transaction_month = date('m/y', $transactions->date_submitted);
            if($transaction_month == $last_month) {
                
                // Update our Total Tranactions
                $total_transactions_misc += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                
                $district = District::findBy('id', $transactions->district);
                $school = School::findBy('id', $transactions->school);
                
                
                $price = 0.00;
                
                if($transactions->price != '')
                    $price = $transactions->price;
                else {
                    $price = $service->{$psychologist->price_tier};
                }
                

                
                
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19 && $transactions->meeting_duration != '') {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                //echo "Total (m): " . $add_to_total_psy . "<br>";
                $clean_price = str_replace(',', '', $add_to_total_psy);
                $total_price_psychologists = $total_price_psychologists + $clean_price;
                //echo "Calc: " . $total_price_psychologists . "<br>";
                
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    //$total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    //$total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    //$total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 14) {
                    //$total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($transactions->price), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    //$total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
                
                $clean_price_d = str_replace(',', '', $add_to_total_district);
                $total_price_districts = $total_price_districts + $clean_price_d;
                
            
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_misc_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_misc_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_misc_today[$transactions->id]['service'] = $service->name;
    		    
    		    $transactions_misc_today[$transactions->id]['district'] = $district->district_name;
    		    $transactions_misc_today[$transactions->id]['school'] = $school->school_name;
    		    $transactions_misc_today[$transactions->id]['student'] = $transactions->student_initials;
    		    $transactions_misc_today[$transactions->id]['lasid'] = $transactions->lasid;
    		    $transactions_misc_today[$transactions->id]['sasid'] = $transactions->sasid;
    		    
    		    $transactions_misc_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_misc_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}


        // Return our template values for 'total_day'
        return array(
            'transactions_today' => $transactions_today,
            'transactions_misc_today' => $transactions_misc_today,
            'total_psycholigists' => number_format($total_price_psychologists, 2, '.', ','),
            'total_districts' => number_format(floatval($total_price_districts), 2, '.', ','),
            'transactions' => $total_transactions,
            'transactions_misc' => $total_transactions_misc
        );
    }
    
    // Returns totals for Transactions and Misc. Transactions matching the current month
    public function calculateLastMonthLastYear() {
        
        // Tracks the total price of the Transactions
        $total_price_psychologists = 0.00;
        $total_price_districts = 0.00;
        // Tracks the total number of Transactions
        $total_transactions = 0;
        // Tracks the total number of Misc. Transactions
        $total_transactions_misc = 0;
        // Get the Last Month
        $currentDate = new \DateTime();
        $lastMonth = $currentDate->modify('first day of last month last year');
        $last_month = $lastMonth->format('m/y');
    
        // Loop through Transactions
        $transactions_today = array();
        
        $options = [
            'order' => 'psychologist ASC'
        ];
        
        $transactions = Transaction::findAll($options);
        while ($transactions->next())
		{
		    // If 'date_submitted' is today
            $transaction_month = date('m/y', $transactions->date_submitted);
            if($transaction_month == $last_month) {
                
                // Update our Total Tranactions
                $total_transactions += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);

                $assignment = Assignment::findOneBy('id', $transactions->pid);
                $district = District::findOneBy('id', $assignment->district);
                $school = School::findOneBy('id', $assignment->school);
                $student = Student::findOneBy('id', $assignment->student);
                
                $price = $service->{$psychologist->price_tier};
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
                $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    //$total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    //$total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    //$total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    //$total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
                
                $clean_price_d = str_replace(',', '', $add_to_total_district);
                $total_price_districts = $total_price_districts + $clean_price_d;
                
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_today[$transactions->id]['service'] = $service->service_code . " - " .$service->name;
    		    
    		    $transactions_today[$transactions->id]['district'] = $district->district_name;
    		    $transactions_today[$transactions->id]['school'] = $school->school_name;
    		    $transactions_today[$transactions->id]['student'] = $student->name;
    		    $transactions_today[$transactions->id]['lasid'] = $student->lasid;
    		    $transactions_today[$transactions->id]['sasid'] = $student->sasid;
    		    
    		    
    		    $transactions_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_today[$transactions->id]['price_district'] = $add_to_total_district;
    		    
    		    
    		    
            }
		}

		// Loop through Misc. Transactions
		$transactions_misc_today = array();
        $transactions = TransactionMisc::findAll($options);
        while ($transactions->next())
		{
            $transaction_month = date('m/y', $transactions->date_submitted);
            if($transaction_month == $last_month) {
                
                // Update our Total Tranactions
                $total_transactions_misc += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                
                $district = District::findBy('id', $transactions->district);
                $school = School::findBy('id', $transactions->school);
                
                
                $price = 0.00;
                
                if($transactions->price != '')
                    $price = $transactions->price;
                else {
                    $price = $service->{$psychologist->price_tier};
                }
                

                
                
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19 && $transactions->meeting_duration != '') {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                //echo "Total (m): " . $add_to_total_psy . "<br>";
                $clean_price = str_replace(',', '', $add_to_total_psy);
                $total_price_psychologists = $total_price_psychologists + $clean_price;
                //echo "Calc: " . $total_price_psychologists . "<br>";
                
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    //$total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    //$total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    //$total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 14) {
                    //$total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($transactions->price), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    //$total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
                
                $clean_price_d = str_replace(',', '', $add_to_total_district);
                $total_price_districts = $total_price_districts + $clean_price_d;
                
            
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_misc_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_misc_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_misc_today[$transactions->id]['service'] = $service->name;
    		    
    		    $transactions_misc_today[$transactions->id]['district'] = $district->district_name;
    		    $transactions_misc_today[$transactions->id]['school'] = $school->school_name;
    		    $transactions_misc_today[$transactions->id]['student'] = $transactions->student_initials;
    		    $transactions_misc_today[$transactions->id]['lasid'] = $transactions->lasid;
    		    $transactions_misc_today[$transactions->id]['sasid'] = $transactions->sasid;
    		    
    		    $transactions_misc_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_misc_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}


        // Return our template values for 'total_day'
        return array(
            'transactions_today' => $transactions_today,
            'transactions_misc_today' => $transactions_misc_today,
            'total_psycholigists' => number_format($total_price_psychologists, 2, '.', ','),
            'total_districts' => number_format(floatval($total_price_districts), 2, '.', ','),
            'transactions' => $total_transactions,
            'transactions_misc' => $total_transactions_misc
        );
    }
    
    
    // Returns totals for Transactions and Misc. Transactions matching the current year
    public function calculateYear() {
        
        // Tracks the total price of the Transactions
        $total_price_psychologists = 0.00;
        $total_price_districts = 0.00;
        // Tracks the total number of Transactions
        $total_transactions = 0;
        // Tracks the total number of Misc. Transactions
        $total_transactions_misc = 0;
        // Get the Year
        $year = date('Y');
    
        // Loop through Transactions
        $transactions_today = array();
        
        $options = [
    		'order' => 'psychologist ASC'
    	];
        
        $transactions = Transaction::findAll($options);
        while ($transactions->next())
		{
		    // If 'date_submitted' is today
            $transaction_year = date('Y', $transactions->date_submitted);
            if($transaction_year == $year) {
                
                // Update our Total Tranactions
                $total_transactions += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                
                $price = $service->{$psychologist->price_tier};
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
			    $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    $total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    $total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
                
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_today[$transactions->id]['service'] = $service->service_code . " - " .$service->name;
    		    $transactions_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}

		// Loop through Misc. Transactions
		$transactions_misc_today = array();
        $transactions = TransactionMisc::findAll($options);
        while ($transactions->next())
		{
            $transaction_year = date('Y', $transactions->date_submitted);
            if($transaction_year == $year) {
                
                // Update our Total Tranactions
                $total_transactions_misc += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                
                //$price = $service->{$psychologist->price_tier};
                $price = 0.00;
    			if($transactions->price != '')
    				$price = $transactions->price;
    			else {
    				$price = $service->{$psychologist->price_tier};
    			}
                
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19 && $transactions->meeting_duration != '') {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
			    $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    $total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 14) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    $total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
            
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_misc_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_misc_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_misc_today[$transactions->id]['service'] = $service->name;
    		    $transactions_misc_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_misc_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}

        // Return our template values for 'total_day'
        return array(
            'transactions_today' => $transactions_today,
            'transactions_misc_today' => $transactions_misc_today,
            'total_psycholigists' => number_format($total_price_psychologists, 2, '.', ','),
            'total_districts' => number_format(floatval($total_price_districts), 2, '.', ','),
            'transactions' => $total_transactions,
            'transactions_misc' => $total_transactions_misc
        );
    }
    
    // Returns totals for Transactions and Misc. Transactions matching the current year
    public function calculateLastYear() {
        
        // Tracks the total price of the Transactions
        $total_price_psychologists = 0.00;
        $total_price_districts = 0.00;
        // Tracks the total number of Transactions
        $total_transactions = 0;
        // Tracks the total number of Misc. Transactions
        $total_transactions_misc = 0;
        // Get the Year
        $year = date('Y') - 1;
    
        // Loop through Transactions
        $transactions_today = array();
        
        $options = [
    		'order' => 'psychologist ASC'
    	];
        
        $transactions = Transaction::findAll($options);
        while ($transactions->next())
		{
		    // If 'date_submitted' is today
            $transaction_year = date('Y', $transactions->date_submitted);
            if($transaction_year == $year) {
                
                // Update our Total Tranactions
                $total_transactions += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                
                $price = $service->{$psychologist->price_tier};
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
			    $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    $total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    $total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
                
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_today[$transactions->id]['service'] = $service->service_code . " - " .$service->name;
    		    $transactions_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}

		// Loop through Misc. Transactions
		$transactions_misc_today = array();
        $transactions = TransactionMisc::findAll($options);
        while ($transactions->next())
		{
            $transaction_year = date('Y', $transactions->date_submitted);
            if($transaction_year == $year) {
                
                // Update our Total Tranactions
                $total_transactions_misc += 1;
            
                // Gather our data to make our calculations
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                
                //$price = $service->{$psychologist->price_tier};
                $price = 0.00;
    			if($transactions->price != '')
    				$price = $transactions->price;
    			else {
    				$price = $service->{$psychologist->price_tier};
    			}
                
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19 && $transactions->meeting_duration != '') {
                    $final_price = $transactions->meeting_duration * 0.50;
                    //$total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    //$total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
                $clean_price = str_replace(',', '', $add_to_total_psy);
			    $total_price_psychologists = $total_price_psychologists + $clean_price;
                
                // DIS CALCULATIONS
                if($transactions->service == 1) {
                    // Get our half and quarter rate
                    $rate_half = $price_district / 2;
                    $rate_quarter = $price_district / 4;
                    $final_price = 0;
                    // If duration is under 30 mins
                    if($transactions->meeting_duration <= 30) {
                        $final_price = $rate_half;
                    } else {
                        $dur = ceil(($transactions->meeting_duration-30) / 15);
                        $final_price = $rate_half + ($dur * $rate_quarter);
                    }
                    $total_price_districts += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 12) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 13) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                } else if($transactions->service == 14) {
                    $total_price_districts += number_format(floatval($price_district), 2, '.', ',');
                    $add_to_total_district = number_format(floatval($price_district), 2, '.', ',');
                    
                } else if($transactions->service == 19) {
                } else {
                    $total_price_districts += $price_district;
                    $add_to_total_district = $price_district;
                }
            
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_misc_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_misc_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_misc_today[$transactions->id]['service'] = $service->name;
    		    $transactions_misc_today[$transactions->id]['price_psy'] = $add_to_total_psy;
    		    $transactions_misc_today[$transactions->id]['price_district'] = $add_to_total_district;
            }
		}

        // Return our template values for 'total_day'
        return array(
            'transactions_today' => $transactions_today,
            'transactions_misc_today' => $transactions_misc_today,
            'total_psycholigists' => number_format($total_price_psychologists, 2, '.', ','),
            'total_districts' => number_format(floatval($total_price_districts), 2, '.', ','),
            'transactions' => $total_transactions,
            'transactions_misc' => $total_transactions_misc
        );
    }
    
    
    
    public function getReviewStatuses($which_month) {
        
        // Loop through all Psychologists
        $opt = [
            'order' => 'firstname ASC'
        ];
        $psychologists = MemberModel::findBy('disable', 0, $opt);
        
        $review_status = array();
        foreach($psychologists as $psy) {
            
            $percent_transactions = 0;
            $percent_misc_transactions = 0;
            $month = '';
            $year = 0;
            
            if($which_month == 'this_month') {
                $month = $this->stringLowercaseAndUnderscore(date('F'));
                $year = date('Y');
            } else {
                $month = strtolower(date("F", strtotime("last month")));
                $year = date("Y", strtotime("last month"));
            }
                
            $opt = [
                'order' => 'date_reviewed DESC'
            ];
            $review_record = ReviewRecord::findOneBy(['psychologist = ?', 'date_month = ?', 'date_year = ?'], [$psy->id, $month, $year], $opt);
            if($review_record) {
                //$review_status[$psy->id]['name'] = $review_record->id;
                $percent_transactions = $review_record->transactions_percentage_reviewed;
                $percent_misc_transactions = $review_record->misc_transactions_percentage_reviewed;
                
                $review_status[$psy->id]['name'] .= $psy->firstname . " " . $psy->lastname;
                $review_status[$psy->id]['percent_transactions'] = $percent_transactions;
                $review_status[$psy->id]['percent_misc_transactions'] = $percent_misc_transactions;
                
                if($percent_transactions == 100 && $percent_misc_transactions == 100) {
                    $review_status[$psy->id]['class'] = "reviewed_full";
                } else {
                    if($percent_transactions == 0 && $percent_misc_transactions == 0) {
                        $review_status[$psy->id]['class'] = "reviewed_none";
                    } else {
                        $review_status[$psy->id]['class'] = "reviewed_partial";
                    }
                }
            }
            
        }
        return $review_status;
    }
    

    // Assignment Totals - Month
    public function calculateAssignmentsMonth($which_month) {
        if($which_month == 'this_month') {
            $assignments = Database::getInstance()->prepare("SELECT * FROM tl_assignment WHERE FROM_UNIXTIME(date_created) >= DATE_FORMAT(NOW(), '%Y-%m-01') AND FROM_UNIXTIME(date_created) < DATE_FORMAT(NOW() + INTERVAL 1 MONTH, '%Y-%m-01')")->execute();
            return $assignments->count();
        } else if($which_month == 'last_month') {
            $assignments = Database::getInstance()->prepare("SELECT * FROM tl_assignment WHERE FROM_UNIXTIME(date_created) >= DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') AND FROM_UNIXTIME(date_created) < DATE_FORMAT(NOW(), '%Y-%m-01');")->execute();
            return $assignments->count();
        }
    }
    
    // Assignment Totals - Year
    public function calculateAssignmentsYear($which_year) {
        if($which_year == 'this_year') {
            $assignments = Database::getInstance()->prepare("SELECT * FROM tl_assignment WHERE YEAR(FROM_UNIXTIME(date_created)) = YEAR(NOW())")->execute();
            return $assignments->count();
        } else if($which_year == 'last_year') {
            $assignments = Database::getInstance()->prepare("SELECT * FROM tl_assignment WHERE YEAR(FROM_UNIXTIME(date_created)) = YEAR(NOW()) - 1")->execute();
            return $assignments->count();
        }
    }


    public function getLastReviewedAndSubmitted() {
        $opt = [
            'order' => 'firstname ASC'
        ];
        $psychologists = MemberModel::findBy('disable', 0, $opt);
        $last_reviewed = array();
        foreach($psychologists as $psy) {
            $last_reviewed[$psy->id]['name'] = $psy->firstname . " " . $psy->lastname;
            $last_reviewed[$psy->id]['last_review_and_submit'] = $psy->last_review_and_submit;
        }
        return $last_reviewed;
    }
    
    private function stringLowercaseAndUnderscore(string $inputString): string {
        // 1. Convert the entire string to lowercase.
        $lowercaseString = strtolower($inputString);
        // 2. Replace all instances of a single space (' ') with an underscore ('_').
        $processedString = str_replace(' ', '_', $lowercaseString);
        return $processedString;
    }
  
}
