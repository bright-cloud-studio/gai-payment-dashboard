<?php

namespace Bcs\Hooks;

class FormHooks
{
    protected static $arrUserOptions = array();

    // When a form is submitted
    public function onFormSubmit($submittedData, $formData, $files, $labels, $form)
    {
        // If this is our specific form
        if($formData['formID'] == 'work_assignment') {

            // Create transaction using submitted data
        }
    }

    // When a form is loaded
    public function onPrepareForm($fields, $formId, $form)
    {
        // If this is our specific form
        if($form->formID == 'work_assignment') {

            // Prefill in our Work Assignment information
            return $fields;
        }

        return $fields;
        
    }

}
