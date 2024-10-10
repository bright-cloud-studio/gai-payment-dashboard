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
    'sql' => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_member']['fields']['psych_work_form_colors'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['psych_work_form_colors'],
    'inputType'               => 'text',
    'default'                 => '',
    'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr'),
    'sql'                     => "text NULL"
);
