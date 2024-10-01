<?php

declare(strict_types=1);


namespace Bcs\Crons;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

#[AsCronJob('minutely')]
class GenerateInvoices
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly string $projectDir,
        private readonly LoggerInterface|null $logger,
    ) {
    }

    public function __invoke(): void
    {
        //$finder = Finder::create()->in(Path::join($this->projectDir, 'system/tmp'));

        //$this->filesystem->remove($finder->getIterator());

        $this->logger?->info('GAI Cron Hit!');
    }
}
