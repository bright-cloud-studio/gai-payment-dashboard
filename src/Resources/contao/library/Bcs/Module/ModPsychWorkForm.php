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

use Bcs\Model\Assignment;
use Bcs\Model\District;
use Bcs\Model\PriceTier;
use Bcs\Model\School;
use Bcs\Model\Service;
use Bcs\Model\Student;
use Bcs\Model\Transaction;

use Contao\BackendTemplate;
use Contao\System;
use Contao\FrontendUser;


class ModPsychWorkForm extends \Contao\Module
{

    /* Default Template */
    protected $strTemplate = 'mod_psych_work_form';
    
    protected static $template_transactions = array();
    protected static $template_assignments = array();
    
    public $is_admin = false;

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
        
        $member = FrontendUser::getInstance();
        if(in_array("2", $member->groups)) {
           $is_admin = true;
        }
            
        // Include Datatables JS library and CSS stylesheets
        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/bcspaymentdashboard/js/datatables.min.js';
        $GLOBALS['TL_CSS'][]        = 'bundles/bcspaymentdashboard/css/datatables.min.css';


        // Find our colorized rows
        $db_colors = str_getcsv($member->psych_work_form_colors);
        $colors = array();
        foreach($db_colors as $color) {
            $split = explode('_', $color);
            $colors[$split[0]] = $split[1];
        }
            

