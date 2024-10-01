<?php

declare(strict_types=1);

namespace Bcs\PaymentDashboardBundle\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;


#[AsCronJob('minutely')]
class GenerateInvoices
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Filesystem $filesystem,
        private readonly LoggerInterface|null $contaoCronLogger = null,
    ) {
    }

    public function __invoke(): void
    {
        echo "blamo";
    }
}
