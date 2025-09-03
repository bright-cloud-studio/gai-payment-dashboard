<?php

namespace Bcs\Backend;

use Contao\Backend;
use Contao\BackendUser;
use Contao\Image;
use Contao\Input;
use Contao\DataContainer;
use Contao\StringUtil;
use Contao\System;

use Bcs\Model\Transaction;
use Bcs\Model\Assignment;
use Bcs\Model\Student;

class MemberBackend extends Backend
{
    
    // Get Members as options for a Select DCA field
    public function getHiddenAssignments(DataContainer $dc) {
        $assignments = array();
        $this->import('Database');
        $result = $this->Database->prepare("SELECT * FROM tl_assignment ORDER BY id ASC")->execute();
        while($result->next())
        {
            $assignments = $assignments + array($result->id => $result->id);   
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
}
