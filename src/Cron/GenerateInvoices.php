<?php

namespace App\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;

#[AsCronJob('minutely')]
class GenerateInvoices
{
    public function __invoke()
    {
        $this->logger?->info('GAI Cron Hit!');
    }
}
