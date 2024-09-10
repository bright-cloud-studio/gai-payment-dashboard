<?php

namespace Bcs\RecaptchaBundle;

use Contao\FormSelect;
use Contao\Config;
use Contao\FormModel;

class FormSelectDynamic extends FormSelect
{
    protected $strTemplate = 'form_select_dynamic';
    
    public function __set($strKey, $varValue): void
    {
        parent::__set($strKey, $varValue);
    }
    
    public function __get($strKey)
    {
        return parent::__get($strKey);
    }
    
    public function validate()
  	{
  		
  	}
  	
  	
  	protected function getOptions(): array
    {
        return parent::getOptions();
    }
  	
  	
  	
}
