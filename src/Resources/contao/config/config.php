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
$GLOBALS['TL_LANG']['MOD']['gai'][0] = "GAI Payment Dashboard";

/* Back end modules */
$GLOBALS['BE_MOD']['gai']['invoice_request'] = array(
	'tables' => array('tl_invoice_request')
);
$GLOBALS['BE_MOD']['gai']['assignment'] = array(
	'tables' => array('tl_assignment')
);
$GLOBALS['BE_MOD']['gai']['transaction'] = array(
	'tables' => array('tl_transaction')
);
$GLOBALS['BE_MOD']['gai']['district'] = array(
	'tables' => array('tl_district')
);
$GLOBALS['BE_MOD']['gai']['school'] = array(
	'tables' => array('tl_school')
);
$GLOBALS['BE_MOD']['gai']['service'] = array(
	'tables' => array('tl_service')
);
$GLOBALS['BE_MOD']['gai']['price_tier'] = array(
	'tables' => array('tl_price_tier')
);


/* Hooks */
$GLOBALS['TL_HOOKS']['processFormData'][]        = array('Bcs\Hooks\FormHooks', 'onFormSubmit');
$GLOBALS['TL_HOOKS']['compileFormFields'][]      = array('Bcs\Hooks\FormHooks', 'onPrepareForm');


/* Front End modules */
$GLOBALS['FE_MOD']['gai']['mod_assignments']          = 'Bcs\Module\ModAssignments';


/* Models */
$GLOBALS['TL_MODELS']['tl_assignment']         = 'Bcs\Model\Assignment';
$GLOBALS['TL_MODELS']['tl_district']           = 'Bcs\Model\District';
$GLOBALS['TL_MODELS']['tl_invoice_request']    = 'Bcs\Model\InvoiceRequest';
$GLOBALS['TL_MODELS']['tl_school']             = 'Bcs\Model\School';
$GLOBALS['TL_MODELS']['tl_service']            = 'Bcs\Model\Service';
$GLOBALS['TL_MODELS']['tl_price_tier']         = 'Bcs\Model\PriceTier';
$GLOBALS['TL_MODELS']['tl_transaction']        = 'Bcs\Model\Transaction';


//$request = System::getContainer()->get('request_stack')->getCurrentRequest();
//if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
//{
//    $GLOBALS['TL_BODY'][] = '<script src="bundles/bcspaymentdashboard/js/select2.min.js"></script>';
//    $GLOBALS['TL_CSS'][] = '/bundles/bcspaymentdashboard/css/select2.min.css';
//}
