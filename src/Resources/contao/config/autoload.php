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
    'Bcs\Backend\TransactionsBackend'           => 'system/modules/gai_invoices/library/Bcs/Backend/TransactionsBackend.php',
    'Bcs\Model\Transactions'                    => 'system/modules/gai_invoices/library/Bcs/Model/Transactions.php',
    
));
