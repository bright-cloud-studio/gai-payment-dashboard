<?php

use Bcs\Model\Service;
use Contao\DataContainer;
use Contao\DC_Table;

/* Psychologist - Custom Fields */

 /* Extend the tl_user palettes */
foreach ($GLOBALS['TL_DCA']['tl_member']['palettes'] as $k => $v) {
    $GLOBALS['TL_DCA']['tl_member']['palettes'][$k] = str_replace('groups;', 'groups;{meetings_legend}, price_tier, price_tier_display;{price_tier_legend},price_tier;', $v);
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
    'eval'                    => array('mandatory'=>false, 'multiple'=>false, 'tl_class'=>'w50', 'hideInput'="'true'),
    'options_callback'	      => array('Bcs\Backend\ServiceBackend', 'getPriceTierDisplay'),
    'sql'                     => "TEXT(20000) NOT NULL default ''"
);