        $assignments = Assignment::findBy(['psychologist = ?'], [$member->id]);
        foreach($assignments as $assignment) {
            
            // ID
            $template_assignments[$assignment->id]['id'] = $assignment->id;
            
            if($colors[$assignment->id] != '') {
                $template_assignments[$assignment->id]['color_data'] = 'data-color="'.$colors[$assignment->id].'"';
                $template_assignments[$assignment->id]['color_class'] = 'class="colorize_'.$colors[$assignment->id].'"';
            }
            
            
            // Date Created
            if($is_admin) {
                $template_assignments[$assignment->id]['date_created'] = "<input value='".date('m/d/y', $assignment->date_created)."' name='date_created_$assignment->id'class='date_created' id='date_created_$assignment->id' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['date_created'] = date('m/d/y', $assignment->date_created);
            }
            
            // Date 30 Day
            $template_assignments[$assignment->id]['data_30_day'] = 'data-30-day="'.$assignment->date_30_day.'"';
            if($is_admin) {
                $template_assignments[$assignment->id]['date_30_day'] = "<input value='$assignment->date_30_day' name='date_30_day_$assignment->id' class='date_30_day' id='date_30_day_$assignment->id' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['date_30_day'] = $assignment->date_30_day;
            }
            
            // Date 45 Day
            $template_assignments[$assignment->id]['data_45_day'] = 'data-45-day="'.$assignment->date_45_day.'"';
            if($is_admin) {
                $template_assignments[$assignment->id]['date_45_day'] = "<input value='$assignment->date_45_day' name='date_45_day_$assignment->id' class='date_45_day' id='date_45_day_$assignment->id' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['date_45_day'] = $assignment->date_45_day;
            }


            
            // Psychologist - Not Needed!
            $template_assignments[$assignment->id]['psychologist'] = $assignment->psychologist;



            // District
            if($is_admin) {
                $template_assignments[$assignment->id]['district'] .= "<select name='district_$assignment->id' class='district' id='district_$assignment->id'>";
                $template_assignments[$assignment->id]['district'] .= "<option value='' selected disabled>Select a District</option>";
                $options = [
                    'order' => 'district_name ASC'
                ];
                $districts = District::findAll($options);
                foreach($districts as $district) {
                    if($assignment->district == $district->id)
                        $template_assignments[$assignment->id]['district'] .= "<option value='" . $district->id . "' selected>$district->district_name</option>";
                    else
                        $template_assignments[$assignment->id]['district'] .= "<option value='" . $district->id . "'>$district->district_name</option>";
                }
                $template_assignments[$assignment->id]['district'] .= "</select>";
                
            } else {
                $district = District::findOneBy('id', $assignment->district);
                $template_assignments[$assignment->id]['district'] = $district->district_name;
            }
            
            
            
            // School
            if($is_admin) {
                $template_assignments[$assignment->id]['school'] .= "<select name='school_$assignment->id' class='school' id='school_$assignment->id'>";
                $template_assignments[$assignment->id]['school'] .= "<option value='' selected disabled>First, select a District</option>";
                $options = [
                    'order' => 'school_name ASC'
                ];
                $schools = School::findAll($options);
                foreach($schools as $school) {
                    if($school->pid == $assignment->district) {
                        if($assignment->school == $school->id)
                            $template_assignments[$assignment->id]['school'] .= "<option value='" . $school->id . "' selected>$school->school_name</option>";
                        else
                            $template_assignments[$assignment->id]['school'] .= "<option value='" . $school->id . "'>$school->school_name</option>";
                    }
                }
                $template_assignments[$assignment->id]['school'] .= "</select>";
                
            } else {
                $school = School::findOneBy('id', $assignment->school);
                $template_assignments[$assignment->id]['school'] = $school->school_name;
            }
            
            
            
            // Student
            if($is_admin) {
                $template_assignments[$assignment->id]['student'] .= "<select name='student_$assignment->id' class='student' id='student_$assignment->id'>";
                $template_assignments[$assignment->id]['student'] .= "<option value='' selected disabled>Select a Student</option>";
                $options = [
                    'order' => 'name ASC'
                ];
                $students = Student::findAll($options);
                foreach($students as $student) {
                    if($student->district == $assignment->district) {
                        if($assignment->student == $student->id)
                            $template_assignments[$assignment->id]['student'] .= "<option value='" . $student->id . "' selected>$student->name</option>";
                        else
                            $template_assignments[$assignment->id]['student'] .= "<option value='" . $student->id . "'>$student->name</option>";
                    }
                }
                $template_assignments[$assignment->id]['student'] .= "</select>";
                
            } else {
                $student = Student::findOneBy('id', $assignment->student );
                $template_assignments[$assignment->id]['student'] = $student->name;
            }
            
            
            // Get our Student
            $student = Student::findOneBy(['id = ?', 'district = ?'], [$assignment->student, $assignment->district]);
            $template_assignments[$assignment->id]['data_student_id'] = 'data-student-id="'.$student->id.'"';
            
            // Student - D.O.B.
            $template_assignments[$assignment->id]['data_date_of_birth'] = 'data-date-of-birth="'.$student->date_of_birth.'"';
            if($is_admin) {
                $template_assignments[$assignment->id]['date_of_birth'] = "<input value='$student->date_of_birth' name='date_of_birth_$assignment->student'class='date_of_birth' id='date_of_birth_$assignment->student' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['date_of_birth'] = $student->date_of_birth;
            }


            // Student - Grade.
            $template_assignments[$assignment->id]['data_grade'] = 'data-grade="'.$student->grade.'"';
            if($is_admin) {
                $template_assignments[$assignment->id]['grade'] = "<input value='$student->grade' name='grade_$assignment->student'class='grade' id='grade_$assignment->student' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['grade'] = $student->grade;
            }
            
            // Student - LASID.
            if($is_admin) {
                $template_assignments[$assignment->id]['lasid'] = "<input value='$student->lasid' name='lasid_$assignment->student'class='lasid' id='lasid_$assignment->student' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['lasid'] = $student->lasid;
            }
            
            // Student - SASID.
            if($is_admin) {
                $template_assignments[$assignment->id]['sasid'] = "<input value='$student->sasid' name='sasid_$assignment->student'class='sasid' id='sasid_$assignment->student' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['sasid'] = $student->sasid;
            }



            // Initial / Re-Eval
            if($is_admin) {
                
                $template_assignments[$assignment->id]['initial_reeval'] .= "<select name='initial_reeval_$assignment->id' class='initial_reeval' id='initial_reeval_$assignment->id'>";
                $template_assignments[$assignment->id]['initial_reeval'] .= "<option value='' selected disabled>Select Initial/Re-Eval</option>";
                $initial_options = array();
                $initial_options = $initial_options + array('extended' => 'Extended Eval');
                $initial_options = $initial_options + array('independent' => 'Independent Eval');
                $initial_options = $initial_options + array('initial' => 'Initial');
                $initial_options = $initial_options + array('initial_504' => 'Initial 504');
                $initial_options = $initial_options + array('re_eval' => 'Re-eval');
                $initial_options = $initial_options + array('re_eval_504' => 'Re-eval 504');
                $initial_options = $initial_options + array('other' => 'Other');
                foreach($initial_options as $value => $title) {

                    if($assignment->initial_reeval == $value)
                        $template_assignments[$assignment->id]['initial_reeval'] .= "<option value='" . $value . "' selected>$title</option>";
                    else
                        $template_assignments[$assignment->id]['initial_reeval'] .= "<option value='" . $value . "'>$title</option>";
                }
                $template_assignments[$assignment->id]['initial_reeval'] .= "</select>";
                
            } else {
                $template_assignments[$assignment->id]['initial_reeval'] = $assignment->initial_reeval;
            }


            
            // Type of Testing, aka Service
            if($is_admin) {
                $template_assignments[$assignment->id]['type_of_testing'] .= "<select name='type_of_testing_$assignment->id' class='type_of_testing' id='type_of_testing_$assignment->id'>";
                $template_assignments[$assignment->id]['type_of_testing'] .= "<option value='' selected disabled>Select Service</option>";
                $aService = array(
        			'column' 	=> array("published=?"),
        			'value'		=> 1,
        			'order' => 'name ASC'
        		);
        		$services = Service::findAll($aService);
                foreach($services as $service) {
                    if($assignment->type_of_testing == $service->id)
                        $template_assignments[$assignment->id]['type_of_testing'] .= "<option value='" . $service->id . "' selected>$service->name</option>";
                    else
                        $template_assignments[$assignment->id]['type_of_testing'] .= "<option value='" . $service->id . "'>$service->name</option>";
                }
                $template_assignments[$assignment->id]['type_of_testing'] .= "</select>";
                
            } else {
                $service = Service::findOneBy('service_code', $assignment->type_of_testing);
                $template_assignments[$assignment->id]['type_of_testing'] = $service->name;
            }
            
            
            
            // Meeting Required
            if($is_admin) {
                
                $template_assignments[$assignment->id]['meeting_required'] .= "<select name='meeting_required_$assignment->id' class='meeting_required' id='meeting_required_$assignment->id'>";
                $template_assignments[$assignment->id]['meeting_required'] .= "<option value='' disabled selected>Select Yes/No</option>";
                $merq_options = array();
                $merq_options = $merq_options + array('yes' => 'Yes');
                $merq_options = $merq_options + array('no' => 'No');
                foreach($merq_options as $value => $title) {

                    if($assignment->meeting_required == $value)
                        $template_assignments[$assignment->id]['meeting_required'] .= "<option value='" . $value . "' selected>$title</option>";
                    else
                        $template_assignments[$assignment->id]['meeting_required'] .= "<option value='" . $value . "'>$title</option>";
                }
                $template_assignments[$assignment->id]['meeting_required'] .= "</select>";
                
            } else {
                $template_assignments[$assignment->id]['meeting_required'] = $assignment->meeting_required;
            }
            
            
            
            // Testing Date
            if(1 == 1) {
                $template_assignments[$assignment->id]['testing_date'] = "<input value='$assignment->testing_date' name='testing_date_$assignment->id' class='testing_date' id='testing_date_$assignment->id' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['testing_date'] = $assignment->testing_date;
            }
            
            
            
            // Meeting Date
            if(1 == 1) {
                $template_assignments[$assignment->id]['meeting_date'] = "<input value='$assignment->meeting_date' name='meeting_date_$assignment->id' class='meeting_date' id='meeting_date_$assignment->id' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['meeting_date'] = $assignment->meeting_date;
            }
            
            
            
            // Contact Info - Parent
            $template_assignments[$assignment->id]['data_contact_parent'] = 'data-contact-parent="'.$assignment->contact_info_parent.'"';
            if(1 == 1) {
                $template_assignments[$assignment->id]['contact_info_parent'] = "<input value='$assignment->contact_info_parent' name='contact_info_parent_$assignment->id' class='contact_info_parent' id='contact_info_parent_$assignment->id' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['contact_info_parent'] = $assignment->contact_info_parent;
            }
            
            
            
            // Contact Info - Teacher
            $template_assignments[$assignment->id]['data_contact_teacher'] = 'data-contact-teacher="'.$assignment->contact_info_teacher.'"';
            if(1 == 1) {
                $template_assignments[$assignment->id]['contact_info_teacher'] = "<input value='$assignment->contact_info_teacher' name='contact_info_teacher_$assignment->id' class='contact_info_teacher' id='contact_info_teacher_$assignment->id' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['contact_info_teacher'] = $assignment->contact_info_teacher;
            }
            
            
            
            // Team Chair
            $template_assignments[$assignment->id]['data_team_chair'] = 'data-team-chair="'.$assignment->team_chair.'"';
            if(1 == 1) {
                $template_assignments[$assignment->id]['team_chair'] = "<input value='$assignment->team_chair' name='team_chair_$assignment->id' class='team_chair' id='team_chair_$assignment->id' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['team_chair'] = $assignment->team_chair;
            }
            
            
            
            // Email
            $template_assignments[$assignment->id]['data_email'] = 'data-email="'.$assignment->email.'"';
            if(1 == 1) {
                $template_assignments[$assignment->id]['email'] = "<input value='$assignment->email' name='email_$assignment->id' class='email' id='email_$assignment->id' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['email'] = $assignment->email;
            }
            
            
            
            // Report Submitted
            if($is_admin) {
                
                $template_assignments[$assignment->id]['report_submitted'] .= "<select name='report_submitted_$assignment->id' class='report_submitted' id='report_submitted_$assignment->id'>";
                $template_assignments[$assignment->id]['report_submitted'] .= "<option value='' disabled selected>Select Yes/No</option>";
                $resu_options = array();
                $resu_options = $resu_options + array('yes' => 'Yes');
                $resu_options = $resu_options + array('no' => 'No');
                foreach($resu_options as $value => $title) {

                    if($assignment->report_submitted == $value)
                        $template_assignments[$assignment->id]['report_submitted'] .= "<option value='" . $value . "' selected>$title</option>";
                    else
                        $template_assignments[$assignment->id]['report_submitted'] .= "<option value='" . $value . "'>$title</option>";
                }
                $template_assignments[$assignment->id]['report_submitted'] .= "</select>";
                
            } else {
                $template_assignments[$assignment->id]['report_submitted'] = $assignment->report_submitted;
            }
            
            
            
            // Notes
            $template_assignments[$assignment->id]['notes_data'] = 'data-notes="'.addslashes($assignment->notes).'"';
            if(1 == 1) {
                $template_assignments[$assignment->id]['notes'] = "<input value='".htmlspecialchars($assignment->notes, ENT_QUOTES) ."' name='notes_$assignment->id' class='notes' id='notes_$assignment->id' autocomplete='off'>";
            } else {
                $template_assignments[$assignment->id]['notes'] = $assignment->notes;
            }
            
        }
        
        //$this->Template->colors = $colors;
        $this->Template->assignments = $template_assignments;
        
    }
  

}
