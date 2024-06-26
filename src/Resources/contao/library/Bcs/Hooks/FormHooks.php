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

        // If this is our specific form
        if($formData['formID'] == 'assignment_selection') {

            // Create transaction using submitted data
            $_SESSION['assignment_uuid'] = $submittedData['assignment_uuid'];
        }
    }

    // When a form is loaded
    public function onPrepareForm($fields, $formId, $form)
    {
        
        // If this is our specific form
        if($form->formID == 'assignment_selection') {
            
            // Loop through the fields
            foreach($fields as $field) {
                
                // If this is our assignment uuid radio field
                if($field->name == 'assignment_uuid') {
                    
                    // Convert to php array
                    $options = unserialize($field->options);
                    
                    
                    
                        // GET ALL ASSIGNMENTS ASSOCIATED WITH ME
                        $member = FrontendUser::getInstance();
                        
                        
                        // get all of the Assignments for this Member
                        $opt = [
                            'order' => 'id ASC'
                        ];
                        $assignments = Assignment::findBy('psychologist', $member->id, $opt);
                        
                        echo "<pre>";
                        print_r($assignments);
                        echo "</pre>";
                        die();



                    // Add our new option
                    $options[] = array (
                        'value' => '123',
                        'label' => '456'
                    );
                    
                    // Save back as a serialized array
                    $field->options = serialize($options);

                }
            }

            // Prefill in our Work Assignment information
            return $fields;
        }

        return $fields;
        
    }

}
