<?php

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Input;
 
/* Table tl_services */
$GLOBALS['TL_DCA']['tl_email_record'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => DC_Table::class,
        'switchToEdit'                => false,
        'onload_callback' => array
        (
              array('tl_email_record', 'setRootType')
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
            //'mode'                    => DataContainer::MODE_TREE,
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
            'toggle' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_issue']['toggle'],
                'icon'                => 'visible.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => array('Bcs\Backend\IssueBackend', 'toggleIcon')
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
        'status' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_email_record']['status'],
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
            'label'                   => &$GLOBALS['TL_LANG']['tl_email_record']['title'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'description' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_email_record']['description'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr', 'rte'=>'tinyMCE'),
            'sql'                     => "text NOT NULL default ''"
        ),


        'published' => array
        (
            'exclude'                 => true,
            'label'                   => &$GLOBALS['TL_LANG']['tl_email_record']['published'],
            'inputType'               => 'checkbox',
            'default'                 => '1',
            'eval'                    => array('submitOnChange'=>false, 'doNotCopy'=>true),
            'sql'                     => "char(1) NOT NULL default '1'"
        )
    )
);



class tl_issue extends Backend
{
    public function addIcon($row, $label, DataContainer|null $dc=null, $imageAttribute='', $blnReturnImage=false, $blnProtected=false, $isVisibleRootTrailPage=false)
	{
            $label = "<span class='".$row['status']."'>" . $label . "</span>";
		return Backend::addPageIcon($row, $label, $dc, $imageAttribute, $blnReturnImage, $blnProtected, $isVisibleRootTrailPage);
	}
}
