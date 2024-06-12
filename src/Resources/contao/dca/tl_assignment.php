<?php

/* Assignment - Parent to Transaction */

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;

use Contao\Input;

/* Table tl_price_chart */
$GLOBALS['TL_DCA']['tl_assignment'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => DC_Table::class,
        'ctable'                      => array('tl_transaction'),
        'enableVersioning'            => true,
        'onload_callback' => array
		(
			array('tl_assignment', 'setRootType')
		),
        'sql' => array
        (
            'keys' => array
            (
                'id' 	=> 	'primary',
                'alias' =>  'index',
                'pid'   => 'index'
            )
        )
    ),
 
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => DataContainer::MODE_TREE,
            'rootPaste'               => true,
            'showRootTrails'          => true,
            //'icon'                    => 'bundles/bcspaymentdashboard/icons/assignment.svg',
            'icon'                    => 'article.svg'.
            'flag'                    => 11,
            'fields'                  => array('date', 'psychologist'),
            'panelLayout'             => 'sort,filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('id'),
			'format'                  => '%s',
			'label_callback'          => array('tl_assignment', 'addIcon')
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
            'transactions' => array
            (
                'href'                => 'do=transaction',
                'icon'                => 'articles.svg'
            ),
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_transaction']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_transaction']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_transaction']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_transaction']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('Bcs\Backend\AssignmentBackend', 'toggleIcon')
			),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_transaction']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        'default'                     => '{transaction_legend},date,psychologist,district,school,student_name,service_provided,price,lasid,sasid,meeting_date,meeting_start,meeting_end,meeting_duration,notes;{internal_legend},reviewed,deleted,misc_billing,sheet_row;{publish_legend},published;'
    ),
 
    // Fields
    'fields' => array
    (
        // Contao Fields
        'id' => array
        (
		    'sql'                     	=> "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
        'tstamp' => array
        (
		    'sql'                     	=> "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting' => array
        (
            'sql'                    	=> "int(10) unsigned NOT NULL default '0'"
        ),
        'alias' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction']['alias'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'search'                  => true,
            'eval'                    => array('unique'=>true, 'rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
            'save_callback' => array
            (
                array('Bcs\Backend\TransactionBackend', 'generateAlias')
            ),
            'sql'                     => "varchar(128) COLLATE utf8mb3_bin NOT NULL default ''"

        ),


        // Transaction Fields
        'date' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction']['date'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'psychologist' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transaction']['psychologist'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'district' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['district'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'school' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['school'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'student_name' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['student_name'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'service_provided' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['service_provided'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['price'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'lasid' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['lasid'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'sasid' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['sasid'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'meeting_date' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['meeting_date'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'meeting_start' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['meeting_start'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'meeting_end' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['meeting_end'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'meeting_duration' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['meeting_duration'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'notes' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['notes'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'reviewed' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['reviewed'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'deleted' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['deleted'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'misc_billing' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['misc_billing'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'sheet_row' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['sheet_row'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'label' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['label'],
            'inputType'               => 'text',
            'default'                 => '',
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'published' => array
        (
            'exclude'                 => true,
            'label'                   => &$GLOBALS['TL_LANG']['tl_transactions']['published'],
            'inputType'               => 'checkbox',
            'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true),
            'sql'                     => "char(1) NOT NULL default ''"
        )
    )
);






class tl_assignment extends Backend
{

	public function setRootType(DataContainer $dc)
	{
		if (Input::get('act') != 'create')
		{
			return;
		}

		// Insert into
		if (Input::get('pid') == 0)
		{
			$GLOBALS['TL_DCA']['tl_assignment']['fields']['type']['default'] = 'root';
		}
		elseif (Input::get('mode') == 1)
		{
			$objPage = Database::getInstance()
				->prepare("SELECT * FROM " . $dc->table . " WHERE id=?")
				->limit(1)
				->execute(Input::get('pid'));

			if ($objPage->pid == 0)
			{
				$GLOBALS['TL_DCA']['tl_assignment']['fields']['type']['default'] = 'root';
			}
		}
	}


    public function addIcon($row, $label, DataContainer|null $dc=null, $imageAttribute='', $blnReturnImage=false, $blnProtected=false, $isVisibleRootTrailPage=false)
	{
		return Backend::addPageIcon($row, $label, $dc, $imageAttribute, $blnReturnImage, $blnProtected, $isVisibleRootTrailPage);
	}
    

}
