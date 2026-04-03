<?php

use Bcs\Model\Service;
use Contao\Backend;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Image;
use Contao\StringUtil;

/* Override the switch user function with our custom one that routes the user to the Dashboard instead of the homepage */
$GLOBALS['TL_DCA']['tl_member']['list']['operations']['su']['button_callback'] = array('Bcs\Backend\MemberBackend', 'switchUserCustomized');

/* Add a confirmation prompt for toggling */
$GLOBALS['TL_DCA']['tl_member']['list']['operations']['toggle']['button_callback'] = static function (
    array   $row,
    ?string $href,
    string  $label,
    string  $title,
    ?string $icon,
    string  $attributes,
    string  $table
): string {
    // tl_member toggle is on the 'disable' field with reverse=true
    // so active = disable is falsy
    $isActive    = empty($row['disable']);
    $activeIcon  = 'visible.svg';
    $inactiveIcon = 'invisible.svg';
    $currentIcon = $isActive ? $activeIcon : $inactiveIcon;

    $confirmMsg = addslashes(
        $GLOBALS['TL_LANG']['MSC']['confirmToggle'] ?? 'Please confirm you would like to toggle this Member on/off'
    );

    return sprintf(
        '<a href="%s" title="%s" onclick="if(!confirm(\'%s\')) return false; Backend.getScrollOffset(); return AjaxRequest.toggleField(this, true)" data-icon="%s" data-icon-disabled="%s" data-states="1,0"%s>%s</a> ',
        Backend::addToUrl($href . '&amp;id=' . $row['id']),
        StringUtil::specialchars($title),
        $confirmMsg,
        $activeIcon,
        $inactiveIcon,
        $attributes,  // preserves any other attributes Contao passes in
        Image::getHtml($currentIcon, $label, 'data-state="' . ($isActive ? 1 : 0) . '"')
    );
};

/* Remove the 'show' action so users can see the full details */
//$GLOBALS['TL_DCA']['tl_member']['list']['operations']['show'] = false;

 /* Extend the tl_user palettes */
foreach ($GLOBALS['TL_DCA']['tl_member']['palettes'] as $k => $v) {
    $GLOBALS['TL_DCA']['tl_member']['palettes'][$k] = str_replace('groups;', 'groups;{price_tier_legend}, price_tier, price_tier_display;{admin_review_legend}, last_reviewed; {last_review_and_submit_legend}, last_review_and_submit; {pwf_hidden_assignments_legend}, pwf_hidden_assignments;', $v);
}

/* Add fields to tl_user */
$GLOBALS['TL_DCA']['tl_member']['fields']['price_tier'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['price_tier'],
    'inputType'               => 'select',
    'flag'                    => DataContainer::SORT_ASC,
    'eval'                    => array('mandatory'=>true, 'multiple'=>false, 'tl_class'=>'', 'submitOnChange'=>false),
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

$GLOBALS['TL_DCA']['tl_member']['fields']['last_reviewed'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['psych_work_form_colors'],
    'inputType'               => 'text',
    'default'                 => '',
    'filter'                  => true,
    'search'                  => true,
    'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
    'sql'                     => "varchar(20) NOT NULL default ''",
    'default'                => date('m/d/y'),
);

$GLOBALS['TL_DCA']['tl_member']['fields']['last_review_and_submit'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['last_review_and_submit'],
    'inputType'               => 'text',
    'default'                 => '',
    'filter'                  => true,
    'search'                  => true,
    'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
    'sql'                     => "varchar(20) NOT NULL default ''",
    'default'                => date('m/d/y'),
);

/* MEMBER GROUP SELECTION */
$GLOBALS['TL_DCA']['tl_member']['fields']['pwf_hidden_assignments'] = array(
    'label'            => &$GLOBALS['TL_LANG']['tl_member_group']['pwf_hidden_assignments'],
    'inputType'        => 'checkboxWizard',
    'eval'             => array('multiple'=> true, 'mandatory'=>false, 'tl_class'=>'long'),
    'flag'             => DataContainer::SORT_ASC,
    'options_callback' => array('Bcs\Backend\MemberBackend', 'getHiddenAssignments'),
    'sql'              => "blob NULL"
);
