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
$GLOBALS['TL_LANG']['tl_invoice_request']['new']                        = array('New Invoice Generation Request', 'Add a new record');
$GLOBALS['TL_LANG']['tl_invoice_request']['show']                       = array('Invoice Generation Request Details', 'Show the details of record ID %s');
$GLOBALS['TL_LANG']['tl_invoice_request']['edit']                       = array('Edit Invoice Generation Request', 'Edit record ID %s');
$GLOBALS['TL_LANG']['tl_invoice_request']['copy']                       = array('Copy Invoice Generation Request', 'Copy record ID %s');
$GLOBALS['TL_LANG']['tl_invoice_request']['delete']                     = array('Delete Invoice Generation Request', 'Delete record ID %s');
$GLOBALS['TL_LANG']['tl_invoice_request']['toggle']                     = array('Toggle Invoice Generation Request', 'Toggle record ID %s');

/* System Fields */
$GLOBALS['TL_LANG']['tl_invoice_request']['publish_legend']             = 'Publish';
$GLOBALS['TL_LANG']['tl_invoice_request']['alias']                      = array('Alias', 'Auto-generated alias.');
$GLOBALS['TL_LANG']['tl_invoice_request']['published']                  = array('Published', 'Show this record on the front end.');

/* Fields */
$GLOBALS['TL_LANG']['tl_invoice_request']['invoice_request_legend']        = 'Invoice Request Information';
$GLOBALS['TL_LANG']['tl_invoice_request']['date_start']                    = array('Date Start', 'The Start Date for our range of Trnsactions to generate invoices for');
$GLOBALS['TL_LANG']['tl_invoice_request']['date_end']                      = array('Date End', 'The End Date for our range of Trnsactions to generate invoices for');

$GLOBALS['TL_LANG']['tl_invoice_request']['exclude_psychologists']          = array('Exclude Psychologists', 'Check a Psychologist to exclude them from this Generation Request. Even if they have Transactions, excluded Psychologists will NOT have an Invoice generated');
$GLOBALS['TL_LANG']['tl_invoice_request']['exclude_districts']              = array('Exclude Districts', 'Check a Distrct to exclude it from this Generation Request. Even if it has Transactions, excluded Districts will NOT have an Invoice generated');

$GLOBALS['TL_LANG']['tl_invoice_request']['batch_legend']                   = 'Batch Print Information';
$GLOBALS['TL_LANG']['tl_invoice_request']['batch_url']                      = array('Link to Batch Print ZIP', 'Once this Invoice Request is completed this field will be filled in with a link to the generated Batch Print ZIP file');

$GLOBALS['TL_LANG']['tl_invoice_request']['internal_legend']                = 'INTERNAL Information';
$GLOBALS['TL_LANG']['tl_invoice_request']['created_invoice_dcas']           = array('Created Invoice DCAs', 'Tracks if this Invoice Request has created the placeholder Invoices for Psychologists and Districts');
$GLOBALS['TL_LANG']['tl_invoice_request']['generated_psys']                 = array('Generated Psychologist Invoices', 'Tracks if the Psychologist Invoices have been generated');
$GLOBALS['TL_LANG']['tl_invoice_request']['generated_districts']            = array('Generated District Invoices', 'Tracks if the District Invoices have been generated');
