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
$GLOBALS['TL_LANG']['tl_transaction']['new']                        = array('New Transaction', 'Add a new record');
$GLOBALS['TL_LANG']['tl_transaction']['show']                       = array('Transaction Details', 'Show the details of record ID %s');
$GLOBALS['TL_LANG']['tl_transaction']['edit']                       = array('Edit Transaction', 'Edit record ID %s');
$GLOBALS['TL_LANG']['tl_transaction']['copy']                       = array('Copy Transaction', 'Copy record ID %s');
$GLOBALS['TL_LANG']['tl_transaction']['delete']                     = array('Delete Transaction', 'Delete record ID %s');
$GLOBALS['TL_LANG']['tl_transaction']['toggle']                     = array('Toggle Transaction', 'Toggle record ID %s');

/* System Fields */
$GLOBALS['TL_LANG']['tl_transaction']['alias']                      = array('Alias', 'Auto-generated alias.');

/* Assignment Display */
$GLOBALS['TL_LANG']['tl_transaction']['assignment_details_legend']  = 'Assignment Details';

/* Fields */
$GLOBALS['TL_LANG']['tl_transaction']['transaction_legend']         = 'Transaction Information';
$GLOBALS['TL_LANG']['tl_transaction']['date_submitted']             = array('Date Submitted', 'The date this Transaction was created. This is the date the generator uses to determine which month it gets invoiced. Will be automatically updated to the first of each month if not Reviewed');
$GLOBALS['TL_LANG']['tl_transaction']['psychologist']               = array('Psychologist', 'The Psychologist who created this Transaction');
$GLOBALS['TL_LANG']['tl_transaction']['service']                    = array('Service', 'The Service this Transaction represents');
$GLOBALS['TL_LANG']['tl_transaction']['price']                      = array('Price', 'The price for this Service, based on the Price Tier assigned to the Psychologist');
$GLOBALS['TL_LANG']['tl_transaction']['meeting_legend']             = 'Meeting Information';
$GLOBALS['TL_LANG']['tl_transaction']['meeting_date']               = array('Meeting Date', 'If a Meeting type Service, the date in which the Meeting took place');
$GLOBALS['TL_LANG']['tl_transaction']['meeting_start']              = array('Meeting Start', 'If a Meeting type Service, the Start Time of the meeting');
$GLOBALS['TL_LANG']['tl_transaction']['meeting_end']                = array('Meeting End', 'If a Meeting type Service, the End Time of the meeting');
$GLOBALS['TL_LANG']['tl_transaction']['meeting_duration']           = array('Meeting Duration', 'Calculated duration, in minutes, of the length of the meeting. Used when calculating the final price during generation');

$GLOBALS['TL_LANG']['tl_transaction']['notes_legend']               = 'Notes';
$GLOBALS['TL_LANG']['tl_transaction']['notes']                      = array('Notes', 'Manually entered notes relating to this Assignment and Transaction, entered by the Psychologist');

$GLOBALS['TL_LANG']['tl_transaction']['status_legend']              = 'Status Details';
$GLOBALS['TL_LANG']['tl_transaction']['status']                    = array('Status', 'Tracks the current Status of this Transaction. Status is updated throughout the generation process at each step of the process');

$GLOBALS['TL_LANG']['tl_transaction']['publish_legend']             = 'Reviewed Information';
$GLOBALS['TL_LANG']['tl_transaction']['published']                  = array('Reviewed', 'Indicates if this Transaction has been Reviewed by the submitting Psychologist');

/* Internal */
$GLOBALS['TL_LANG']['tl_transaction']['internal_legend']           = 'Internal Information';
$GLOBALS['TL_LANG']['tl_transaction']['district']                  = array('District', 'Parent Assignment\'s selected District value, copied here to enable Search and Filter');
$GLOBALS['TL_LANG']['tl_transaction']['lasid']                     = array('LASID', 'Parent Assignment\'s selected Student\'s LASID value, copied here to enable Search and Filter');
$GLOBALS['TL_LANG']['tl_transaction']['sasid']                     = array('SASID', 'Parent Assignment\'s selected Student\'s SASID value, copied here to enable Search and Filter');
$GLOBALS['TL_LANG']['tl_transaction']['originally_submitted']      = array('Original Date Submitted', 'If this Transaction is not Reviewed and is carried forward, this will track the original Date Submitted before manually updating to the first of the current month');
