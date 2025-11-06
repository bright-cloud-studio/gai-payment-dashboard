<?php

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Input;
 
/* Table tl_services */
$GLOBALS['TL_DCA']['tl_issue'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => DC_Table::class,
        'switchToEdit'                => false,
        'onload_callback' => array
        (
              array('tl_issue', 'setRootType')
        ),
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
            'mode'                  => DataContainer::MODE_SORTED,
            'rootPaste'               => false,
            'showRootTrails'          => false,
            'icon'                    => 'pagemounts.svg',
            'flag'                    => DataContainer::SORT_DESC,
            'fields'                  => array('status'),
            'panelLayout'             => 'sort,filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('status', 'title'),
            'format'                  => '%s - %s',
            'label_callback'          => array('tl_issue', 'addIcon')
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
                'label'               => &$GLOBALS['TL_LANG']['tl_issue']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_issue']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_issue']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        'default'                     => '{issues_legend}, status, title, description;{publish_legend},published;'
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

        'psychologist' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['psychologist'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true),
            'options_callback'	      => array('Bcs\Backend\TransactionMiscBackend', 'getPsychologists'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),


      
        'status' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_issue']['status'],
            'inputType'               => 'select',
            'default'                 => 'outstanding',
            'filter'                  => true,
            'search'                  => true,
            'options'                  => array('prioritize' => 'Priority', 'outstanding' => 'Outstanding', 'completed' => 'Completed'),
            'eval'                     => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                      => "varchar(15) NOT NULL default ''"
        ),
        'title' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_issue']['title'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'description' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_issue']['description'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr', 'rte'=>'tinyMCE'),
            'sql'                     => "text NOT NULL default ''"
        )

      
    )
);
