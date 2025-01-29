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
            
            // Set 'Week' total
            $template->total_week = array('value' => '0.00', 'transactions' => '20', 'transactions_misc' => '25');
            
            // Set 'Month' total
            $template->total_month = array('value' => '0.00', 'transactions' => '30', 'transactions_misc' => '35');
            
            // Set 'Year' total
            $template->total_year = array('value' => '0.00', 'transactions' => '40', 'transactions_misc' => '45');
            
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
                $add_to_total_psy = 0.00;
                
                
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

                
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_today[$transactions->id]['service'] = $service->name;
    		    $transactions_today[$transactions->id]['price'] = $add_to_total_psy;
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
                $add_to_total_psy = 0.00;
                
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
            
                // Add this Misc. Transaction to our template so we can use it in our debug log    
                $transactions_misc_today[$transactions->id]['id'] = $transactions->id;
    		    $transactions_misc_today[$transactions->id]['psychologist'] = $psychologist->firstname . ' ' . $psychologist->lastname;
    		    $transactions_misc_today[$transactions->id]['service'] = $service->name;
    		    $transactions_misc_today[$transactions->id]['price'] = $add_to_total_psy;
            }
		}
		
		
		
		
		
        // Return our template values for 'total_day'
        return array(
            'transactions_today' => $transactions_today,
            'transactions_misc_today' => $transactions_misc_today,
            'total_psycholigists' => number_format($total_price_psychologists, 2, '.', ''),
            'total_districts' => number_format($total_price_districts, 2, '.', ''),
            'transactions' => $total_transactions,
            'transactions_misc' => $total_transactions_misc
        );
    }
  
}
