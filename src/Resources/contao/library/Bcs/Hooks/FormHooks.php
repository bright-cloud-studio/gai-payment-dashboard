<?php

namespace Bcs\Hooks;

use Bcs\Model\Assignment;

use Contao\FrontendUser;
use Contao\Input;

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
                        foreach($assignments as $assignmnet) {
                            
                            // Format the assignment so it can be added to the form
                            $options[] = array (
                                'value' => $assignmnet->id,
                                'label' => $assignmnet->district . " - " . $assignmnet->student_name . " - " . $assignmnet->type_of_testing
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
            return $fields;
        }

        
        return $fields;
        
    }

}
