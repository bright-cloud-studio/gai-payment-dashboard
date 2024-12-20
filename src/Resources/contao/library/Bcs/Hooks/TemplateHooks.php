<?php

namespace Bcs\Hooks;

use Bcs\Model\Service;
use Bcs\Model\Transaction;
use Bcs\Model\TransactionMisc;

use Contao\Controller;
use Contao\Environment;
use Contao\FrontendUser;
use Contao\Input;
use Contao\MemberModel;
use Contao\PageModel;
use Contao\Template;

class TemplateHooks
{
    protected static $arrUserOptions = array();

    // When a form is submitted
    public function onParseTemplate($template)
    {
        if ('be_welcome' === $template->getName()) {
            $template->total_daily = '1000.00';
        }
    }
  
}
