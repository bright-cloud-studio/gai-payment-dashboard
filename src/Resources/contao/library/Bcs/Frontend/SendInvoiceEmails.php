<?php

/**
 * Bright Cloud Studio's GAI Invoices
 *
 * Copyright (C) 2022-2023 Bright Cloud Studio
 *
 * @package    bright-cloud-studio/gai-invoices
 * @link       https://www.brightcloudstudio.com/
 * @license    http://opensource.org/licenses/lgpl-3.0.html
**/


namespace Bcs\Frontend;

use Google;
use Contao\System;
use Contao\Frontend as Contao_Frontend;


class SendInvoiceEmails extends Contao_Frontend {

    public function sendEmails() {
        
        // Check the URL, if it contains our ailias do our stuffs
        if (substr(\Environment::get('request'), 39, 27) == "send-invoice-emails-success") {
            
            // Create a client connection to Google
            $client = new Google\Client();
            $client->setAuthConfig($_SERVER['DOCUMENT_ROOT'] . '/key.json');
            $client->addScope(Google\Service\Sheets::SPREADSHEETS);
            $service = new \Google_Service_Sheets($client);
            $spreadsheetId = '1PEJN5ZGlzooQrtIEdeo4_nZH73W0aJTUbRIoibzl3Lo';
            
            // Mark this Work Assignment as Processed
            $updateRow = [
               "Yes",
            ];
            $rows = [$updateRow];
            $valueRange = new \Google_Service_Sheets_ValueRange();
            $valueRange->setValues($rows);
            $options = ['valueInputOption' => 'USER_ENTERED'];
           
            // Store values from the main email to the notification email
            $sent_to_name = '';
            $sent_to_inv_num = '';
            $sent_to_inv_url = '';
            $sent_to_billing_month = '';
            
            // PKs
            $pk_psychologists = 7;
            $pk_schools = 5;
            $pk_notification = 6;

            
            // if our "send psychologist emails" checkbox is ticked
            if($_POST['send_psy_emails'] == 'yes') {
            
                /** SEND PSYCHOLOGIST EMAILS */
                //for ($i = 1; $i <= $_POST['psy_total']; $i++) {
                for ($i = 1; $i <= 1; $i++) {
                        
                    // Get our psychologist email
                    $objNotification = \NotificationCenter\Model\Notification::findByPk($pk_psychologists);
                    if (null !== $objNotification) {
                        
                        // Sender info
                        $arrTokens['sender_name'] = 'Global Assessments, Inc';
                        $arrTokens['sender_address'] = 'billing@globalassessmentsinc.com';
                        $arrTokens['reply_to_address'] = 'billing@globalassessmentsinc.com';
                        
                        // Recipient info
                        $arrTokens['recipient_name'] = $_POST['name_'.$i];
                        $sent_to_name = $arrTokens['recipient_name'];
                        
                        $arrTokens['recipient_email'] = $_POST['email_psy_'.$i];
                        $arrTokens['recipient_cc'] = '';
                        //$arrTokens['recipient_email'] = 'mark@brightcloudstudio.com';
                        //$arrTokens['recipient_cc'] = 'ed@globalassessmentsinc.com';
    
                        $arrTokens['invoice_number'] = $_POST['invoice_number_'.$i];
                        $sent_to_inv_num = $arrTokens['invoice_number'];
                        
                        $arrTokens['invoice_url'] = $_POST['url_psy_'.$i];
                        $sent_to_inv_url = $arrTokens['invoice_url'];
                        
                        $arrTokens['billing_month'] =  date('F', mktime(0, 0, 0, $_POST['billing_month_'.$i], 10));
                        $sent_to_billing_month = $arrTokens['billing_month'];
                        
                        // Send out the email using our tokens
                        $objNotification->send($arrTokens); // Language is optional

                    }
                }
            }
            
            /* SEND SCHOOL EMAILS */
            for ($i = 1; $i <= $_POST['school_total']; $i++) {
                
                if($_POST['send_school_' . $i] == 'yes') {
                    
                    // send our user their email
                    $objNotification = \NotificationCenter\Model\Notification::findByPk($pk_schools);
                    if (null !== $objNotification) {
                        
                        // Sender info
                        $arrTokens['sender_name'] = 'Global Assessments, Inc';
                        $arrTokens['sender_address'] = 'billing@globalassessmentsinc.com';
                        $arrTokens['reply_to_address'] = 'billing@globalassessmentsinc.com';
                        
                        $arrTokens['recipient_name'] = $_POST['district_name_'.$i] . " - " . $_POST['school_name_'.$i];
                        $sent_to_name = $arrTokens['recipient_name'];
                        
                        
                        $arrTokens['recipient_email'] = $_POST['email_school_'.$i];
                        $arrTokens['recipient_cc'] = '';
                        if($_POST['cc_school_'.$i] != '')
                            $arrTokens['recipient_cc'] = $_POST['cc_school_'.$i];
                        
                        
                        //$arrTokens['recipient_email'] = "mark@brightcloudstudio.com";
                        //$arrTokens['recipient_cc'] = 'stjeanmark@gmail.com';
    
                        $arrTokens['billing_month'] =  date('F', mktime(0, 0, 0, $_POST['billing_month_school_'.$i], 10));
                        $sent_to_billing_month = $arrTokens['billing_month'];
                        
                        $arrTokens['invoice_number'] = $_POST['invoice_number_school_'.$i];
                        $sent_to_inv_num = $arrTokens['invoice_number'];
                        $arrTokens['invoice_url'] = $_POST['url_school_'.$i];
                        
                        
                        $arrTokens['invoice_total'] = "$" . number_format( $_POST['invoice_total_school_'.$i], 2 );
                        
                         
                        
                        $objNotification->send($arrTokens); // Language is optional
                        

                        $range = 'Invoices - School!I' . $_POST['row_id_school_'.$i];
                        $service->spreadsheets_values->update($spreadsheetId, $range, $valueRange, $options);
                        
                        
                    }
                    
                    // Send Ed his own notification
                    $objNotification = \NotificationCenter\Model\Notification::findByPk($pk_notification);
                    if (null !== $objNotification) {
                        
                        $arrTokens['sender_name'] = 'Global Assessments, Inc';
                        $arrTokens['sender_address'] = 'billing@globalassessmentsinc.com';
                        $arrTokens['reply_to_address'] = 'billing@globalassessmentsinc.com';
                        
                        $arrTokens['recipient_name'] = $sent_to_name;
                        $arrTokens['recipient_email'] = 'ed@globalassessmentsinc.com';
                        
                        $arrTokens['recipient_cc'] = 'mark@brightcloudstudio.com';
    
                        $arrTokens['invoice_number'] = $sent_to_inv_num;
                        $arrTokens['billing_month'] = $sent_to_billing_month;
                        
                        $objNotification->send($arrTokens); // Language is optional
                    }
                    
                }
            }
        }
    }
}
