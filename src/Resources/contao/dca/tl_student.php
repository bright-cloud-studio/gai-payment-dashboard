<?php

/* Table tl_rep */
$GLOBALS['TL_DCA']['tl_student'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        'sql' => array
        (
            'keys' => array
            (
                'id' 	=> 	'primary',
                'name' =>  'index'
            )
        )
    ),
 
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 1,
            'fields'                  => array('name'),
            'flag'                    => 1,
            'panelLayout'             => 'filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('name'),
            'format'                  => '%s'
        ),
        'global_operations' => array
        (
            'export' => array
            (
                'label'               => 'Export Reps CSV',
                'href'                => 'key=exportReps',
                'icon'                => 'system/modules/frasch_find_your_rep/assets/icons/file-export-icon-16.png'
            ),
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
                'label'               => &$GLOBALS['TL_LANG']['tl_student']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
			
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_student']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_student']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
            (
              'label'               => &$GLOBALS['TL_LANG']['tl_student']['toggle'],
              'icon'                => 'visible.gif',
              'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
              'button_callback'     => array('Bcs\Backend\Reps', 'toggleIcon')
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_student']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        'default'                     => '{rep_legend},rep_name,company_name,region,product_line,address,city,zip,phone_number,alt_phone_number,email,website;{state_legend},state;{publish_legend},published;'
    ),
 
    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting' => array
    		(
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
    		),
        'published' => array
    		(
    			'exclude'                 => true,
    			'label'                   => &$GLOBALS['TL_LANG']['tl_student']['published'],
    			'inputType'               => 'checkbox',
    			'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true),
    			'sql'                     => "char(1) NOT NULL default ''"
    		),


      
    		'name' => array
    		(
    			'label'                   => &$GLOBALS['TL_LANG']['tl_student']['name'],
    			'inputType'               => 'text',
    			'default'                 => '',
    			'search'                  => true,
    			'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
    			'sql'                     => "varchar(255) NOT NULL default ''"
    		),
        'date_of_birth' => array
    		(
    			'label'                   => &$GLOBALS['TL_LANG']['tl_student']['date_of_birth'],
    			'inputType'               => 'text',
    			'default'                 => '',
    			'search'                  => true,
    			'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
    			'sql'                     => "varchar(255) NOT NULL default ''"
    		),
        'gender' => array
    		(
    			'label'                   => &$GLOBALS['TL_LANG']['tl_student']['gender'],
    			'inputType'               => 'text',
    			'default'                 => '',
    			'search'                  => true,
    			'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
    			'sql'                     => "varchar(255) NOT NULL default ''"
    		),
        'grade' => array
    		(
    			'label'                   => &$GLOBALS['TL_LANG']['tl_student']['grade'],
    			'inputType'               => 'text',
    			'default'                 => '',
    			'search'                  => true,
    			'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
    			'sql'                     => "varchar(255) NOT NULL default ''"
    		),
        'lasid' => array
    		(
    			'label'                   => &$GLOBALS['TL_LANG']['tl_student']['lasid'],
    			'inputType'               => 'text',
    			'default'                 => '',
    			'search'                  => true,
    			'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
    			'sql'                     => "varchar(255) NOT NULL default ''"
    		),
        'sasid' => array
    		(
    			'label'                   => &$GLOBALS['TL_LANG']['tl_student']['sasid'],
    			'inputType'               => 'text',
    			'default'                 => '',
    			'search'                  => true,
    			'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
    			'sql'                     => "varchar(255) NOT NULL default ''"
    		)

      

    )
);
