<?php

/* Transaction - Misc. : A Transaction without District/School/Student information */


use Bcs\Model\Service;

use Contao\MemberModel;

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;

/* Table tl_price_chart */
$GLOBALS['TL_DCA']['tl_transaction_misc'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => DC_Table::class,
        'switchToEdit'                => false,
		'enableVersioning'            => true,
		'markAsCopy'                  => 'title',
        'sql' => array
        (
            'keys' => array
            (
                'id' 	=> 	'primary',
                'alias' =>  'index',
            )
        )
    ),
 
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => DataContainer::MODE_SORTED,
            'rootPaste'               => false,
            'icon'                    => 'pagemounts.svg',
            'defaultSearchField'      => 'date_submitted',
            'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
            'fields'                  => array('date_submitted DESC'),
            'panelLayout'             => 'sort,filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('date_submitted', 'psychologist'),
            'format'                  => '%s -  %s',
            'label_callback'          => array('tl_transaction_misc', 'addIcon')
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
                'label'               => &$GLOBALS['TL_LANG']['tl_transaction_misc']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'toggle' => array
      			(
                'label'               => &$GLOBALS['TL_LANG']['tl_transaction_misc']['toggle'],
                'icon'                => 'visible.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => array('Bcs\Backend\TransactionMiscBackend', 'toggleIcon')
      			),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_transaction_misc']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        '__selector__' => ['service'],
        'default'      => '{transaction_legend},date_submitted, psychologist, service, district, school, service_label, price;{meeting_legend}, meeting_date, meeting_start, meeting_end, meeting_duration;{student_legend}, student_initials, lasid, sasid;{notes_legend},notes;{publish_legend},published;',
        // Parking = 14
        '14'           => '{transaction_legend},date_submitted ,psychologist, district, service, service_label, price;{notes_legend},notes;{publish_legend},published;',
        // Misc. Travel Expenses = 18
        '18'           => '{transaction_legend},date_submitted, psychologist, service, service_label, price;{notes_legend},notes;{publish_legend},published;',
        // Editing Serivces = 19
        '19'           => '{transaction_legend},date_submitted, psychologist, service, service_label, meeting_duration, price;{notes_legend},notes;{publish_legend},published;',
        // Manager = 20
        '20'           => '{transaction_legend},date_submitted, psychologist, service, service_label, price;{notes_legend},notes;{publish_legend},published;',
        // Test Late Cancel - First = 32
        '32'           => '{transaction_legend},date_submitted, psychologist, district, school, student_initials, lasid, sasid; {meeting_legend}, meeting_date, service, service_label, price;{notes_legend},notes;{publish_legend},published;',
        // Test Late Cancel - Additional = 33
        '33'           => '{transaction_legend},date_submitted, psychologist, district, school, student_initials, lasid, sasid; {meeting_legend}, meeting_date, service, service_label, price;{notes_legend},notes;{publish_legend},published;',
        // Misc. Billing = 99
        '99'           => '{transaction_legend},date_submitted, psychologist, service, service_label, price;{notes_legend},notes;{publish_legend},published;',
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
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['alias'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'search'                  => false,
            'filter'                  => false,
            'eval'                    => array('unique'=>true, 'rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
            'save_callback' => array
            (
                array('Bcs\Backend\TransactionBackend', 'generateAlias')
            ),
            'sql'                     => "varchar(128) COLLATE utf8mb3_bin NOT NULL default ''"

        ),
        'published' => array
        (
            'exclude'                 => true,
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['published'],
            'inputType'               => 'checkbox',
            'eval'                    => array('submitOnChange'=>false, 'doNotCopy'=>true),
            'sql'                     => "char(1) NOT NULL default ''"
        ),


        'district' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['district'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50', 'chosen'=>true, 'submitOnChange'=>true),
            'options_callback'	      => array('Bcs\Backend\TransactionMiscBackend', 'getDistricts'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'school' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['school'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50', 'chosen'=>true),
            'options_callback'	      => array('Bcs\Backend\TransactionMiscBackend', 'getSchools'),
            'sql'                     => "varchar(255) NOT NULL default ''",
            'foreignKey'              => 'tl_school.school_name'
        ),
        'student_initials' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['student'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'lasid' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['lasid'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'sasid' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['sasid'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),

        
        
        /* ******************* */
        // Transaction Fields
        /* ******************* */
        'date_submitted' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['date_submitted'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => false,
            'search'                  => false,
            'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                 => date("m/d/y")
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
        'service' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['service'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true),
            'options_callback'	      => array('Bcs\Backend\TransactionMiscBackend', 'getServices'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'service_label' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['service_label'],
            'inputType'               => 'text',
            'filter'                  => false,
            'search'                  => false,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['price'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => false,
            'search'                  => false,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        
        'meeting_date' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['meeting_date'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => false,
            'search'                  => false,
            'eval'                    => array('rgxp'=>'date', 'datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                 => date("m/d/y")
        ),
        'meeting_start' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['meeting_start'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => false,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'meeting_end' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['meeting_end'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => false,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'meeting_duration' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['meeting_duration'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => false,
            'search'                  => false,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'notes' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction_misc']['notes'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => false,
            'filter'                  => false,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr', 'allowHtml'=>false),
            'sql'                     => "text NOT NULL default ''"
        )
    )
);





class tl_transaction_misc extends Backend
{
	public function addIcon($row, $label)
	{

        // Clear out our current label
        $label = '';

        // Add our formatted date and a dash
        $label .= date('m/d/Y', $row['date_submitted']) . " - ";

        // Add the Psy's name
        $psy = MemberModel::findBy('id', $row['psychologist']);
        $label .= $psy->firstname . " " . $psy->lastname . " - ";

        $service = Service::findBy('service_code', $row['service']);
        $label .= $service->name . " - ";

        $label .= $row['service_label'];
        
        
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
