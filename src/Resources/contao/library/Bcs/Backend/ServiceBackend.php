<?php

namespace Bcs\Backend;

use Contao\Backend;
use Contao\Image;
use Contao\Input;
use Contao\DataContainer;
use Contao\StringUtil;

use Bcs\Model\Service;
use Bcs\Model\PriceTier;


class ServiceBackend extends Backend
{
  
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
      if (strlen(Input::get('tid')))
      {
        $this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
        $this->redirect($this->getReferer());
      }

  		$href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);
  
  		if (!$row['published'])
  		{
  			$icon = 'invisible.gif';
  		}

		  return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
	}	
	

	public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
	{
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_service']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_service']['fields']['published']['save_callback'] as $callback)
			{
  				if (is_array($callback))
  				{
  					$this->import($callback[0]);
  					$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, ($dc ?: $this));
  				}
  				elseif (is_callable($callback))
  				{
  					$blnVisible = $callback($blnVisible, ($dc ?: $this));
  				}
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_service SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")->execute($intId);
	}
	
	public function generateAlias($varValue, DataContainer $dc)
	{
		$autoAlias = false;
		
		// Generate an alias if there is none
		if ($varValue == '')
		{
			$autoAlias = true;
			$varValue = standardize(StringUtil::restoreBasicEntities($dc->activeRecord->name));
		}

		$objAlias = $this->Database->prepare("SELECT id FROM tl_service WHERE id=? OR alias=?")->execute($dc->id, $varValue);

		// Check whether the page alias exists
		if ($objAlias->numRows > 1)
		{
			if (!$autoAlias)
			{
				throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
			}

			$varValue .= '-' . $dc->id;
		}

		return $varValue;
	}

     // Get Price Tiers as select menu
    public function getPriceTiers(DataContainer $dc) { 
    
        // Hold the psys
        $tiers = array();
        $tiers = $tiers + array('price_tier_1' => 'Price Tier 1');
        $tiers = $tiers + array('price_tier_2' => 'Price Tier 2');
        $tiers = $tiers + array('price_tier_3' => 'Price Tier 3');
        $tiers = $tiers + array('price_tier_4' => 'Price Tier 4');
        $tiers = $tiers + array('price_tier_5' => 'Price Tier 5');
        $tiers = $tiers + array('price_tier_6' => 'Price Tier 6');
        $tiers = $tiers + array('price_tier_7' => 'Price Tier 7');
        $tiers = $tiers + array('price_tier_8' => 'Price Tier 8');
        $tiers = $tiers + array('price_tier_9' => 'Price Tier 9');
        $tiers = $tiers + array('price_tier_10' => 'Price Tier 10');
        
        return $tiers;
    
	}

    public function getPriceTierDisplay(DataContainer $dc) {
        
        // Hold the psys
        $options = array();
        $services = Service::findBy('published', '1');
        
        // loop through each service
        foreach($services as $service) {
            // loop through each service's tiers
            $tiers = PriceTier::findBy('pid', $service->id);

            foreach($tiers as $tier) {
                $options[$service->name][$tier->id] = "$" . $tier->tier_type . " " . $tier->tier_price;
            }
        }
        return $options;
    }
        

    

}
