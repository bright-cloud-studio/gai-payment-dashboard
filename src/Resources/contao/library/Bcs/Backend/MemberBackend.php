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
}
