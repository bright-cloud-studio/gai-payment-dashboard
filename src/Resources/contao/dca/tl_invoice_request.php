<?php

use Contao\DataContainer;
use Contao\DC_Table;
 
/* Table tl_services */
$GLOBALS['TL_DCA']['tl_invoice_request'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => DC_Table::class,
        'enableVersioning'            => true,
        'sql' => array
        (
            'keys' => array
            (
                'id' 	=> 	'primary',
                'name' =>  'index'
            )
        )
    ),
 
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => DataContainer::MODE_SORTED,
            'fields'                  => array('service_code'),
            'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout'             => 'filter;search,limit',
            'defaultSearchField'      => 'name'
        ),
        'label' => array
        (
            'fields'                  => array('service_code', 'name'),
            'format'                  => '<span class="label-info">Service Code: [%s]</span> Name: %s'
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_invoice_request']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
			
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_invoice_request']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_invoice_request']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_invoice_request']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        'default'                     => '{services_legend},service_code,name,psychologist_tier_1,psychologist_tier_2,psychologist_tier_3,psychologist_tier_4,psychologist_tier_5,psychologist_tier_6,psychologist_tier_7,psychologist_tier_8,psychologist_tier_9,psychologist_tier_10,school_tier_1,school_tier_2,school_tier_3;{publish_legend},published;'
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
        'published' => array
        (
            'exclude'                 => true,
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['published'],
            'inputType'               => 'checkbox',
            'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true),
            'sql'                     => "char(1) NOT NULL default ''"
        )



      
      
        'date_start' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_request']['date_start'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                 => date("m/d/y"),
        ),
        'date_end' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_request']['date_end'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                 => date("m/d/y"),
        ),

        'exclude_psychologists' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_request']['exclude_psychologists'],
            'inputType'               => 'select',
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'options_callback'	      => array('Bcs\Backend\InvoiceRequestBackend', 'getPsychologists'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'exclude_districts' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_request']['exclude_districts'],
            'inputType'               => 'select',
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'options_callback'	      => array('Bcs\Backend\InvoiceRequestBackend', 'getDistricts'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),



      
        
    )
);
