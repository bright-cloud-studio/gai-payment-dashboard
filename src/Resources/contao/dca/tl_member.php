<?php

use Bcs\Model\Service;
use Contao\DataContainer;
use Contao\DC_Table;

/* Psychologist - Custom Fields */

 /* Extend the tl_user palettes */
foreach ($GLOBALS['TL_DCA']['tl_member']['palettes'] as $k => $v) {
    $GLOBALS['TL_DCA']['tl_member']['palettes'][$k] = str_replace('groups;', 'groups;{meetings_legend},price_tier_assignments;{price_tier_legend},price_tier;', $v);
}
    
/* Add fields to tl_user */
$GLOBALS['TL_DCA']['tl_member']['fields']['price_tier_assignments'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['price_tier_assignments'],
    'inputType'               => 'checkbox',
    'flag'                    => DataContainer::SORT_ASC,
    'eval'                    => array('mandatory'=>false, 'multiple'=>true, 'tl_class'=>'w50'),
    'options_callback'	      => array('Bcs\Backend\ServiceBackend', 'getPriceTierAssignmentOptions'),
    'sql'                     => "TEXT(20000) NOT NULL default ''"
);

