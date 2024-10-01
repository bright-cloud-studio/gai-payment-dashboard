<?php

declare(strict_types=1);


namespace Bcs\Cron;

use Bcs\Model\InvoiceRequest;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\CoreBundle\Framework\ContaoFramework;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

#[AsCronJob('minutely')]
class Generate
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly Connection $connection,
        private readonly string $deleteFeedbacksAfter,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        $this->framework->initialize();

        $inv = new InvoiceRequest();
        $inv->date_start = rand(1,999);
        $inv->date_end = rand(1,999);
        $inv->save();
    }
}
