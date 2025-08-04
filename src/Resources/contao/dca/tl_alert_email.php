<?php

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Input;
 
/* Table tl_services */
$GLOBALS['TL_DCA']['tl_alert_email'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => DC_Table::class,
        'switchToEdit'                => false,
        'sql' => array
        (
            'keys' => array
            (
                'id' 	=> 	'primary'
            )
        )
    ),
 
    // List
    'list' => array
    (
        'sorting' => array
        (
            // Attempt to list as collapsable view
            'mode'                    => DataContainer::MODE_SORTED,
            'rootPaste'               => false,
            'showRootTrails'          => false,
            'icon'                    => 'pagemounts.svg',
            'flag'                    => DataContainer::SORT_DESC,
            'fields'                  => array('month'),
            'panelLayout'             => 'sort,filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('month', 'warning_date', 'final_date'),
            'format'                  => '%s - %s - %s',
            'label_callback'          => array('tl_alert_email', 'generateLabel')
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_alert_email']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'toggle' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_alert_email']['toggle'],
                'icon'                => 'visible.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => array('Bcs\Backend\AlertEmailBackend', 'toggleIcon')
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_alert_email']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_alert_email']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        'default'                     => '{alert_email_legend}, month; {warning_legend}, warning_date, warning_subject, warning_body, warning_last_sent;{final_legend}, final_date, final_subject, final_body, final_last_sent;{publish_legend},published;'
    ),
 
    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                   => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'                   => "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting' => array
        (
            'sql'                   => "int(10) unsigned NOT NULL default '0'"
        ),

        
        'month' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_alert_email']['month'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        
        

        
        'warning_date' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_alert_email']['warning_date'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => false,
            'search'                  => false,
            'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) default ''"
        ),
        'warning_subject' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_alert_email']['warning_subject'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr', 'allowHtml'=>false),
            'sql'                     => "text NOT NULL default ''"
        ),
        'warning_body' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_alert_email']['warning_body'],
            'inputType'               => 'textarea',
            'default'                 => '',
            'search'                  => false,
            'filter'                  => false,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr w100', 'rte'=>'tinyMCE', 'allowHtml' => true),
            'sql'                     => "text NOT NULL default ''"
        ),
        'warning_last_sent' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_alert_email']['warning_last_sent'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => false,
            'search'                  => false,
            'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) default ''"
        ),
        
        
        
        
        'final_date' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_alert_email']['final_date'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => false,
            'search'                  => false,
            'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) default ''"
        ),
        'final_subject' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_alert_email']['final_subject'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr', 'allowHtml'=>false),
            'sql'                     => "text NOT NULL default ''"
        ),
        'final_body' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_alert_email']['final_body'],
            'inputType'               => 'textarea',
            'default'                 => '',
            'search'                  => false,
            'filter'                  => false,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr w100', 'rte'=>'tinyMCE', 'allowHtml' => true),
            'sql'                     => "text NOT NULL default ''"
        ),
        'final_last_sent' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_alert_email']['final_last_sent'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => false,
            'search'                  => false,
            'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) default ''"
        ),

        
        
        'published' => array
        (
            'exclude'                 => true,
            'label'                   => &$GLOBALS['TL_LANG']['tl_alert_email']['published'],
            'inputType'               => 'checkbox',
            'default'                 => '1',
            'eval'                    => array('submitOnChange'=>false, 'doNotCopy'=>true),
            'sql'                     => "char(1) NOT NULL default '1'"
        )
      
    )
);


class tl_alert_email extends Backend
{
    $new_label .= $row['month'];
    $new_label .= ' | ';
    $new_label .= 'Warning: ' . date('m/d/y', $row['warning_date']);
    $new_label .= ' | ';
    $new_label .= 'Final: ' . date('m/d/y', $row['final_date']);

    
}



