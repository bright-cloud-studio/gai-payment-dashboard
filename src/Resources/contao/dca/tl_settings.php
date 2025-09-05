<?php

use Contao\Config;

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace('{date_legend', '{pwf_notice_30_day_legend}, pwf_notice_30_day_subject, pwf_notice_30_day_body;{date_legend', $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);


$GLOBALS['TL_DCA']['tl_settings']['fields'] += [

    // Add a Select to pick the Model
    'pwf_notice_30_day_subject' => [
        'label'             => &$GLOBALS['TL_LANG']['tl_settings']['pwf_notice_30_day_subject'],
        'inputType'               => 'text',
        'default'                 => '',
        'search'                  => true,
        'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr', 'allowHtml'=>false),
        'sql'                     => "text NOT NULL default ''"
    ],

    // Add an input box for the prompt
    'pwf_notice_30_day_body' => [
        'label'             => &$GLOBALS['TL_LANG']['tl_settings']['pwf_notice_30_day_body'],
        'inputType'               => 'textarea',
        'default'                 => '',
        'search'                  => false,
        'filter'                  => false,
        'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr w100', 'rte'=>'tinyMCE', 'allowHtml' => true),
        'sql'                     => "text NOT NULL default ''"
    ],

];
