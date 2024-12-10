<?php

/**
* Bright Cloud Studio's GAI Payment Dashboard
*
* Copyright (C) 2024-2025 Bright Cloud Studio
*
* @package    bright-cloud-studio/gai-payment-dashboard
* @link       https://www.brightcloudstudio.com/
* @license    http://opensource.org/licenses/lgpl-3.0.html
**/

namespace Bcs\Module;

use Bcs\Model\District;
use Bcs\Model\School;
use Bcs\Model\Student;
use Bcs\Model\Assignment;

use Contao\BackendTemplate;
use Contao\System;
use Contao\FrontendUser;


class ModListAssignmentsFilter extends \Contao\Module
{

    /* Default Template */
    protected $strTemplate = 'mod_list_assignments_filter';
    
    protected static $filter_districts = array();
    protected static $filter_schools = array();
    protected static $filter_students = array();

    /* Construct function */
    public function __construct($objModule, $strColumn='main')
	{
        parent::__construct($objModule, $strColumn);
	}

    /* Generate function */
    public function generate()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
        {
            $objTemplate = new BackendTemplate('be_wildcard');
 
            $objTemplate->wildcard = '### ' . mb_strtoupper($GLOBALS['TL_LANG']['FMD']['assignments'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&table=tl_module&act=edit&id=' . $this->id;
 
            return $objTemplate->parse();
        }
 
        return parent::generate();
    }


    protected function compile()
    {
        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/bcspaymentdashboard/js/list_assignments_filter.js';
        //$GLOBALS['TL_CSS'][]        = 'bundles/bcspaymentdashboard/css/datatables.min.css';
        
        $member = FrontendUser::getInstance();

        // get all of the Assignments for this Member
        $opt = [
            'order' => 'date_created ASC'
        ];
                        
        // Get Transactions that have our selected Assignment as the parent and that belong to this Psychologist
        $assignments = Assignment::findBy('psychologist', $member->id, $opt);
        
        foreach($assignments as $assignment) {
            
            // Get the District name
            $district = District::findOneBy('id', $assignment->district);
            $filter_districts[] = $district->district_name;
            
            // Get the School name
            $school = School::findOneBy('id', $assignment->school);
            $filter_schools[] = $school->school_name;
            
            // Get the Student name
            $student = Student::findOneBy('id', $assignment->student);
            $filter_students[] = $student->name;
        }
        
        // Sort arrays alphabetically
        asort($filter_districts);
        asort($filter_schools);
        asort($filter_students);

        // Add sorted arrays to our template
        $this->Template->filter_districts = $filter_districts;
        $this->Template->filter_schools = $filter_schools;
        $this->Template->filter_students = $filter_students;
        
    }
  

}
