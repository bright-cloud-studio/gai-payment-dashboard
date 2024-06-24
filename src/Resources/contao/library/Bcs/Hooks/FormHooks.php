<?php

namespace Bcs\Hooks;

class FormHooks
{
    protected static $arrUserOptions = array();
 
    public function onFormSubmit($submittedData, $formData, $files, $labels, $form)
    {
        if($formData['formID'] == 'work_assignment') {
            echo "HOOK HIT1!";
            die();
        }
    }

    public function onPrepareForm($fields, $formId, $form)
    {
        if($formData['formID'] == 'work_assignment') {
            echo "HOOK HIT2!";
            die();
        }
    }

}
