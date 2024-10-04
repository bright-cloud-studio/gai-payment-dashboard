<?php

use Contao\ArrayUtil;
use Contao\System;

use Bcs\Forms\FormSelectDynamic;

use Bcs\SelectMenuDynamic;


/**
* @copyright  Bright Cliud Studio
* @author     Bright Cloud Studio
* @package    GAI Payment Dashboard
* @license    LGPL-3.0+
* @see	       https://github.com/bright-cloud-studio/gai-payment-d
*/

/* Create new sections in the Contao sidebar */


/* Back end modules - Data DCAs */
$GLOBALS['TL_LANG']['MOD']['gai_data'][0] = "Invoices - Data";

$GLOBALS['BE_MOD']['gai_data']['district'] = array(
	'tables' => array('tl_district')
);
$GLOBALS['BE_MOD']['gai_data']['school'] = array(
	'tables' => array('tl_school')
);
$GLOBALS['BE_MOD']['gai_data']['student'] = array(
	'tables' => array('tl_student')
);
$GLOBALS['BE_MOD']['gai_data']['service'] = array(
	'tables' => array('tl_service')
);

$GLOBALS['BE_MOD']['gai_data']['service'] = array(
	'tables' => array('tl_service')
);

/* Back end modules - Work DCAs */
$GLOBALS['TL_LANG']['MOD']['gai'][0] = "Invoices - Generator";

$GLOBALS['BE_MOD']['gai']['invoice_request'] = array(
	'tables' => array('tl_invoice_request')
);
$GLOBALS['BE_MOD']['gai']['invoice'] = array(
	'tables' => array('tl_invoice')
);
$GLOBALS['BE_MOD']['gai']['invoice_district'] = array(
	'tables' => array('tl_invoice_district')
);

$GLOBALS['BE_MOD']['gai']['assignment'] = array(
	'tables' => array('tl_assignment')
);
$GLOBALS['BE_MOD']['gai']['transaction'] = array(
	'tables' => array('tl_transaction')
);
$GLOBALS['BE_MOD']['gai']['transaction_misc'] = array(
	'tables' => array('tl_transaction_misc')
);


/* Hooks */
$GLOBALS['TL_HOOKS']['processFormData'][]        = array('Bcs\Hooks\FormHooks', 'onFormSubmit');
$GLOBALS['TL_HOOKS']['compileFormFields'][]      = array('Bcs\Hooks\FormHooks', 'onPrepareForm');


/* Front End modules */
$GLOBALS['FE_MOD']['gai']['mod_list_services']           = 'Bcs\Module\ModListServices';
$GLOBALS['FE_MOD']['gai']['mod_list_transactions']       = 'Bcs\Module\ModListTransactions';
$GLOBALS['FE_MOD']['gai']['mod_list_assignments_filter'] = 'Bcs\Module\ModListAssignmentsFilter';
$GLOBALS['FE_MOD']['gai']['mod_psych_work_form']         = 'Bcs\Module\ModPsychWorkForm';
$GLOBALS['FE_MOD']['gai']['mod_review_transactions']     = 'Bcs\Module\ModReviewTransactions';


/* Models */
$GLOBALS['TL_MODELS']['tl_assignment']         = 'Bcs\Model\Assignment';
$GLOBALS['TL_MODELS']['tl_district']           = 'Bcs\Model\District';
$GLOBALS['TL_MODELS']['tl_invoice']            = 'Bcs\Model\Invoice';
$GLOBALS['TL_MODELS']['tl_invoice_district']   = 'Bcs\Model\InvoiceDistrict';
$GLOBALS['TL_MODELS']['tl_invoice_request']    = 'Bcs\Model\InvoiceRequest';
$GLOBALS['TL_MODELS']['tl_school']             = 'Bcs\Model\School';
$GLOBALS['TL_MODELS']['tl_service']            = 'Bcs\Model\Service';
$GLOBALS['TL_MODELS']['tl_student']            = 'Bcs\Model\Student';
$GLOBALS['TL_MODELS']['tl_price_tier']         = 'Bcs\Model\PriceTier';
$GLOBALS['TL_MODELS']['tl_transaction']        = 'Bcs\Model\Transaction';
$GLOBALS['TL_MODELS']['tl_transaction_misc']   = 'Bcs\Model\TransactionMisc';


// Custom Form Fields
$GLOBALS['BE_FFL']['select_dynamic'] = SelectMenuDynamic::class;
$GLOBALS['TL_FFL']['select_dynamic'] = FormSelectDynamic::class;


/** Add new notification type */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['gai_invoices']['send_email'] = array
(
    'recipients'           => array('recipient_email'),
    'email_subject'        => array('recipient_name', 'invoice_number', 'billing_month'),
    'email_text'           => array('recipient_name', 'invoice_number', 'invoice_url', 'invoice_total', 'billing_month'),
    'email_html'           => array('recipient_name', 'invoice_number', 'invoice_url'),
    'file_name'            => array('invoice_filename', 'invoice_url'),
    'file_content'         => array('invoice_url', 'invoice_file'),
    'email_sender_name'    => array('sender_name'),
    'email_sender_address' => array('sender_address'),
    'email_recipient_cc'   => array('recipient_cc'),
    'email_replyTo'        => array('reply_to_address'),
    'attachment_tokens'    => array('invoice_token'),
);
