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
$GLOBALS['TL_LANG']['tl_transaction_misc']['new']                        = array('New Transaction', 'Add a new record');
$GLOBALS['TL_LANG']['tl_transaction_misc']['show']                       = array('Transaction Details', 'Show the details of record ID %s');
$GLOBALS['TL_LANG']['tl_transaction_misc']['edit']                       = array('Edit Transaction', 'Edit record ID %s');
$GLOBALS['TL_LANG']['tl_transaction_misc']['copy']                       = array('Copy Transaction', 'Copy record ID %s');
$GLOBALS['TL_LANG']['tl_transaction_misc']['delete']                     = array('Delete Transaction', 'Delete record ID %s');
$GLOBALS['TL_LANG']['tl_transaction_misc']['toggle']                     = array('Toggle Transaction', 'Toggle record ID %s');

/* System Fields */
$GLOBALS['TL_LANG']['tl_transaction_misc']['alias']                      = array('Alias', 'Auto-generated alias.');
$GLOBALS['TL_LANG']['tl_transaction_misc']['published']                  = array('Reviewed', 'Indicates if this Transaction has been Reviewed by the submitting Psychologist');

/* Fields */
$GLOBALS['TL_LANG']['tl_transaction_misc']['transaction_legend']         = 'Misc. Transaction Information';
$GLOBALS['TL_LANG']['tl_transaction_misc']['date_submitted']             = array('Date Submitted', 'The date this Misc. Transaction was created');
$GLOBALS['TL_LANG']['tl_transaction_misc']['psychologist']               = array('Psychologist', 'The Psychologist who created this Misc. Transaction');
$GLOBALS['TL_LANG']['tl_transaction_misc']['service']                    = array('Service', 'The Service Code assigned to this Misc. Transaction');
$GLOBALS['TL_LANG']['tl_transaction_misc']['service_label']              = array('Service Label', 'The manual label entered when creating this Misc. Transaction');
$GLOBALS['TL_LANG']['tl_transaction_misc']['price']                      = array('Price', 'The Price Tier price assigned to the selected Service');


$GLOBALS['TL_LANG']['tl_transaction_misc']['meeting_legend']             = 'Meeting Information';
$GLOBALS['TL_LANG']['tl_transaction_misc']['meeting_date']               = array('Meeting Date', 'The date whcih a Meeting service has taken place');
$GLOBALS['TL_LANG']['tl_transaction_misc']['meeting_start']              = array('Meeting Start', 'The start time of the meeting');
$GLOBALS['TL_LANG']['tl_transaction_misc']['meeting_end']                = array('Meeting End', 'The end time of the meeting');
$GLOBALS['TL_LANG']['tl_transaction_misc']['meeting_duration']           = array('Meeting Duration', 'The total duration of the meeting in minutes');

$GLOBALS['TL_LANG']['tl_transaction_misc']['notes_legend']               = 'Notes';
$GLOBALS['TL_LANG']['tl_transaction_misc']['notes']                      = array('Notes', 'Custom notes entered by the Psychologist who created this Misc. Transaction');

$GLOBALS['TL_LANG']['tl_transaction_misc']['publish_legend']             = 'Reviewed Information';
$GLOBALS['TL_LANG']['tl_transaction_misc']['reviewed']                   = array('Reviewed', 'Indicates if this Misc. Transaction has been Reviewed by the submitting Psychologist');


$GLOBALS['TL_LANG']['tl_transaction_misc']['district']                   = array('District', 'The District selected by the Psychologist who created this Misc. Transaction');
$GLOBALS['TL_LANG']['tl_transaction_misc']['school']                     = array('School', 'The School selected by the Psychologist who created this Misc. Transaction');

$GLOBALS['TL_LANG']['tl_transaction_misc']['student_legend']             = 'Student Information';
$GLOBALS['TL_LANG']['tl_transaction_misc']['student_initials']           = array('Student Initials', 'The initials of the Student who this Misc. Transaction is about');
$GLOBALS['TL_LANG']['tl_transaction_misc']['lasid']                      = array('LASID', 'The assigned LASID for the Student');
$GLOBALS['TL_LANG']['tl_transaction_misc']['sasid']                      = array('SASID', 'The assigned SASID for the Student');


$GLOBALS['TL_LANG']['tl_transaction_misc']['internal_legend']             = 'INTERNAL Information';
$GLOBALS['TL_LANG']['tl_transaction_misc']['originally_submitted']        = array('Original Date Submitted', 'If this Transaction is not Reviewed and is carried forward, this will track the original Date Submitted before manually updating to the first of the current month');
