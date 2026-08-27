<?php

namespace Bcs\Backend;

use Contao\Backend;
use Contao\BackendUser;
use Contao\FrontendUser;
use Contao\Image;
use Contao\Input;
use Contao\DataContainer;
use Contao\StringUtil;
use Contao\System;
use Contao\CoreBundle\DataContainer\DataContainerOperation;

use Bcs\Model\District;
use Bcs\Model\Transaction;
use Bcs\Model\Assignment;
use Bcs\Model\Student;

class MemberBackend extends Backend
{
    
    // Displays this Psychologist's Assignments and tracks which ones to hide from the Psych Work Form
    public function getHiddenAssignments(DataContainer $dc) {

        $hidden_assignments;
        if($dc->activeRecord) {
            
            $hidden_assignments = unserialize($dc->activeRecord->pwf_hidden_assignments);
        }
        
        $assignments = array();
        $this->import('Database');
        $result = $this->Database->prepare("SELECT * FROM tl_assignment ORDER BY date_created DESC")->execute();
        while($result->next())
        {
            if($result->psychologist == $dc->activeRecord->id) {
                $d = District::findBy('id', $result->district);
                $assignments = $assignments + array($result->id => '[ID: '.$result->id.'] ' . date('m/d/y', strtotime($result->date_created)) . ' - ' . $d->district_name);
            }
        }
        return $assignments;
    }

    


    
    public function switchUserCustomized($row, $href, $label, $title, $icon)
	{
		$user = BackendUser::getInstance();
		$blnCanSwitchUser = $user->isAdmin || (!empty($user->amg) && is_array($user->amg));

		if (!$blnCanSwitchUser)
		{
			return '';
		}

		if (!$row['login'] || !$row['username'] || (!$user->isAdmin && count(array_intersect(StringUtil::deserialize($row['groups'], true), $user->amg)) < 1))
		{
			return Image::getHtml(str_replace('.svg', '--disabled.svg', $icon)) . ' ';
		}

		$url = System::getContainer()->get('router')->generate('contao_backend_preview', array('page'=>'19', 'user'=>$row['username']));

		return '<a href="' . StringUtil::specialcharsUrl($url) . '" target="_blank" data-turbo-prefetch="false">' . Image::getHtml($icon, $title) . '</a> ';
	}

    /**
     * Ask for confirmation before the native publish toggle fires.
     *
     * Contao renders toggle operations itself and ignores their "attributes", so the confirm
     * has to come from a button_callback that returns the anchor. Core's own callback is kept
     * and called first so the permission check still applies.
     */
    public function addToggleConfirmation(DataContainer $dc)
    {
        if (!isset($GLOBALS['TL_DCA']['tl_member']['list']['operations']['toggle']))
        {
            return;
        }

        $arrToggle = &$GLOBALS['TL_DCA']['tl_member']['list']['operations']['toggle'];
        $varDefault = $arrToggle['button_callback'] ?? null;
        $arrLabel = $arrToggle['label'] ?? null;

        $arrToggle['button_callback'] = static function (DataContainerOperation $operation) use ($varDefault, $arrLabel)
        {
            // Let core run first - it drops the href when the user may not edit this Member
            if ($varDefault !== null)
            {
                $varDefault($operation);
            }

            // "disable" is a reverse toggle, so an unchecked record is the published one
            $arrRow = $operation->getRecord();
            $intState = $arrRow['disable'] ? 0 : 1;
            $strIcon = $intState ? 'visible.svg' : 'invisible.svg';

            // Render a plain icon if toggling is not allowed
            if (!isset($operation['href']))
            {
                $operation->setHtml(Image::getHtml($strIcon, $operation['label']) . ' ');

                return;
            }

            // Keep both titles so the tooltip still flips after the Ajax call
            $strTitle = $operation['title'];
            $strTitleDisabled = (is_array($arrLabel) && isset($arrLabel[2])) ? sprintf($arrLabel[2], $arrRow['id']) : $strTitle;

            $strConfirm = StringUtil::specialchars(str_replace("'", "\\'", $GLOBALS['TL_LANG']['tl_member']['confirmToggle']));
            $strHref = Backend::addToUrl($operation['href'] . '&amp;id=' . $arrRow['id']);

            $operation->setHtml(
                '<a href="' . $strHref . '"'
                . ' title="' . StringUtil::specialchars($intState ? $strTitle : $strTitleDisabled) . '"'
                . ' data-title="' . StringUtil::specialchars($strTitle) . '"'
                . ' data-title-disabled="' . StringUtil::specialchars($strTitleDisabled) . '"'
                . ' data-action="contao--scroll-offset#store"'
                . ' onclick="if(!confirm(\'' . $strConfirm . '\')) return false; return AjaxRequest.toggleField(this,true)">'
                . Image::getHtml($strIcon, $operation['label'], 'data-icon="visible.svg" data-icon-disabled="invisible.svg" data-state="' . $intState . '"')
                . '</a> '
            );
        };
    }
}
