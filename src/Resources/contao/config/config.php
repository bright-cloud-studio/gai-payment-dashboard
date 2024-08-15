<?php

use Contao\ArrayUtil;
use Contao\System;

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
$GLOBALS['BE_MOD']['gai_data']['price_tier'] = array(
	'tables' => array('tl_price_tier')
);


/* Back end modules - Work DCAs */
$GLOBALS['TL_LANG']['MOD']['gai'][0] = "Invoices - Generator";

$GLOBALS['BE_MOD']['gai']['invoice_request'] = array(
	'tables' => array('tl_invoice_request')
);
$GLOBALS['BE_MOD']['gai']['assignment'] = array(
	'tables' => array('tl_assignment')
);
$GLOBALS['BE_MOD']['gai']['transaction'] = array(
	'tables' => array('tl_transaction')
);


/* Hooks */
$GLOBALS['TL_HOOKS']['processFormData'][]        = array('Bcs\Hooks\FormHooks', 'onFormSubmit');
$GLOBALS['TL_HOOKS']['compileFormFields'][]      = array('Bcs\Hooks\FormHooks', 'onPrepareForm');


/* Front End modules */
$GLOBALS['FE_MOD']['gai']['mod_list_services']          = 'Bcs\Module\ModListServices';
$GLOBALS['FE_MOD']['gai']['mod_list_transactions']      = 'Bcs\Module\ModListTransactions';


/* Models */
$GLOBALS['TL_MODELS']['tl_assignment']         = 'Bcs\Model\Assignment';
$GLOBALS['TL_MODELS']['tl_district']           = 'Bcs\Model\District';
$GLOBALS['TL_MODELS']['tl_invoice_request']    = 'Bcs\Model\InvoiceRequest';
$GLOBALS['TL_MODELS']['tl_school']             = 'Bcs\Model\School';
$GLOBALS['TL_MODELS']['tl_service']            = 'Bcs\Model\Service';
$GLOBALS['TL_MODELS']['tl_student']            = 'Bcs\Model\Student';
$GLOBALS['TL_MODELS']['tl_price_tier']         = 'Bcs\Model\PriceTier';
$GLOBALS['TL_MODELS']['tl_transaction']        = 'Bcs\Model\Transaction';
