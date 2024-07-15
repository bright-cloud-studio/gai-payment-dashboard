<?php

namespace Bcs\Backend;

use Contao\Backend;
use Contao\Image;
use Contao\Input;
use Contao\DataContainer;
use Contao\StringUtil;

use Bcs\Model\Service;


class ServiceBackend extends Backend
{
  
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
      if (strlen(Input::get('tid')))
      {
        $this->toggleVisibility(\Input::get('tid'), (\Input::get('state') == 1), (@func_get_arg(12) ?: null));
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
		//$this->log('A new version of record "tl_service.id='.$intId.'" has been created'.$this->getParentEntries('tl_listing', $intId), __METHOD__, TL_GENERAL);
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



    public function getPriceTierAssignmentOptions(DataContainer $dc) {
        // Hold the psys
        $services = array();

        // Use the DB to grab all of our enabled members, aka our psychologists
		$this->import('Database');
		$result = $this->Database->prepare("SELECT * FROM tl_service WHERE published=1")->execute();
		while($result->next())
		{
            // Add ti array with ID as the value and firstname lastname as the label
            $services = $services + array($result->service_code => $result->name);   
		}

		return $services;
    }
        

    

}
