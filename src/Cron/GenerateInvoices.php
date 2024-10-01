<?php

declare(strict_types=1);

namespace Bcs\\PaymentDashboardBundle\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\Validator;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

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
        $this->contaoCronLogger->info("GAI HIT!");
    }
}
