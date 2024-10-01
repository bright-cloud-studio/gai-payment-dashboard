<?php

/* Assignment - Parent to Transaction */

use Bcs\Model\District;
use Bcs\Model\Student;

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Input;
use Contao\MemberModel;

use Contao\System;

/* Table tl_price_chart */
$GLOBALS['TL_DCA']['tl_assignment'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => DC_Table::class,
        'ctable'                      => array('tl_transaction'),
        'switchToEdit'                => false,
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
            'mode'                    => DataContainer::MODE_SORTED,
            'rootPaste'               => false,
            'showRootTrails'          => false,
            'icon'                    => 'pagemounts.svg',
            'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
            'fields'                  => array('date_created DESC'),
            'panelLayout'             => 'sort,filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('date_created', 'district', 'school', 'psychologist', 'student'),
			'format'                  => '%s %s %s %s %s',
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
        //'default'                     => '{assignment_legend}, date_created;'
        'default'                     => '{assignment_legend}, date_created, date_30_day, date_45_day, psychologist, district, school, student, initial_reeval, type_of_testing, testing_date, meeting_required, meeting_date, contact_info_parent, contact_info_teacher, team_chair, email, report_submitted;{notes_legend},notes;{shared_legend},psychologists_shared;{publish_legend},published;'
    ),
 
    // Fields
    'fields' => array
    (
        // Contao Fields
        'id' => array
        (
		    'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
        'tstamp' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['date'],
            'inputType'               => 'text',
		    'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
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
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                 => date("m/d/y"),
        ),
        'date_30_day' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['date_30_day'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'eval'                    => array('datepicker'=>true, 'mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                 => "",
        ),
        'date_45_day' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['date_45_day'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'eval'                    => array('datepicker'=>true, 'mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                 => "",
        ),
        'psychologist' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['psychologist'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50', 'chosen'=>true, 'includeBlankOption'=>true, 'blankOptionLabel'=>'Select a Psychologist'),
            'options_callback'	      => array('Bcs\Backend\AssignmentBackend', 'getPsychologists'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'psychologists_shared' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['psychologists_shared'],
            'inputType'               => 'checkbox',
            'filter'                  => true,
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('multiple'=> true, 'mandatory'=>false, 'tl_class'=>'w50'),
            'options_callback'	      => array('Bcs\Backend\AssignmentBackend', 'getPsychologistsShared'),
            'sql' => "blob NULL"
        ),
        'district' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['district'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true, 'submitOnChange'=>true),
            'options_callback'	      => array('Bcs\Backend\AssignmentBackend', 'getDistricts'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'school' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['school'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'foreignKey'              => 'tl_school.school_name',
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50', 'chosen'=>true),
            'options_callback'	      => array('Bcs\Backend\AssignmentBackend', 'getSchools'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'student' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['student'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'foreignKey'              => 'tl_student.name',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true),
            'options_callback'	      => array('Bcs\Backend\AssignmentBackend', 'getStudents'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),

        
        
        'initial_reeval' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['initial_reeval'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'default'                 => '',
    		'eval'                     => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true),
    		'options_callback'	      => array('Bcs\Backend\AssignmentBackend', 'getInitialReeval'),
            'sql'                      => "varchar(30) NOT NULL default ''"
        ),
        'type_of_testing' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['type_of_testing'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true),
            'options_callback'	      => array('Bcs\Backend\AssignmentBackend', 'getServices'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'testing_date' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['testing_date'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('datepicker'=>true, 'mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
        ),
        'meeting_required' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['meeting_required'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'default'                 => '',
            'options'                  => array('yes' => 'Yes', 'no' => 'No'),
    		'eval'                     => array('mandatory'=>false, 'tl_class'=>'w50', 'chosen'=>true, 'includeBlankOption'=>true, 'blankOptionLabel'=>'Select Yes/No'),
    		'sql'                      => "varchar(10) NOT NULL default ''"
        ),
        'meeting_date' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['meeting_date'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('datepicker'=>true, 'mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
        ),
        'contact_info_parent' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['contact_info_parent'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "text NOT NULL default ''"
        ),
        'contact_info_teacher' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['contact_info_teacher'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "text NOT NULL default ''"
        ),
        'team_chair' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['team_chair'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'email' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['email'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'report_submitted' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['report_submitted'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'default'                 => 'no',
            'options'                  => array('no' => 'No', 'yes' => 'Yes'),
    		'eval'                     => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true),
    		'sql'                      => "varchar(5) NOT NULL default ''"
        ),
        'notes' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['notes'],
            'inputType'               => 'textarea',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr w100'),
            'sql'                     => "text NOT NULL default ''"
        ),
        
        'published' => array
        (
            'exclude'                 => true,
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['published'],
            'inputType'               => 'checkbox',
            'eval'                    => array('submitOnChange'=>false, 'doNotCopy'=>true),
            'sql'                     => "char(1) NOT NULL default ''"
        )
    )
);


/*
$request = System::getContainer()->get('request_stack')->getCurrentRequest();
if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
{
    
    //$GLOBALS['TL_JAVASCRIPT'][''] = 'https://code.jquery.com/jquery-3.7.1.min.js';

    // Use jQuery from Contao's Assets, add to backend page
    //$jquery_src = \Contao\Controller::addAssetsUrlTo('assets/jquery/js/jquery.min.js');
    //array_unshift($GLOBALS['TL_JAVASCRIPT'], $jquery_src);

    
    //$GLOBALS['TL_JAVASCRIPT'][] = '/bundles/bcspaymentdashboard/js/select2.min.js';
    //$GLOBALS['TL_JAVASCRIPT'][] = '/bundles/bcspaymentdashboard/js/backend.js';
    //$GLOBALS['TL_CSS'][]        = '/bundles/bcspaymentdashboard/css/select2.min.css';
}
*/




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
        $label = '';

        // Add our formatted date and a dash
        $label .= date('m/d/Y', strtotime($row['date_created'])) . " - ";

        // Add the Psy's name
        $district = District::findOneBy('id', $row['district']);
        $label .= $district->district_name . " - ";

        $psy = MemberModel::findBy('id', $row['psychologist']);
        $label .= $psy->firstname . " " . $psy->lastname . " - ";

        
        $student = Student::findBy('id', $row['student']);
        if($student->lasid != '' && $student->sasid != '') {
            $label .= $student->lasid . " / " . $student->sasid;
        } else {
            if($student->lasid != '')
                $label .= $student->lasid;
            if($student->sasid != '')
                $label .= $student->sasid;
        }

        
		return Backend::addPageIcon($row, $label, $dc, $imageAttribute, $blnReturnImage, $blnProtected, $isVisibleRootTrailPage);
	}
    
}
