<?php

namespace Bcs\RecaptchaBundle;

use Contao\FormSelect;
use Contao\Config;
use Contao\FormModel;

class FormSelectDynamic extends FormSelect
{
    protected $strTemplate = 'form_select_dynamic';
    
    public function __generate()
    {
        parent::__generate();
    }
    
    public function validate()
  	{
  		$mandatory = $this->mandatory;
  		$options = $this->getPost($this->strName);
  
  		// Check if there is at least one value
  		if ($mandatory && \is_array($options))
  		{
  			foreach ($options as $option)
  			{
  				if (\strlen($option))
  				{
  					$this->mandatory = false;
  					break;
  				}
  			}
  		}
  
  		$varInput = $this->validator($options);
      
  
  		// Add class "error"
  		if ($this->hasErrors())
  		{
  			$this->class = 'error';
  		}
  		else
  		{
  			$this->varValue = $varInput;
  		}
  
  		// Reset the property
  		if ($mandatory)
  		{
  			$this->mandatory = true;
  		}
  	}
}
