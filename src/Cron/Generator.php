<?php

declare(strict_types=1);

namespace Bcs\PaymentDashboardBundle\Cron;


use Contao\Config;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Environment;
use Contao\StringUtil;
use Contao\System;
use Cron\CronExpression;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCronJob('minutely')]
class Generator
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly string $projectDir,
        private readonly LoggerInterface|null $logger,
        private readonly Connection $connection,
        private readonly TranslatorInterface $translator,
        private readonly ContaoFramework $framework,
    ) {
    }

    public function __invoke(string $scope): void
    {
        $this->logger?->info('Start: Generator');
    }

}

// vendor/bin/contao-console debug:container --tag contao.cronjob
