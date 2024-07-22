<?php

namespace Bcs\Hooks;

use Bcs\Model\Assignment;
use Bcs\Model\District;
use Bcs\Model\PriceTier;
use Bcs\Model\Psychologist;
use Bcs\Model\Service;
use Bcs\Model\School;
use Bcs\Model\Student;
use Bcs\Model\Transaction;

use Contao\FrontendUser;
use Contao\Input;
use Contao\MemberModel;

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
            
            /* Insert the submitted data as a Transaction */

            // Create a new Transaction
            $transaction = new Transaction();
            
            // Apply values
            $transaction->pid = $submittedData['assignment_uuid'];
            $transaction->psyhologist = $submittedData['assignment_uuid'];
            $transaction->tstamp = time();
            $transaction->date_submitted = time();
            // Save our new Transaction
            $transaction->save();
            
        }
    }

    // When a form is loaded
    public function onPrepareForm($fields, $formId, $form)
    {
        
        // Get the Front end user
        $member = FrontendUser::getInstance();
        
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
                            'order' => 'id ASC'
                        ];

                        // Get the Assignments using our specific criteria
                        $assignments = Assignment::findBy('psychologist', $member->id, $opt);

                        // Loop through all the collected Assignments
                        foreach($assignments as $assignment) {
                            
                            // Get the formated 'Date Created'
                            $t = strtotime($assignment->date_created);
                            
                            // Get label for District
                            $district = District::findOneBy('id', $assignment->district);

                            // Get label for School
                            $school = School::findOneBy('pid', $assignment->school);
                            
                            
                            // date_created - district - school - student
                            $student = Student::findOneBy('id', $assignment->student);
                            $label = date('m/d/y',$t) . " - " . $district->district_name . " - " . $school->school_name . " - " . $student->name;
                            
                            
                            // Format the assignment so it can be added to the form
                            $options[] = array (
                                'value' => $assignment->id,
                                'label' => $label
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
                if($field->name == 'service_price') {
                    
                    // Get the price for this service, for this psychologist's tier
                    $field->value = "123456";
                }
                
                // Assignment Details
                if($field->name == 'district') { $field->value = $assignment->district; }
                if($field->name == 'school') { $field->value = $assignment->school; }
    
                
                
                
                
                
                
                //Get Student and fill in values
                $student = Student::findOneBy('id', $assignment->student );

                if($field->name == 'student') { $field->value = $assignment->student; }
                if($field->name == 'student_dob') { $field->value = $student->date_of_birth; }
                if($field->name == 'student_lasid') { $field->value = $student->lasid; }
                if($field->name == 'student_sasid') { $field->value = $student->sasid; }
                
                // Transaction Details
                if($field->name == 'service_provided') { $field->value = $assignment->type_of_testing; }
                
                if($field->name == 'hourly_rate') {
                    
                    // get all of the price tiers that are assigned to this service
                    $prices = PriceTier::findBy('pid', $assignment->type_of_testing);
                    // loop through those prices, find one that is in our assigned tiers
                    foreach($prices as $price) {
                        if(in_array($price->id, $member->price_tier_assignments)) {
                            //$field->value = $price->tier_price;
                        }
                    }
                    
                }
                
            }

            
            // Return our modified fields
            return $fields;
        }

        // Return our modified fields
        return $fields;
        
    }

}
