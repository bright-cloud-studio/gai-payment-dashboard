<?php

/**
 * Bright Cloud Studio's GAI Invoices
 *
 * Copyright (C) 2021 Bright Cloud Studio
 *
 * @package    bright-cloud-studio/gai-invoices
 * @link       https://www.brightcloudstudio.com/
 * @license    http://opensource.org/licenses/lgpl-3.0.html
**/

/* Register the classes */
ClassLoader::addClasses(array
(
    // Our Transactions
    'Bcs\Backend\TransactionBackend'           => 'system/modules/gai_invoices/library/Bcs/Backend/TransactionBackend.php',
    'Bcs\Model\Transaction'                    => 'system/modules/gai_invoices/library/Bcs/Model/Transaction.php',

    'Bcs\Backend\AssignmentBackend'           => 'system/modules/gai_invoices/library/Bcs/Backend/AssignmentBackend.php',
    'Bcs\Model\Assignment'                    => 'system/modules/gai_invoices/library/Bcs/Model/Assignment.php',
    
));
