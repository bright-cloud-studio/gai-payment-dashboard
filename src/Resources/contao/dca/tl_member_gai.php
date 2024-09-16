<?php


class tl_member_gai extends tl_member
{
	/**
	 * Generate a "switch account" button and return it as string
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 *
	 * @return string
	 */
	public function switchUser($row, $href, $label, $title, $icon)
	{
        echo "BAM!";
        die();

        
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

		return '<a href="' . StringUtil::specialcharsUrl($url) . '" title="' . StringUtil::specialchars($title) . '" target="_blank">' . Image::getHtml($icon, $label) . '</a> ';
	}

}
