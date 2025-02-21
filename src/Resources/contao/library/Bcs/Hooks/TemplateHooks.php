<?php

namespace Bcs\Hooks;

use Bcs\Model\Service;
use Bcs\Model\Transaction;
use Bcs\Model\TransactionMisc;

use Contao\Controller;
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
            
            // Set 'This Month' total
            $template->total_this_month = $this->calculateThisMonth();
            
            // Set 'Last Month' total
            $template->total_last_month = $this->calculateLastMonth();
            
            // Set 'Year' total
            $template->total_year = $this->calculateYear();
            
            $template->transactions_today = $transactions_today;
            
        }
    }
    
    
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
        $transactions = Transaction::findAll();
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
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    $total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
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
                $price = $service->{$psychologist->price_tier};
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    $total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
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
    
        // Loop through Transactions
        $transactions_today = array();
        $transactions = Transaction::findAll();
        while ($transactions->next())
		{
		    // If 'date_submitted' is today
            $transaction_month = date('m', $transactions->date_submitted);
            if($transaction_month == $month) {
                
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
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    $total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
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
            $transaction_month = date('m', $transactions->date_submitted);
            if($transaction_month == $month) {
                
                // Update our Total Tranactions
                $total_transactions_misc += 1;
            
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
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    $total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
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
        $transactions = Transaction::findAll();
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
                $price = $service->{$psychologist->price_tier};
                $price_district = $service->school_tier_1_price;
                
                $add_to_total_psy = 0.00;
                $add_to_total_district = 0.00;
                
                // PSY CALCULATIONS
                if($transactions->service == 1) {
                    $dur = ceil(intval($transactions->meeting_duration) / 60);
                    $final_price = $dur * $price;
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    $total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
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
            $transaction_month = date('m/y', $transactions->date_submitted);
            if($transaction_month == $last_month) {
                
                // Update our Total Tranactions
                $total_transactions_misc += 1;
            
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
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    $total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
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
        $transactions = Transaction::findAll();
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
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    $total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
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
            $transaction_year = date('Y', $transactions->date_submitted);
            if($transaction_year == $year) {
                
                // Update our Total Tranactions
                $total_transactions_misc += 1;
            
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
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else if($transactions->service == 19) {
                    $final_price = $transactions->meeting_duration * 0.50;
                    $total_price_psychologists += number_format(floatval($final_price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($final_price), 2, '.', ',');
                } else {
                    $total_price_psychologists += number_format(floatval($price), 2, '.', ',');
                    $add_to_total_psy = number_format(floatval($price), 2, '.', ',');
                }
                
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

  
}
