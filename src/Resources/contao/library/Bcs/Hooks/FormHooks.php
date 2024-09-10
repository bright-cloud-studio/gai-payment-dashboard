<?php

namespace Bcs\Hooks;
use DateTime;

use Bcs\Model\Assignment;
use Bcs\Model\District;
use Bcs\Model\PriceTier;
use Bcs\Model\Psychologist;
use Bcs\Model\Service;
use Bcs\Model\School;
use Bcs\Model\Student;
use Bcs\Model\Transaction;
use Bcs\Model\TransactionMisc;

use Contao\Controller;
use Contao\Environment;
use Contao\FrontendUser;
use Contao\Input;
use Contao\MemberModel;
use Contao\PageModel;


class FormHooks
{
    protected static $arrUserOptions = array();

    // When a form is submitted
    public function onFormSubmit($submittedData, $formData, $files, $labels, $form)
    {
        
        // Assignment Selection Form
        if($formData['formID'] == 'assignment_selection') {
            // Create transaction using submitted data
            $_SESSION['assignment_uuid'] = $submittedData['assignment_uuid'];
        }
            
        // Assignment Generate Transaction Form
        else if($formData['formID'] == 'assignment_generate_transaction') {
            // Create a new Transaction
            $transaction = new Transaction();
            
            // Apply values
            $transaction->pid = $submittedData['assignment_uuid'];
            $transaction->tstamp = time();
            
            $transaction->date_submitted = strtotime($submittedData['date_submitted']);
            $transaction->psychologist = $submittedData['psychologist'];
            $transaction->service = $submittedData['service_provided'];
            $transaction->price = $submittedData['hourly_rate'];
            $transaction->meeting_date = strtotime($submittedData['meeting_date']);
            $transaction->meeting_start = $submittedData['start_time'];
            $transaction->meeting_end = $submittedData['end_time'];
            
            $transaction->meeting_duration = $this->timeDifferenceInMinutes($submittedData['start_time'],$submittedData['end_time']);
                
            $transaction->notes = $submittedData['notes'];
            
            // Save our new Transaction
            $transaction->save();   
        }

        // Assignment Misc. Billing Form
        else if($formData['formID'] == 'assignment_misc_billing') {
            
            // Create a new Transaction
            $transaction = new TransactionMisc();
            
            // Apply values
            $transaction->pid = 0;
            $transaction->tstamp = time();
            
            $transaction->date_submitted = strtotime($submittedData['date_submitted']);

            $member = FrontendUser::getInstance();
            $transaction->psychologist = $submittedData['psychologist'];

            $service = Service::findBy('name', 'Misc. Billing');
            $transaction->service = $service->service_code;

            $transaction->service_label = $submittedData['service_label'];
            
            $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
            $transaction->notes = $submittedData['notes'];
            
            // Save our new Transaction
            $transaction->save();
            
        }
            
        // Assignment Misc. Travel Expenses Form
        else if($formData['formID'] == 'assignment_misc_travel_expenses') {

            // Create a new Transaction
            $transaction = new TransactionMisc();
            
            // Apply values
            $transaction->pid = 0;
            $transaction->tstamp = time();
            
            $transaction->date_submitted = strtotime($submittedData['date_submitted']);

            $member = FrontendUser::getInstance();
            $transaction->psychologist = $member->id;

            $service = Service::findBy('name', 'Misc. Travel Expenses');
            $transaction->service = $service->service_code;

            $transaction->service_label = $submittedData['service_label'];
            
            $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
            $transaction->notes = $submittedData['notes'];
            
            // Save our new Transaction
            $transaction->save();
            
        }

        // Assignment Parking form
        else if($formData['formID'] == 'assignment_parking') {

            // Create a new Transaction
            $transaction = new TransactionMisc();
            
            // Apply values
            $transaction->pid = 0;
            $transaction->tstamp = time();
            
            $transaction->date_submitted = strtotime($submittedData['date_submitted']);

            $member = FrontendUser::getInstance();
            $transaction->psychologist = $member->id;

            $service = Service::findBy('name', 'Parking');
            $transaction->service = $service->service_code;

            $transaction->district = $submittedData['district'];
            
            $transaction->service_label = $submittedData['service_label'];
            
            $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
            $transaction->notes = $submittedData['notes'];
            
            // Save our new Transaction
            $transaction->save();
            
        }

        // Assignment Manager form
        else if($formData['formID'] == 'assignment_manager') {

            // Create a new Transaction
            $transaction = new TransactionMisc();
            
            // Apply values
            $transaction->pid = 0;
            $transaction->tstamp = time();
            
            $transaction->date_submitted = strtotime($submittedData['date_submitted']);

            $member = FrontendUser::getInstance();
            $transaction->psychologist = $member->id;

            $service = Service::findBy('name', 'Manager');
            $transaction->service = $service->service_code;

            $transaction->service_label = $submittedData['service_label'];
            
            $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
            $transaction->notes = $submittedData['notes'];
            
            // Save our new Transaction
            $transaction->save();
            
        }

        // Assignment Editing Services form
        else if($formData['formID'] == 'assignment_editing_services') {

            // Create a new Transaction
            $transaction = new TransactionMisc();
            
            // Apply values
            $transaction->pid = 0;
            $transaction->tstamp = time();
            
            $transaction->date_submitted = strtotime($submittedData['date_submitted']);

            $member = FrontendUser::getInstance();
            $transaction->psychologist = $member->id;

            $service = Service::findBy('name', 'Editing Services');
            $transaction->service = $service->service_code;

            $transaction->service_label = "Editing Services";

            $transaction->meeting_duration = $submittedData['meeting_duration'];
            
            $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
            $transaction->notes = $submittedData['notes'];
            
            // Save our new Transaction
            $transaction->save();
            
        }

        // Assignment Test Late Cancel - First form
        else if($formData['formID'] == 'assignment_test_late_cancel_first') {

            // Create a new Transaction
            $transaction = new TransactionMisc();
            
            // Apply values
            $transaction->pid = 0;
            $transaction->tstamp = time();
            
            $transaction->date_submitted = strtotime($submittedData['date_submitted']);

            $member = FrontendUser::getInstance();
            $transaction->psychologist = $member->id;

            $service = Service::findBy('name', 'First Test Late Cancel');
            $transaction->service = $service->service_code;
            $transaction->service_label = $service->name;

            $transaction->district = $submittedData['district'];
            $transaction->school = $submittedData['school'];

            $transaction->student_initials = $submittedData['student_initials'];
            $transaction->lasid = $submittedData['lasid'];
            $transaction->sasid = $submittedData['sasid'];

            $transaction->meeting_date = strtotime($submittedData['meeting_date']);

            $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
            $transaction->notes = $submittedData['notes'];
            
            // Save our new Transaction
            $transaction->save();
            
        }

        // Assignment Test Late Cancel - First form
        else if($formData['formID'] == 'assignment_test_late_cancel_additional') {

            // Create a new Transaction
            $transaction = new TransactionMisc();
            
            // Apply values
            $transaction->pid = 0;
            $transaction->tstamp = time();
            
            $transaction->date_submitted = strtotime($submittedData['date_submitted']);

            $member = FrontendUser::getInstance();
            $transaction->psychologist = $member->id;

            $service = Service::findBy('name', 'Additional Test Late Cancel');
            $transaction->service = $service->service_code;
            $transaction->service_label = $service->name;

            $transaction->district = $submittedData['district'];
            $transaction->school = $submittedData['school'];

            $transaction->student_initials = $submittedData['student_initials'];
            $transaction->lasid = $submittedData['lasid'];
            $transaction->sasid = $submittedData['sasid'];

            $transaction->meeting_date = strtotime($submittedData['meeting_date']);

            $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
            $transaction->notes = $submittedData['notes'];
            
            // Save our new Transaction
            $transaction->save();
            
        }
        
    }

