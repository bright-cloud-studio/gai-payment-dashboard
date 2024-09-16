<?php

use Bcs\Model\Service;
use Contao\DataContainer;
use Contao\DC_Table;

/* Psychologist - Custom Fields */

 /* Extend the tl_user palettes */
foreach ($GLOBALS['TL_DCA']['tl_member']['palettes'] as $k => $v) {
    $GLOBALS['TL_DCA']['tl_member']['palettes'][$k] = str_replace('groups;', 'groups;{price_tier_legend}, price_tier, price_tier_display;', $v);
}
    
/* Add fields to tl_user */
$GLOBALS['TL_DCA']['tl_member']['fields']['price_tier'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['price_tier'],
    'inputType'               => 'select',
    'flag'                    => DataContainer::SORT_ASC,
    'eval'                    => array('mandatory'=>true, 'multiple'=>false, 'tl_class'=>''),
    'options_callback'	      => array('Bcs\Backend\ServiceBackend', 'getPriceTiers'),
    'sql'                     => "varchar(50) NOT NULL default ''",
    'default'                 => "tier_1_price"
);

$GLOBALS['TL_DCA']['tl_member']['fields']['price_tier_display'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['price_tier_display'],
    'inputType'               => 'checkbox',
    'flag'                    => DataContainer::SORT_ASC,
    'eval'                    => array('mandatory'=>false, 'multiple'=>true, 'tl_class'=>'clr', 'hideInput'=>true),
    'options_callback'	      => array('Bcs\Backend\ServiceBackend', 'getPriceTierDisplay'),
    'sql'                     => "TEXT(20000) NOT NULL default ''"
);


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
