<?php

/* Transaction - Child to Assignment */

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;

/* Table tl_price_chart */
$GLOBALS['TL_DCA']['tl_transaction'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => DC_Table::class,
        'ptable'                      => 'tl_assignment',
        'switchToEdit'                => true,
		'enableVersioning'            => true,
		'markAsCopy'                  => 'title',
        'sql' => array
        (
            'keys' => array
            (
                'id' 	=> 	'primary',
                'alias' =>  'index',
                'pid'   =>  'index'
            )
        )
    ),
 
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => DataContainer::MODE_TREE_EXTENDED,
			'panelLayout'             => 'filter;search',
			'defaultSearchField'      => 'date',
            'icon'                    => 'pagemounts.svg'
        ),
        'label' => array
        (
            'fields'                  => array('date', 'psychologist'),
			'format'                  => '%s <span class="label-info">[%s]</span>',
			'label_callback'          => array('tl_transaction', 'addIcon')
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
                'label'               => &$GLOBALS['TL_LANG']['tl_transaction']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
			
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_transaction']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_transaction']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_transaction']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('Bcs\Backend\TransactionBackend', 'toggleIcon')
			),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_transaction']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        'default'                     => '{transaction_legend},date,psychologist,district,school,student_initials,service_code,price,lasid,sasid,meeting_date,meeting_start,meeting_end,meeting_duration,notes;{publish_legend},published;'
    ),
 
    // Fields
    'fields' => array
    (
        /* ******************* */
        // Contao Fields
        /* ******************* */
        'id' => array
        (
		    'sql'                     	=> "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array
        (
		    'foreignKey'              => 'tl_assignment.id',
			'sql'                     => "int(10) unsigned NOT NULL default 0",
			'relation'                => array('type'=>'belongsTo', 'load'=>'lazy')
        ),
        'tstamp' => array
        (
		    'sql'                     	=> "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting' => array
        (
            'sql'                    	=> "int(10) unsigned NOT NULL default '0'"
        ),
        'alias' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction']['alias'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'search'                  => true,
            'eval'                    => array('unique'=>true, 'rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
            'save_callback' => array
            (
                array('Bcs\Backend\TransactionBackend', 'generateAlias')
            ),
            'sql'                     => "varchar(128) COLLATE utf8mb3_bin NOT NULL default ''"

        ),
        
        /* ******************* */
        // Transaction Fields
        /* ******************* */
        'billing_month' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction']['billing_month'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'date_submitted' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction']['date_submitted'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'psychologist' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction']['psychologist'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'district' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['district'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'school' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['school'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'student_initials' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['student_initials'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'service_code' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['service_code'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['price'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'lasid' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['lasid'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'sasid' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['sasid'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'meeting_date' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['meeting_date'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'meeting_start' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['meeting_start'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'meeting_end' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['meeting_end'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'meeting_duration' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['meeting_duration'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'notes' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['notes'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'reviewed' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['reviewed'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'deleted' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['deleted'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'misc_billing' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['misc_billing'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'assignment_id' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['assignment_id'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'published' => array
        (
            'exclude'                 => true,
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['published'],
            'inputType'               => 'checkbox',
            'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true),
            'sql'                     => "char(1) NOT NULL default ''"
        )
    )
);





class tl_transaction extends Backend
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
