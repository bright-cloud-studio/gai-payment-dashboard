<?php

namespace Bcs\Hooks;

use Bcs\Model\Assignment;
use Bcs\Model\District;
use Bcs\Model\Psychologist;
use Bcs\Model\School;
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
            
            $transaction = new Transaction();
            $transaction->pid = 15;
            $transaction->psychologist =  $submittedData['psychologist'];
            $transaction->save();
            
        }
    }

    // When a form is loaded
    public function onPrepareForm($fields, $formId, $form)
    {

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

                        // Get the Front end user
                        $member = FrontendUser::getInstance();
                        
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
                            $label = date('m/d/y',$t) . " - " . $district->district_name . " - " . $school->school_name . " - " . $assignment->student_name;
                            
                            
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

            // Apply our pre-defined values to the form
            $fields['psychologist']->value = $assignment->psychologist;
            
            // Loop through the fields
            foreach($fields as $field) {
                
                // If this is the Psychologists select field
                if($field->name == 'psychologist') {
                    
                    // Stores our generated options
                    $options = [];
                    
                    // Get all active members, aka the psychologists
                    $psychologists = MemberModel::findBy('disable', '');
                    
                    // Loop through the found psychologists
                    foreach($psychologists as $psy) {
                        
                        // Add them to the select dropdown
                        $options[] = array (
                            'value' => $psy->id,
                            'label' => $psy->firstname . " " . $psy->lastname
                        );
                        
                    }
     
                }
                
                $field->options = serialize($options);
            }
            
            
            // Fill in the fields with the correct data
            
            
            return $fields;
        }

        
        return $fields;
        
    }

}
