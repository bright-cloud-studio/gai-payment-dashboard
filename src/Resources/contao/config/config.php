<?php
 
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
$GLOBALS['BE_MOD']['gai']['transaction'] = array(
	'tables' => array('tl_transaction')
);
$GLOBALS['BE_MOD']['gai']['assignment'] = array(
	'tables' => array('tl_assignment')
);



/* Front End modules */
$GLOBALS['FE_MOD']['gai']['mod_work_assignments']          = 'Bcs\Module\ModWorkAssignments';


/* Models */
$GLOBALS['TL_MODELS']['tl_transaction']    = 'Bcs\Model\Transaction';
