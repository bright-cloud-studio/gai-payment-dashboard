<?php

use Contao\Config;

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace('{date_legend', '{pwf_notice_30_day_legend}, pwf_notice_30_day_subject, pwf_notice_30_day_body;{pwf_notice_report_submitted_legend}, pwf_notice_report_submitted_subject, pwf_notice_report_submitted_body;{pwf_hide_legend},pwf_hide_cutoff;{date_legend', $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);


$GLOBALS['TL_DCA']['tl_settings']['fields'] += [

    'pwf_notice_30_day_subject' => [
        'label'             => &$GLOBALS['TL_LANG']['tl_settings']['pwf_notice_30_day_subject'],
        'inputType'               => 'text',
        'default'                 => '',
        'search'                  => true,
        'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr', 'allowHtml'=>true),
        'sql'                     => "text NOT NULL default ''"
    ],
    'pwf_notice_30_day_body' => [
        'label'             => &$GLOBALS['TL_LANG']['tl_settings']['pwf_notice_30_day_body'],
        'inputType'               => 'textarea',
        'default'                 => '',
        'search'                  => false,
        'filter'                  => false,
        'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr w100', 'rte'=>'tinyMCE', 'allowHtml' => true),
        'sql'                     => "text NOT NULL default ''"
    ],

    'pwf_notice_report_submitted_subject' => [
        'label'             => &$GLOBALS['TL_LANG']['tl_settings']['pwf_notice_report_submitted_subject'],
        'inputType'               => 'text',
        'default'                 => '',
        'search'                  => true,
        'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr', 'allowHtml'=>true),
        'sql'                     => "text NOT NULL default ''"
    ],
    'pwf_notice_report_submitted_body' => [
        'label'             => &$GLOBALS['TL_LANG']['tl_settings']['pwf_notice_report_submitted_body'],
        'inputType'               => 'textarea',
        'default'                 => '',
        'search'                  => false,
        'filter'                  => false,
        'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr w100', 'rte'=>'tinyMCE', 'allowHtml' => true),
        'sql'                     => "text NOT NULL default ''"
    ],

    
    'pwf_hide_cutoff' => [
        'label'             => &$GLOBALS['TL_LANG']['tl_settings']['pwf_hide_cutoff'],
        'inputType'               => 'text',
        'default'                 => '',
        'search'                  => false,
        'filter'                  => false,
        'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
        'sql'                     => "varchar(20) NOT NULL default ''",
    ],

];
