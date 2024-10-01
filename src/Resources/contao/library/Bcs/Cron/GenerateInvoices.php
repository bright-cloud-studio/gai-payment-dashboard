<?php

declare(strict_types=1);

namespace Bcs\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Terminal42\NotificationCenterBundle\BulkyItem\BulkyItemStorage;

#[AsCronJob('minutely')]
class GenerateInvoices
{
    public function __invoke(): void
    {
        echo "HIT HIT HIT";
        die();
    }
}
