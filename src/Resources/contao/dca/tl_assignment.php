<?php

/* Assignment - Parent to Transaction */

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Input;

/* Table tl_price_chart */
$GLOBALS['TL_DCA']['tl_assignment'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => DC_Table::class,
        'ctable'                      => array('tl_transaction'),
        'enableVersioning'            => true,
        'onload_callback' => array
		(
			array('tl_assignment', 'setRootType')
		),
        'sql' => array
        (
            'keys' => array
            (
                'id' 	=> 	'primary',
                'alias' =>  'index',
                'pid'   => 'index'
            )
        )
    ),
 
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => DataContainer::MODE_TREE,
            'rootPaste'               => true,
            'showRootTrails'          => true,
            'icon'                    => 'pagemounts.svg',
            'flag'                    => 11,
            'fields'                  => array('date_created', 'district', 'school', 'psychologist'),
            'panelLayout'             => 'sort,filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('date_created', 'district', 'school', 'psychologist'),
			'format'                  => '%s | %s - %s | %s',
			'label_callback'          => array('tl_assignment', 'addIcon')
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
            'transactions' => array
            (
                'href'                => 'do=transaction',
                'icon'                => 'articles.svg'
            ),
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
				'button_callback'     => array('Bcs\Backend\AssignmentBackend', 'toggleIcon')
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
        'default'                     => '{assignment_legend}, date_created, date_30_day, date_45_day, psychologist, district, school, student_name, student_dob, student_gender, student_grade, student_lasid, student_sasid, initial_reeval, type_of_testing, testing_date, meeting_required, meeting_date, contact_info_parent, contact_info_teacher, team_chair, email, report_submitted, notes;{publish_legend},published;'
    ),
 
    // Fields
    'fields' => array
    (
        // Contao Fields
        'id' => array
        (
		    'sql'                     	=> "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
        'tstamp' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction']['date'],
            'inputType'               => 'text',
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
                array('Bcs\Backend\AssignmentBackend', 'generateAlias')
            ),
            'sql'                     => "varchar(128) COLLATE utf8mb3_bin NOT NULL default ''"

        ),


        // Transaction Fields
        'date_created' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction']['date_created'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(10) NOT NULL default ''",
            'default'                 => date("m/d/y, g:i a"),
        ),
        'date_30_day' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction']['date_30_day'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'date_45_day' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['date_45_day'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'psychologist' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['psychologist'],
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
        'student_name' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['student_name'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'student_dob' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['student_dob'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'student_gender' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['student_gender'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'student_grade' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['student_grade'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'student_lasid' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['student_lasid'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'student_sasid' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['student_sasid'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),


        
        'initial_reeval' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['initial_reeval'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'type_of_testing' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['type_of_testing'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'testing_date' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['testing_date'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'meeting_required' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['meeting_required'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'meeting_date' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['meeting_date'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'contact_info_parent' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['contact_info_parent'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'contact_info_teacher' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['contact_info_teacher'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'team_chair' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['team_chair'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'email' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['email'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'report_submitted' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['report_submitted'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'notes' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['notes'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
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






class tl_assignment extends Backend
{
	public function setRootType(DataContainer $dc)
	{
		if (Input::get('act') != 'create')
		{
			return;
		}
		if (Input::get('pid') == 0)
		{
			$GLOBALS['TL_DCA']['tl_assignment']['fields']['type']['default'] = 'root';
		}
		elseif (Input::get('mode') == 1)
		{
			$objPage = Database::getInstance()
				->prepare("SELECT * FROM " . $dc->table . " WHERE id=?")
				->limit(1)
				->execute(Input::get('pid'));

			if ($objPage->pid == 0)
			{
				$GLOBALS['TL_DCA']['tl_assignment']['fields']['type']['default'] = 'root';
			}
		}
	}

    public function addIcon($row, $label, DataContainer|null $dc=null, $imageAttribute='', $blnReturnImage=false, $blnProtected=false, $isVisibleRootTrailPage=false)
	{
		return Backend::addPageIcon($row, $label, $dc, $imageAttribute, $blnReturnImage, $blnProtected, $isVisibleRootTrailPage);
	}
}
