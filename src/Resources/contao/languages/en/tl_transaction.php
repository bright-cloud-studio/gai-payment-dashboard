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
$GLOBALS['TL_LANG']['tl_transaction']['new']                        = array('New record', 'Add a new record');
$GLOBALS['TL_LANG']['tl_transaction']['show']                       = array('Record details', 'Show the details of record ID %s');
$GLOBALS['TL_LANG']['tl_transaction']['edit']                       = array('Edit record', 'Edit record ID %s');
$GLOBALS['TL_LANG']['tl_transaction']['copy']                       = array('Copy record', 'Copy record ID %s');
$GLOBALS['TL_LANG']['tl_transaction']['delete']                     = array('Delete record', 'Delete record ID %s');
$GLOBALS['TL_LANG']['tl_transaction']['toggle']                     = array('Toggle record', 'Toggle record ID %s');

/* System Fields */
$GLOBALS['TL_LANG']['tl_transaction']['publish_legend']             = 'Publish';
$GLOBALS['TL_LANG']['tl_transaction']['alias']                      = array('Alias', 'Auto-generated alias.');
$GLOBALS['TL_LANG']['tl_transaction']['published']                  = array('Published', 'Show this record on the front end.');

/* Fields */
$GLOBALS['TL_LANG']['tl_transaction']['transaction_legend']         = 'Transaction Information';
$GLOBALS['TL_LANG']['tl_transaction']['date']                       = array('Date', 'Today\'s Date');
$GLOBALS['TL_LANG']['tl_transaction']['psychologist']               = array('Psychologist', 'Psycholigist this transactions is for');
$GLOBALS['TL_LANG']['tl_transaction']['district']                   = array('District', 'District the school is in');
$GLOBALS['TL_LANG']['tl_transaction']['school']                     = array('School', 'The name of the school');
$GLOBALS['TL_LANG']['tl_transaction']['student_name']               = array('Student Name', 'The initials of the student');
$GLOBALS['TL_LANG']['tl_transaction']['service_provided']           = array('Service Provided', 'The ID of the service being provided');
$GLOBALS['TL_LANG']['tl_transaction']['price']                      = array('Price', 'Price for this transaction');
$GLOBALS['TL_LANG']['tl_transaction']['lasid']                      = array('LASID', 'The LASID assigned to the student');
$GLOBALS['TL_LANG']['tl_transaction']['sasid']                      = array('SASID', 'The SASID assigned to the student');
$GLOBALS['TL_LANG']['tl_transaction']['meeting_date']               = array('Meeting Date', 'The date the meeting occured');
$GLOBALS['TL_LANG']['tl_transaction']['meeting_start']              = array('Meeting Start', 'The time the meeting started');
$GLOBALS['TL_LANG']['tl_transaction']['meeting_end']                = array('Meeting End', 'The time the meeting ended');
$GLOBALS['TL_LANG']['tl_transaction']['meeting_duration']           = array('Meeting Duration', 'The amount of minutes the meeting lasted for');
$GLOBALS['TL_LANG']['tl_transaction']['notes']                      = array('Notes', 'Notes added by the psychologist');
$GLOBALS['TL_LANG']['tl_transaction']['internal_legend']            = 'FOR INTERNAL USE ONLY';
$GLOBALS['TL_LANG']['tl_transaction']['reviewed']                   = array('Reviewed', 'Tracks if the psychologist has performed their mandatory review');
$GLOBALS['TL_LANG']['tl_transaction']['deleted']                    = array('Deleted', 'Tracks if the psychologist has deleted this transactions');
$GLOBALS['TL_LANG']['tl_transaction']['misc_billing']               = array('Misc. Billing', 'The display name for a Misc. Billing transaction');
$GLOBALS['TL_LANG']['tl_transaction']['sheet_row']                  = array('Sheet Row', 'The row of the Work Assignment this transaction was generated for');
$GLOBALS['TL_LANG']['tl_transaction']['label']                      = array('Label', 'The label used as the main descriptor for Misc. Billing Transactions');
