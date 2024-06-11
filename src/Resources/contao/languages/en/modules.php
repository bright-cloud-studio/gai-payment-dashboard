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

/* Back end modules */
$GLOBALS['TL_LANG']['MOD']['transaction']                  = array('Transactions', 'Stored Transactions for the Duplicate Checker');

/* Back end modules */
$GLOBALS['TL_LANG']['MOD']['psychologists']                 = array('Psychologists', 'Psychologist data pulled from Sheets');
$GLOBALS['TL_LANG']['MOD']['services']                      = array('Services', 'Services data pulled from Sheets');

/* Front end modules */
$GLOBALS['TL_LANG']['MOD']['gai_invoices']                  = array('GAI Invoices', 'Custom modules for the Dashboard');
$GLOBALS['TL_LANG']['FMD']['mod_work_assignments']          = array('Work Assignments', 'Displays Work Assignments and allows creation of transactions for them');
$GLOBALS['TL_LANG']['FMD']['mod_add_meetings']              = array('Add Meetings', 'Manually add meeting transactions without a Work Assignment');

// Manually Approved Services
$GLOBALS['TL_LANG']['FMD']['mod_misc_billing']              = array('Miscellaneous Billing', 'Manually add miscellaneous billing');
$GLOBALS['TL_LANG']['FMD']['mod_misc_travel_expenses']      = array('Miscellaneous Travel Expenses', 'Manually add Miscellaneous Travel Expenses');
$GLOBALS['TL_LANG']['FMD']['mod_parking']                   = array('Parking', 'Manually add Parking transactions');
$GLOBALS['TL_LANG']['FMD']['mod_manager']                   = array('Manager', 'Manually add Manager transactions');
$GLOBALS['TL_LANG']['FMD']['mod_editing_services']          = array('Editing Services', 'Manually add Editing Services transactions');


$GLOBALS['TL_LANG']['FMD']['mod_invoice_history'] 	        = array('Invoice History', 'Displays all previous invoices and a preview of the next upcoming one');
$GLOBALS['TL_LANG']['FMD']['mod_transaction_review']        = array('Transaction Review', 'List and review transactions submitted in the last month');
$GLOBALS['TL_LANG']['FMD']['mod_admin_review']              = array('Admin Review', 'Review and edit all transactions for the last billing month');
$GLOBALS['TL_LANG']['FMD']['mod_send_invoice_emails']       = array('Send Invoice Emails', 'Choose which schools and psychologist to send the previous months invoice');
