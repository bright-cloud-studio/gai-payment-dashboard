<?php

namespace Bcs\Cron;

class GenerateInvoices
{
    public function __invoke()
    {
        $this->logger?->info('GAI Cron Hit!');
    }
}
