<?php

use Contao\Backend;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Image;
use Contao\System;
use Contao\StringUtil;
 
/* Table tl_services */
$GLOBALS['TL_DCA']['tl_invoice_district'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => DC_Table::class,
        'ptable'                      => 'tl_invoice_request',
        'switchToEdit'                => false,
		    'enableVersioning'            => true,
        'sql' => array
        (
            'keys' => array
            (
                'id'            => 	'primary',
                'pid'           =>  'index',
                'invoice_url'   =>  'index'
            )
        )
    ),
 
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => DataContainer::MODE_SORTABLE,
            'fields'                  => array('pid DESC'),
            'flag'                    => DataContainer::SORT_DESC,
            'panelLayout'             => 'sort,filter;search,limit',
            'rootPaste'               => false,
            'icon'                    => 'pagemounts.svg',
            'defaultSearchField'      => 'district'
        ),
        'label' => array
        (
            'fields'                  => array('district'),
            'format'                  => '%s',
            'label_callback'          => array('tl_invoice_district', 'addIcon')
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
                'label'               => &$GLOBALS['TL_LANG']['tl_invoice_district']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
			
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_invoice_district']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_invoice_district']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_invoice_district']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        'default'                     => '{invoice_legend}, district, invoice_url; {internal_legend:hide}, district_name, transaction_ids, misc_transaction_ids, invoice_html;{publish_legend},published;'
    ),
 
    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                   => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array
        (
            'foreignKey'              => 'tl_invoice_request.CONCAT("Invoice Request ", id, ": ", date_start," - ", date_end)',
            'sql'                     => "int(10) unsigned NOT NULL default 0",
            'relation'                => array('type'=>'belongsTo', 'load'=>'lazy')
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
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_district']['published'],
            'inputType'               => 'checkbox',
            'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true),
            'sql'                     => "char(1) NOT NULL default ''"
        ),



      
      
        'district' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_district']['district'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => false,
            'foreignKey'              => 'tl_district.district_name',
            'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true, 'includeBlankOption'=>true, 'blankOptionLabel'=>'Select a District'),
            'options_callback'	      => array('Bcs\Backend\AssignmentBackend', 'getDistricts'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'district_name' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_district']['district_name'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => false,
            'filter'                  => false,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "text NULL"
        ),
        'invoice_url' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_district']['invoice_url'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => false,
            'filter'                  => false,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "text NULL"
        ),
        'transaction_ids' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_district']['transaction_ids'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => false,
            'filter'                  => false,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "text NULL"
        ),
        'misc_transaction_ids' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_district']['misc_transaction_ids'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => false,
            'filter'                  => false,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "text NULL"
        ),
        'invoice_html' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_district']['invoice_html'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => false,
            'filter'                  => false,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr', 'rte'=>'tinyMCE'),
            'sql'                     => "text NULL"
        ),



      
        
    )
);




class tl_invoice_district extends Backend
{
	public function addIcon($row, $label)
	{      
		$sub = 0;
		$unpublished = ($row['start'] && $row['start'] > time()) || ($row['stop'] && $row['stop'] <= time());

		if ($unpublished || !$row['published'])
		{
			++$sub;
		}

		if ($row['protected'])
		{
			$sub += 2;
		}

		$image = 'articles.svg';

		if ($sub > 0)
		{
			$image = 'articles_' . $sub . '.svg';
		}

		$attributes = sprintf(
			'data-icon="%s" data-icon-disabled="%s"',
			$row['protected'] ? 'articles_2.svg' : 'articles.svg',
			$row['protected'] ? 'articles_3.svg' : 'articles_1.svg',
		);

		$href = System::getContainer()->get('router')->generate('contao_backend_preview', array('page'=>$row['pid'], 'article'=>($row['alias'] ?: $row['id'])));

		return '<a href="' . StringUtil::specialcharsUrl($href) . '" title="' . StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['view']) . '" target="_blank">' . Image::getHtml($image, '', $attributes) . '</a> ' . $label;
	}
}
