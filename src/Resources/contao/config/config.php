<?php
 
/**
* @copyright  Bright Cliud Studio
* @author     Bright Cloud Studio
* @package    GAI Payment Dashboard
* @license    LGPL-3.0+
* @see	       https://github.com/bright-cloud-studio/gai-payment-d
*/


/* Create new sections in the Contao sidebar */
array_unshift($GLOBALS['TL_LANG']['MOD'], array('gai', 'GAI Payment Dashboard'));



/* Back end modules */
$GLOBALS['BE_MOD']['gai']['transaction'] = array(
	'tables' => array('tl_transaction')
);
$GLOBALS['BE_MOD']['gai']['assignment'] = array(
	'tables' => array('tl_assignment')
);


/* Models */
$GLOBALS['TL_MODELS']['tl_transaction']    = 'Bcs\Model\Transaction';