    // When a form is loaded
    public function onPrepareForm($fields, $formId, $form)
    {
        // If we are loading our "Create Transactions" form
        if(str_contains(Environment::get('request'), "create-transaction")) {
            
            // If we don't have an assignment unique id in our session
            if(!$_SESSION['assignment_uuid']) {
                
                // Find the one "root" page
                $root_page = PageModel::findOneByType('root');
                // Get the url for our root page
                $root_url = $root_page->getFrontendUrl();
                // Set code to 302, which means temporary internal redirect
                $redirect_code = 302;
                // Temporary Redirect (302) the user to the root page
                Controller::redirect($root_url, ($redirect_code ? $redirect_code : NULL));
                
            }
            
        }
        
        ///////////////////////////////
        // ASSIGNMENT SELECTION FORM //
        //////////////////////////////
        if($form->formID == 'assignment_selection') {
            
            // Loop through the fields
            foreach($fields as $field) {
                
                // If this is our assignment uuid radio field
                if($field->name == 'assignment_uuid') {
                    
                    // Convert to php array
                    $options = unserialize($field->options);
                        
                        // get all of the Assignments for this Member
                        $opt = [
                            'order' => 'date_created ASC'
                        ];

                        $member = FrontendUser::getInstance();
                         
                        // Get the Assignments using our specific criteria
                        $assignments = Assignment::findBy('psychologist', $member->id, $opt);

                        // Loop through all the collected Assignments
                        foreach($assignments as $assignment) {
                            $label = '';
                            
                            // Get the formated 'Date Created'
                            $t = strtotime($assignment->date_created);
                            
                            // Get label for District
                            $district = District::findOneBy('id', $assignment->district);

                            // Get label for School
                            $school = School::findOneBy('id', $assignment->school);
                            
                            // date_created - district - school - student
                            $student = Student::findOneBy('id', $assignment->student);
                            
                            // Get total number of Transactions this Member has for this Assignment
                            $transactions_total = Transaction::countBy(['pid = ?', 'psychologist = ?'], [$assignment->id, $member->id]);
                            
                            $label .= "<span id='transactions'>(" . $transactions_total . ")</span> ";
                            $label .= "<span id='date'>" . date('m/d/y',$t) . "</span> - ";
                            $label .= "<span id='district'>" . $district->district_name. "</span> - ";
                            $label .= "<span id='school'>" . $school->school_name . "</span> - ";
                            $label .= "<span id='student'>" . $student->name . "</span>";
                            
                            
                            // Format the assignment so it can be added to the form
                            $options[] = array (
                                'value' => $assignment->id,
                                'label' => $label,
                                'district' => $district->district_name,
                                'school' => $school->school_name,
                                'student' => $student->name
                            );
                        }

                    // Save back as a serialized array
                    $field->options = serialize($options);
                }
            }

            // Prefill in our Work Assignment information
            return $fields;
        }


        //////////////////////////////////////////
        // ASSIGNMENT GENERATE TRANSACTION FORM //
        //////////////////////////////////////////
        else if($form->formID == 'assignment_generate_transaction') {
            
            // Get the Assignment
            $assignment = Assignment::findOneBy('id', $_SESSION['assignment_uuid']);

            
            // Customize certain fields, first loop through all form fields
            foreach($fields as $field) {
                
                // Hidden Fields
                if($field->name == 'psychologist') { $field->value = $assignment->psychologist; }
                if($field->name == 'report_submitted') { $field->value = $assignment->report_submitted; }
                if($field->name == 'service_assigned') { $field->value = $assignment->type_of_testing; }
                
                $service = Service::findOneBy('service_code', $assignment->type_of_testing);
                if($field->name == 'service_type') { $field->value = $service->service_type; }
                

                // Assignment Details
                $district = District::findOneBy('id', $assignment->district );
                if($field->name == 'district') { $field->value = $assignment->district; }
                if($field->name == 'district_label') { $field->value = $district->district_name; }
                $school = School::findOneBy('id', $assignment->school );
                if($field->name == 'school') { $field->value = $assignment->school; }
                if($field->name == 'school_label') { $field->value = $school->school_name; }
    

                
                //Get Student and fill in values
                $student = Student::findOneBy('id', $assignment->student );

                if($field->name == 'student') { $field->value = $assignment->student; }
                if($field->name == 'student_label') { $field->value = $student->name; }
                if($field->name == 'student_dob') { $field->value = $student->date_of_birth; }
                if($field->name == 'student_lasid') { $field->value = $student->lasid; }
                if($field->name == 'student_sasid') { $field->value = $student->sasid; }
                
                // Transaction Details
                if($field->name == 'service_provided') { $field->value = $assignment->type_of_testing; }
                if($field->name == 'service_provided_label') { $field->value = $service->name; }
                
            }

            
            // Return our modified fields
            return $fields;
        }
        
        //////////////////////////////////
        // ASSIGNMENT MISC BILLING FORM //
        /////////////////////////////////
        if($form->formID == 'assignment_misc_billing') {
            
            // Loop through the fields
            foreach($fields as $field) {
                
                // If this is our assignment uuid radio field
                if($field->name == 'psychologist') {
                    
                    // Convert to php array
                    $options = unserialize($field->options);


                    $opt = [
                        'order' => 'firstname ASC'
                    ];
                    // Hold the psys
                    $psychologists = MemberModel::findBy('disable', '0', $opt);
            
                    // loop through each service
                    foreach($psychologists as $psy) {
        
                        $options[] = array (
                            'value' => $psy->id,
                            'label' => $psy->firstname . ' ' . $psy->lastname
                        );
                        
                    }
            

                    // Save back as a serialized array
                    $field->options = serialize($options);
                }
            }

            // Prefill in our Work Assignment information
            return $fields;
        }
        
        
        //////////////////////////////////
        // ASSIGNMENT ADD MEETING FORM //
        /////////////////////////////////
        if($form->formID == 'assignment_add_meeting' || $form->formID == 'assignment_test_late_cancel_first' || $form->formID == 'assignment_test_late_cancel_additional') {
            
            // Loop through the fields
            foreach($fields as $field) {
                
                // If this is our assignment uuid radio field
                if($field->name == 'district') {
                    
                    // Convert to php array
                    $options = unserialize($field->options);
                    
                    $opt = [
                        'order' => 'district_name ASC'
                    ];
                    // Hold the psys
                    $districts = District::findBy('published', '1', $opt);
            
                    // loop through each service
                    foreach($districts as $district) {
        
                        $options[] = array (
                            'value' => $district->id,
                            'label' => $district->district_name,
                            'mandatory' => false
                        );
                        
                    }
            

                    // Save back as a serialized array
                    $field->options = serialize($options);
                }
            }

            // Prefill in our Work Assignment information
            return $fields;
        }
        
        

        // Return our modified fields
        return $fields;
        
    }

    // Converts our H:i format into pure minutes
    public function hoursToMinutes($hours) 
    { 
        $minutes = 0; 
        if (strpos($hours, ':') !== false) 
        { 
            // Split hours and minutes. 
            list($hours, $minutes) = explode(':', $hours); 
        } 
        return $hours * 60 + $minutes; 
    } 
    
    public function timeDifferenceInMinutes($time1, $time2) {
        // Convert times to UNIX timestamps
        $timestamp1 = strtotime($time1);
        $timestamp2 = strtotime($time2);
    
        // Calculate the difference in seconds
        $differenceInSeconds = abs($timestamp2 - $timestamp1);
    
        // Convert seconds to minutes
        $differenceInMinutes = round($differenceInSeconds / 60);
    
        return $differenceInMinutes;
    }

}
