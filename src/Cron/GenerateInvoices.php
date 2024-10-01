<?php

namespace App\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;

#[AsCronJob('minutely')]
class InactiveMembershipCron
{
    public function __invoke(): void
    {
        $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . "cron_log_".date('m-d-Y_hia').".txt", "w") or die("Unable to open file!");
        fwrite($myfile, "CRON Triggered! \n");
        fclose($myfile);
        
    }
}
