<?php

/**
 * @copyright  Bright Cliud Studio
 * @author     Bright Cloud Studio
 * @package    GAI Payment Dashboard
 * @license    LGPL-3.0+
 * @see	       https://github.com/bright-cloud-studio/gai-payment-dashboard
 */

namespace Bcs\PaymentDashboardBundle\ContaoManager;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create('Bcs\PaymentDashboardBundle\BcsPaymentDashboardBundle')
                ->setLoadAfter(['Contao\CoreBundle\ContaoCoreBundle']),
        ];
    }
}
