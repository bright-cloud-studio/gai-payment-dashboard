<?php

namespace Bcs\Backend;

use Bcs\Model\District;
use Bcs\Model\Student;

use Contao\Backend;
use Contao\Image;
use Contao\Input;
use Contao\DataContainer;
use Contao\StringUtil;


class EmailRecordBackend extends Backend
{
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (strlen(Input::get('tid')))
        {
            $this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }
        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);
        if (!$row['published'])
        {
            $icon = 'invisible.gif';
        }
        return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }


    public function getAssignments(DataContainer $dc) { 

        $assignments = array();

		$this->import('Database');
		$result = $this->Database->prepare("SELECT * FROM tl_assignment WHERE published=1 ORDER BY date_created DESC")->execute();
		while($result->next())
		{
            $d = District::findBy('id', $result->district);
            $s = Student::findBy('id', $result->student);
            $lasid_sasid = '';
            if($s->lasid != '' && $s->sasid != '') {
                $lasid_sasid = $s->lasid . " / " . $s->sasid;
            } else {
                if($s->lasid != '')
                    $lasid_sasid = $s->lasid;
                if($s->sasid != '')
                    $lasid_sasid = $s->sasid;
        }
            $assignments = $assignments + array($result->id => ($result->date_created . " - " . $d->district_name . " - " . $lasid_sasid));   
		}

		return $assignments;
	}
    
    
    
    public function getPsychologists(DataContainer $dc) { 

        $psychologists = array();

		$this->import('Database');
		$result = $this->Database->prepare("SELECT * FROM tl_member WHERE disable=0 ORDER BY firstname ASC")->execute();
		while($result->next())
		{
            $psychologists = $psychologists + array($result->id => ($result->firstname . " " . $result->lastname));   
		}

		return $psychologists;
	}


}
