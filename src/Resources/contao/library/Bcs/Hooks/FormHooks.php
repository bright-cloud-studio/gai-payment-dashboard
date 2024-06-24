<?php

namespace Bcs\Hooks;

class Handler
{
    protected static $arrUserOptions = array();
 
    public function onProcessForm($submittedData, $formData, $files, $labels, $form)
    {
        if($formData['formID'] == 'work_assignment') {
            echo "HOOK HIT!";
            die();
        }
    }

}
