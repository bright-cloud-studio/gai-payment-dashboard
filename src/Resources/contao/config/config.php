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

$GLOBALS['TL_LANG']['MOD']['gai_alerts'][0] = "Automated Emails";
$GLOBALS['BE_MOD']['gai_alerts']['alert_email'] = array( 'tables' => array('tl_alert_email') );

/* Back end modules - Data DCAs */
$GLOBALS['TL_LANG']['MOD']['gai_data'][0] = "Invoices - Data";
$GLOBALS['BE_MOD']['gai_data']['district'] = array( 'tables' => array('tl_district') );
$GLOBALS['BE_MOD']['gai_data']['school'] = array( 'tables' => array('tl_school') );
$GLOBALS['BE_MOD']['gai_data']['student'] = array( 'tables' => array('tl_student') );
$GLOBALS['BE_MOD']['gai_data']['service'] = array( 'tables' => array('tl_service') );
$GLOBALS['BE_MOD']['gai_data']['service'] = array( 'tables' => array('tl_service') );

/* Back end modules - Work DCAs */
$GLOBALS['TL_LANG']['MOD']['gai'][0] = "Invoices - Generator";
$GLOBALS['BE_MOD']['gai']['invoice_request'] = array( 'tables' => array('tl_invoice_request') );
$GLOBALS['BE_MOD']['gai']['invoice'] = array( 'tables' => array('tl_invoice') );
$GLOBALS['BE_MOD']['gai']['invoice_district'] = array( 'tables' => array('tl_invoice_district') );
$GLOBALS['BE_MOD']['gai']['assignment'] = array( 'tables' => array('tl_assignment') );
$GLOBALS['BE_MOD']['gai']['transaction'] = array( 'tables' => array('tl_transaction') );
$GLOBALS['BE_MOD']['gai']['transaction_misc'] = array( 'tables' => array('tl_transaction_misc') );

/* Back end modules - Work DCAs */
$GLOBALS['TL_LANG']['MOD']['gai_issue'][0] = "Issues";
$GLOBALS['BE_MOD']['gai_issue']['issue'] = array( 'tables' => array('tl_issue') );

/* Hooks */
$GLOBALS['TL_HOOKS']['processFormData'][]        = array('Bcs\Hooks\FormHooks', 'onFormSubmit');
$GLOBALS['TL_HOOKS']['compileFormFields'][]      = array('Bcs\Hooks\FormHooks', 'onPrepareForm');
$GLOBALS['TL_HOOKS']['parseTemplate'][]          = array('Bcs\Hooks\TemplateHooks', 'onParseTemplate');

/* Front End modules */
$GLOBALS['FE_MOD']['gai']['mod_list_services']           = 'Bcs\Module\ModListServices';
$GLOBALS['FE_MOD']['gai']['mod_list_transactions']       = 'Bcs\Module\ModListTransactions';
$GLOBALS['FE_MOD']['gai']['mod_list_assignments_filter'] = 'Bcs\Module\ModListAssignmentsFilter';
$GLOBALS['FE_MOD']['gai']['mod_psych_work_form']         = 'Bcs\Module\ModPsychWorkForm';
$GLOBALS['FE_MOD']['gai']['mod_review_transactions']     = 'Bcs\Module\ModReviewTransactions';
$GLOBALS['FE_MOD']['gai']['mod_invoice_history']         = 'Bcs\Module\ModInvoiceHistory';
$GLOBALS['FE_MOD']['gai']['mod_admin_review']            = 'Bcs\Module\ModAdminReview';

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

/* Custom Form Fields */
$GLOBALS['BE_FFL']['select_dynamic'] = SelectMenuDynamic::class;
$GLOBALS['TL_FFL']['select_dynamic'] = FormSelectDynamic::class;
