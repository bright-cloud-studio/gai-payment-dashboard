<?php

namespace App\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;

#[AsCronJob('minutely')]
class GenerateInvoices
{
    public function __invoke(): void
    {

        if (null !== $this->contaoCronLogger) {
            $this->contaoCronLogger->info("GAI Cron Triggered!");
        }
        
        $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . "cron_log_".date('m-d-Y_hia').".txt", "w") or die("Unable to open file!");
        fwrite($myfile, "CRON Triggered! \n");
        fclose($myfile);

        
        
    }
}
