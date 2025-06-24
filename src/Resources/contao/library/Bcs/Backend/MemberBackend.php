<?php

namespace Bcs\Backend;

use Contao\Backend;
use Contao\Image;
use Contao\Input;
use Contao\DataContainer;
use Contao\StringUtil;

use Bcs\Model\Transaction;
use Bcs\Model\Assignment;
use Bcs\Model\Student;

class MemberBackend extends Backend
{
  public function switchUser($row, $href, $label, $title, $icon)
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

		$url = System::getContainer()->get('router')->generate('contao_backend_preview', array('user'=>$row['username']));

		return '<a href="' . StringUtil::specialcharsUrl($url) . '" target="_blank" data-turbo-prefetch="false">' . Image::getHtml($icon, $title) . '</a> ';
	}
}
