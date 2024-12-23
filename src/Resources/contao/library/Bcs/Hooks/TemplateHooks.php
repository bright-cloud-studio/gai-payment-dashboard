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

    // When a form is submitted
    public function onParseTemplate($template)
    {
        if ('be_welcome' === $template->getName()) {
            
            // Set 'Today' total
            $template->total_day = $this->calculateDay();
            
            // Set 'Week' total
            $template->total_week = array('value' => '2000.00', 'transactions' => '20', 'transactions_misc' => '25');
            
            // Set 'Month' total
            $template->total_month = array('value' => '3000.00', 'transactions' => '30', 'transactions_misc' => '35');
            
            // Set 'Year' total
            $template->total_year = array('value' => '4000.00', 'transactions' => '40', 'transactions_misc' => '45');
            
        }
    }
    
    
    public function calculateDay() {
        
        // Tracks the total price of the Transactions
        $total_price = 0.00;
        
        // Tracks the total number of Transactions
        $total_transactions = 0;
        
        // Tracks the total number of Misc. Transactions
        $total_transactions_misc = 0;
        
        // Get Today's Date
        $today = date('m/d/y');
        
        // Loop through Transactions
        $transactions = Transaction::findAll();
        while ($transactions->next())
		{
		    // If 'date_submitted' is today
            $transaction_date = date('m/d/y', $transactions->date_submitted);
            if($transaction_date == $today) {
                
                // Update our Total Tranactions
                $total_transactions += 1;
            
                // Update our Total Price
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                $price = $service->{$psychologist->price_tier};
                $total_price += $price;
            }
		}
		
		// Loop through Misc. Transactions
        $transactions = TransactionMisc::findAll();
        while ($transactions->next())
		{
		    // If 'date_submitted' is today
            $transaction_date = date('m/d/y', $transactions->date_submitted);
            if($transaction_date == $today) {
                
                // Update our Total Tranactions
                $total_transactions_misc += 1;
            
                // Update our Total Price
                $service = Service::findOneBy('service_code', $transactions->service);
                $psychologist = MemberModel::findBy('id', $transactions->psychologist);
                $price = $service->{$psychologist->price_tier};
                $total_price += $price;
            }
		}
		
		
        // Return our template values for 'total_day'
        return array('value' => number_format($total_price, 2, '.', ''), 'transactions' => $total_transactions, 'transactions_misc' => $total_transactions_misc);
    }
  
}
