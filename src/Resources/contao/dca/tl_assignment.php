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
                'label'               => &$GLOBALS['TL_LANG']['tl_assignment']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_assignment']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_assignment']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_assignment']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('Bcs\Backend\AssignmentBackend', 'toggleIcon')
			),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_assignment']['show'],
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
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['date'],
            'inputType'               => 'text',
		    'sql'                     	=> "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting' => array
        (
            'sql'                    	=> "int(10) unsigned NOT NULL default '0'"
        ),
        'alias' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['alias'],
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
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['date_created'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                 => date("m/d/y g:i A"),
        ),
        'date_30_day' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['date_30_day'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                 => date('m/d/y g:i A', strtotime("+30 days")),
        ),
        'date_45_day' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['date_45_day'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                 => date('m/d/y g:i A', strtotime("+45 days")),
        ),
        'psychologist' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['psychologist'],
            'inputType'               => 'select',
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'options_callback'	      => array('Bcs\Backend\AssignmentBackend', 'getPsychologists'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'district' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['district'],
            'inputType'               => 'select',
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'options_callback'	      => array('Bcs\Backend\AssignmentBackend', 'getDistricts'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'school' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['school'],
            'inputType'               => 'select',
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'options_callback'	      => array('Bcs\Backend\AssignmentBackend', 'getSchools'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'student_name' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['student_name'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'student_dob' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['student_dob'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
        ),
        'student_gender' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['student_gender'],
            'inputType'               => 'select',
            'default'                 => '',
            'options'                  => array('male' => 'Male', 'female' => 'Female', 'other' => 'Other'),
    		'eval'                     => array('mandatory'=>true, 'tl_class'=>'w50'),
    		'sql'                      => "varchar(32) NOT NULL default 'slider'"
        ),
        'student_grade' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['student_grade'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'student_lasid' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['student_lasid'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'student_sasid' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['student_sasid'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),


        
        'initial_reeval' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['initial_reeval'],
            'inputType'               => 'select',
            'default'                 => '',
            'options'                  => array('initial' => 'Initial', 're_eval' => 'Re-eval'),
    		'eval'                     => array('mandatory'=>true, 'tl_class'=>'w50'),
    		'sql'                      => "varchar(10) NOT NULL default 'slider'"
        ),
        'type_of_testing' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['type_of_testing'],
            'inputType'               => 'select',
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'options_callback'	      => array('Bcs\Backend\AssignmentBackend', 'getServices'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'testing_date' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['testing_date'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                 => date("m/d/y g:i A"),
        ),
        'meeting_required' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['meeting_required'],
            'inputType'               => 'select',
            'default'                 => '',
            'options'                  => array('yes' => 'Yes', 'no' => 'No'),
    		'eval'                     => array('mandatory'=>true, 'tl_class'=>'w50'),
    		'sql'                      => "varchar(10) NOT NULL default 'slider'"
        ),
        'meeting_date' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['meeting_date'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'contact_info_parent' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['contact_info_parent'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'contact_info_teacher' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['contact_info_teacher'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'team_chair' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['team_chair'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'email' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['email'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'report_submitted' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['report_submitted'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'notes' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['notes'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),


        
        'published' => array
        (
            'exclude'                 => true,
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['published'],
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
