<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Bcs\Cron;

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\Cache\EntityCacheTags;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\System;

class GenerateInvoices
{
    public function __construct(
        private ContaoFramework $framework,
        private EntityCacheTags $entityCacheTags,
    ) {
        $this->framework->initialize();
    }

    public function updateEvents(): void
    {
        System::getContainer()
                ->get('monolog.logger.contao.cron')
                ->info('GAI WOOO')
            ;
    }
}
