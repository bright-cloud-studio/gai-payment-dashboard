<?php

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Input;
 
/* Table tl_services */
$GLOBALS['TL_DCA']['tl_email_record'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => DC_Table::class,
        'switchToEdit'                => false,
        'sql' => array
        (
            'keys' => array
            (
                'id' 	=> 	'primary'
            )
        )
    ),
 
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => DataContainer::MODE_UNSORTED,
            'rootPaste'               => false,
            'flag'                    => DataContainer::SORT_DESC,
            'fields'                  => array('date_created DESC'),
            'icon'                    => 'pagemounts.svg',
            'defaultSearchField'      => 'date_created',
            'panelLayout'             => 'filter;sort,search,limit'
            
        ),
        'label' => array
        (
            'fields'                  => array('email_type'),
            'format'                  => '%s',
            'label_callback'          => array('tl_email_record', 'generateLabel')
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_issue']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'toggle' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_issue']['toggle'],
                'icon'                => 'visible.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => array('Bcs\Backend\EmailRecordBackend', 'toggleIcon')
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_issue']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_issue']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        'default'                     => '{email_record_legend}, date_created, email_type, email_recipient, email_subject, email_body;'
    ),
 
    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                   => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'                   => "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting' => array
        (
            'sql'                   => "int(10) unsigned NOT NULL default '0'"
        ),

        'date_created' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_email_record']['date_created'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                => date('m/d/y g:i a')
        ),

        'email_type' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_email_record']['email_type'],
            'inputType'               => 'select',
            'default'                 => 'alert_weekly',
            'filter'                  => true,
            'search'                  => true,
            'options'                 => array(
                'alert_weekly' => 'Alert Email - Week Remaining Reminder',
                'alert_final' => 'Alert Email - Final Day Reminder',
                'pwf_30_day' => 'Psych Work Form - 30 Day',
                'pwf_report_submitted' => 'Psych Work Form - Report Submitted'
            ),
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(25) NOT NULL default ''"
        ),

        'email_recipient' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_email_record']['email_recipient'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => false,
            'foreignKey'              => 'tl_member.CONCAT(firstname," ",lastname)',
            'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true),
            'options_callback'	      => array('Bcs\Backend\EmailRecordBackend', 'getPsychologists'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),

        'email_subject' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_email_record']['email_subject'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),

        'email_body' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_email_record']['email_body'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr', 'rte'=>'tinyMCE'),
            'sql'                     => "text NOT NULL default ''"
        )

        
    )
);



class tl_email_record extends Backend
{
    public function generateLabel($row, $label, DataContainer|null $dc=null, $imageAttribute='', $blnReturnImage=false, $blnProtected=false, $isVisibleRootTrailPage=false)
    {
        $label = $row['date_created'] . " - " . $label;
        return Backend::addPageIcon($row, $label, $dc, $imageAttribute, $blnReturnImage, $blnProtected, $isVisibleRootTrailPage);
    }
}
