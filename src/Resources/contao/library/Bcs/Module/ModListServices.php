<?php

/**
* Bright Cloud Studio's GAI Payment Dashboard
*
* Copyright (C) 2024-2025 Bright Cloud Studio
*
* @package    bright-cloud-studio/gai-payment-dashboard
* @link       https://www.brightcloudstudio.com/
* @license    http://opensource.org/licenses/lgpl-3.0.html
**/

namespace Bcs\Module;

use Bcs\Model\Service;
use Bcs\Model\PriceTier;


use Contao\BackendTemplate;
use Contao\System;

use Contao\FrontendUser;

class ModListServices extends \Contao\Module
{

    /* Default Template */
    protected $strTemplate = 'mod_assignments';
    
    protected static $service_prices = array();

    /* Construct function */
    public function __construct($objModule, $strColumn='main')
	{
        parent::__construct($objModule, $strColumn);
	}

    /* Generate function */
    public function generate()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
        {
            $objTemplate = new BackendTemplate('be_wildcard');
 
            $objTemplate->wildcard = '### ' . mb_strtoupper($GLOBALS['TL_LANG']['FMD']['assignments'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&table=tl_module&act=edit&id=' . $this->id;
 
            return $objTemplate->parse();
        }
 
        return parent::generate();
    }


    protected function compile()
    {
        $member = FrontendUser::getInstance();
        
        $services = Service::findBy('published', '1');
        foreach($services as $service) {

            // Create an array of services codes linked to prices then add them to the template so we can grab them with jQuery
            
            $prices = PriceTier::findBy('pid', $service->id);
            foreach($prices as $price) {
                if(in_array($price->id, $member->price_tier_assignments)) {
                    $service_prices[$service->service_code]['price'] = $price->tier_price;
                    $service_prices[$service->service_code]['service_type'] = $service->service_type;
                    $service_prices[$service->service_code]['service_name'] = $service->name;
                }
            }
            
            
        }
        
        $this->Template->service_prices = $service_prices;
        
    }
  

}
