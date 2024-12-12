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

/* System Buttons */
$GLOBALS['TL_LANG']['tl_district']['new']                        = array('New District', 'Add a new record');
$GLOBALS['TL_LANG']['tl_district']['show']                       = array('District details', 'Show the details of record ID %s');
$GLOBALS['TL_LANG']['tl_district']['edit']                       = array('Edit District', 'Edit record ID %s');
$GLOBALS['TL_LANG']['tl_district']['copy']                       = array('Copy District', 'Copy record ID %s');
$GLOBALS['TL_LANG']['tl_district']['delete']                     = array('Delete District', 'Delete record ID %s');
$GLOBALS['TL_LANG']['tl_district']['toggle']                     = array('Toggle District', 'Toggle record ID %s');

/* System Fields */
$GLOBALS['TL_LANG']['tl_district']['publish_legend']             = 'Publish';
$GLOBALS['TL_LANG']['tl_district']['alias']                      = array('Alias', 'Auto-generated alias.');
$GLOBALS['TL_LANG']['tl_district']['published']                  = array('Published', 'Publishing a District will make it available as an option when creating Assignments.');

/* Fields */
$GLOBALS['TL_LANG']['tl_district']['district_legend']            = 'District Information';
$GLOBALS['TL_LANG']['tl_district']['district_name']              = array('District Name', 'Enter the name of this District');
$GLOBALS['TL_LANG']['tl_district']['invoice_prefix']             = array('Invoice Prefix', 'Unique Prefix used before their invoice number');
$GLOBALS['TL_LANG']['tl_district']['purchase_order']             = array('Purchase Order', 'Purchase Order used on this District\'s invoices');

$GLOBALS['TL_LANG']['tl_district']['address_legend']             = 'Address Information';
$GLOBALS['TL_LANG']['tl_district']['contact_name']               = array('Contact Name', 'The name of the contact person at this District. This is who will receive this District\'s Invoice when sending out emails.');
$GLOBALS['TL_LANG']['tl_district']['contact_address']            = array('Contact Address', 'The address stamped on this District\'s Invoice');
$GLOBALS['TL_LANG']['tl_district']['city']                       = array('City', 'The City for this address');
$GLOBALS['TL_LANG']['tl_district']['state']                      = array('State', 'The State for this address');
$GLOBALS['TL_LANG']['tl_district']['zip']                        = array('ZIP', 'The ZIP for this address');
$GLOBALS['TL_LANG']['tl_district']['phone']                      = array('Phone Number', 'The Contact Person\'s phone number');
$GLOBALS['TL_LANG']['tl_district']['contact_email']              = array('Contact Email', 'The primary email this District\'s Invoice will be sent to');
$GLOBALS['TL_LANG']['tl_district']['contact_cc_email']           = array('Contact CC Email', 'A secondary email address that will be CCed in the Invoice email');
