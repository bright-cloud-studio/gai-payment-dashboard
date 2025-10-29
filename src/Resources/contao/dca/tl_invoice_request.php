<?php

use Contao\Backend;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Input;
 
/* Table tl_services */
$GLOBALS['TL_DCA']['tl_invoice_request'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => DC_Table::class,
        'ctable'                      => array('tl_invoice'),
        'switchToEdit'                => false,
        'enableVersioning'            => true,
        'onsubmit_callback' => array
		(
			array('Bcs\Backend\InvoiceRequestBackend', 'createInvoiceDCAs')
		),
        'onload_callback' => array
		(
			array('tl_invoice_request', 'setRootType')
		),
        'ondelete_callback' => array
		(
			array('Bcs\Backend\InvoiceRequestBackend', 'deleteInvoiceRequest')
		),
        'sql' => array
        (
            'keys' => array
            (
                'id' 	=> 	'primary',
                'pid'   => 'index'
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
            'icon'                    => 'pagemounts.svg',
            'flag'                    => DataContainer::SORT_DESC,
            'fields'                  => array('tstamp DESC'),
            'defaultSearchField'      => 'tstamp',
            'panelLayout'             => 'sort,filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('date_start', 'date_end'),
            'format'                  => '%s - %s',
            'label_callback'          => array('tl_invoice_request', 'addIcon')
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
            'invoices' => array
            (
                'href'                => 'do=invoice',
                'icon'                => 'articles.svg'
            ),
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_invoice_request']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_invoice_request']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_invoice_request']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        'default'                     => '{invoice_request_legend}, date_start, date_end, exclude_psychologists, exclude_districts;{batch_legend},batch_url; {configuration_legend:hide}, use_all_transactions; {internal_legend:hide},created_invoice_dcas,generated_psys,generated_districts;{publish_legend},published;'
    ),
 
    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                   => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
        'tstamp' => array
        (
		    'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting' => array
        (
            'sql'                   => "int(10) unsigned NOT NULL default '0'"
        ),
        'published' => array
        (
            'exclude'                 => true,
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_request']['published'],
            'inputType'               => 'checkbox',
            'default'                 => '1',
            'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true),
            'sql'                     => "char(1) NOT NULL default '1'"
        ),



      
      
        'date_start' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_request']['date_start'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                 => "",
        ),
        'date_end' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_request']['date_end'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('datepicker'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(20) NOT NULL default ''",
            'default'                 => "",
        ),

        'exclude_psychologists' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_request']['exclude_psychologists'],
            'inputType'               => 'checkbox',
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>false, 'multiple'=>true, 'tl_class'=>'w50'),
            'options_callback'	      => array('Bcs\Backend\InvoiceRequestBackend', 'getPsychologists'),
            'sql'                     => "blob NULL"
        ),
        'exclude_districts' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_request']['exclude_districts'],
            'inputType'               => 'checkbox',
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>false, 'multiple'=>true, 'tl_class'=>'w50'),
            'options_callback'	      => array('Bcs\Backend\InvoiceRequestBackend', 'getDistricts'),
            'sql'                     => "blob NULL"
        ),

        'batch_url' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_invoice_request']['batch_url'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''",
            'default'                 => "",
        ),

        // Configuration Options
        'use_all_transactions' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_salsify_attribute']['use_all_transactions'],
            'inputType'               => 'checkbox',
            'default'				  => '',
            'eval'                    => array('multiple'=>false, 'chosen'=>true, 'tl_class'=>'w50'),
            'sql'                     => "char(1) NOT NULL default ''"
        ),

        
        'created_invoice_dcas' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['created_invoice_dcas'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'default'                 => 'no',
            'options'                  => array('yes' => 'Yes', 'no' => 'No'),
    		'eval'                     => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true, 'includeBlankOption'=>true, 'blankOptionLabel'=>'Select Yes/No'),
    		'sql'                      => "varchar(10) NOT NULL default 'no'"
        ),
        'generated_psys' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['generated_psys'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'default'                 => 'no',
            'options'                  => array('yes' => 'Yes', 'no' => 'No'),
    		'eval'                     => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true, 'includeBlankOption'=>true, 'blankOptionLabel'=>'Select Yes/No'),
    		'sql'                      => "varchar(10) NOT NULL default 'no'"
        ),
        'generated_districts' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['generated_districts'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'default'                 => 'no',
            'options'                  => array('yes' => 'Yes', 'no' => 'No'),
    		'eval'                     => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true, 'includeBlankOption'=>true, 'blankOptionLabel'=>'Select Yes/No'),
    		'sql'                      => "varchar(10) NOT NULL default 'no'"
        ),



      
        
    )
);


class tl_invoice_request extends Backend
{
	public function setRootType(DataContainer $dc)
	{
		if (Input::get('act') != 'create')
		{
			return;
		}
		if (Input::get('pid') == 0)
		{
			$GLOBALS['TL_DCA']['tl_invoice_request']['fields']['type']['default'] = 'root';
		}
		elseif (Input::get('mode') == 1)
		{
			$objPage = Database::getInstance()
				->prepare("SELECT * FROM " . $dc->table . " WHERE id=?")
				->limit(1)
				->execute(Input::get('pid'));

			if ($objPage->pid == 0)
			{
				$GLOBALS['TL_DCA']['tl_invoice_request']['fields']['type']['default'] = 'root';
			}
		}
	}

    public function addIcon($row, $label, DataContainer|null $dc=null, $imageAttribute='', $blnReturnImage=false, $blnProtected=false, $isVisibleRootTrailPage=false)
	{
        // Add link to Batch Print if it exists
        if($row['batch_url'] != '')
            $label .= " - <a href='".$row['batch_url']."' style='font-weight: 600; color:#008000;'>Download Batch Print ZIP</a>";
        
		return Backend::addPageIcon($row, $label, $dc, $imageAttribute, $blnReturnImage, $blnProtected, $isVisibleRootTrailPage);
	}
    
}
