<?php

namespace Bcs\Backend;

use Contao\Backend;
use Contao\Image;
use Contao\Input;
use Contao\DataContainer;
use Contao\StringUtil;


class AlertEmailBackend extends Backend
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
		if (is_array($GLOBALS['TL_DCA']['tl_alert_email']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_alert_email']['fields']['published']['save_callback'] as $callback)
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
		$this->Database->prepare("UPDATE tl_alert_email SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")->execute($intId);
		//$this->log('A new version of record "tl_district.id='.$intId.'" has been created'.$this->getParentEntries('tl_listing', $intId), __METHOD__, TL_GENERAL);
	}

}
