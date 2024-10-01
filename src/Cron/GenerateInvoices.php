<?php

declare(strict_types=1);

namespace Bcs\PaymentDashboardBundle\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;


/** 
 * @CronJob("minutely")
 */
class GenerateInvoices
{
    public function __invoke()
    {

        $this->contaoCronLogger->info("GAI Cron Triggered!");
        
        $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . "cron_log_".date('m-d-Y_hia').".txt", "w") or die("Unable to open file!");
        fwrite($myfile, "CRON Triggered! \n");
        fclose($myfile);

        
        
    }
}
