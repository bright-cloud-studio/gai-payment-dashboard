<?php

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Input;
 
/* Table tl_services */
$GLOBALS['TL_DCA']['tl_service'] = array
(
 
    // Config
    'config' => array
    (
        'dataContainer'               => DC_Table::class,
        'switchToEdit'                => false,
        'onload_callback' => array
		(
			array('tl_service', 'setRootType')
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
            'mode'                    => DataContainer::MODE_SORTED,
            'rootPaste'               => false,
            'showRootTrails'          => false,
            'icon'                    => 'pagemounts.svg',
            'flag'                    => DataContainer::SORT_INITIAL_LETTERS_ASC,
            'fields'                  => array('service_code'),
            'panelLayout'             => 'sort,filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('service_code', 'name'),
			'format'                  => '%s - %s',
			'label_callback'          => array('tl_service', 'addIcon')
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
                'label'               => &$GLOBALS['TL_LANG']['tl_service']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_district']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('Bcs\Backend\DistrictBackend', 'toggleIcon')
			),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_district']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        'default'                     => '{services_legend}, service_code, name, service_type, description;{psy_tiers_legend}, tier_1_price, tier_2_price, tier_3_price, tier_4_price, tier_5_price, tier_6_price, tier_7_price, tier_8_price, tier_9_price, tier_10_price;{school_tiers_legend}, school_tier_1_price, school_tier_2_price, school_tier_3_price;{publish_legend}, published;'
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
			'sql'                   => "int(10) unsigned NOT NULL default 0"
		),
        'tstamp' => array
        (
            'sql'                   => "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting' => array
        (
            'sql'                   => "int(10) unsigned NOT NULL default '0'"
        ),
        'alias' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_service']['alias'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'search'                  => true,
            'eval'                    => array('unique'=>true, 'rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
            'save_callback' => array
            (
                array('Bcs\Backend\ServiceBackend', 'generateAlias')
            ),
            'sql'                     => "varchar(128) COLLATE utf8mb3_bin NOT NULL default ''"

        ),
        'service_code' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_service']['service_code'],
            'inputType'               => 'text',
            'default'                 => '0',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(5) unsigned NOT NULL default '0'"
        ),
        'name' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_service']['name'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'service_type' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['service_type'],
            'inputType'               => 'select',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'options'                  => array('fixed_price' => 'Fixed Price', 'time_based' => 'Time Based', 'manual_price' => 'Manual Price Entry'),
    		'eval'                     => array('mandatory'=>true, 'tl_class'=>'w50', 'includeBlankOption'=>true, 'blankOptionLabel'=>'Select a Service Type'),
    		'sql'                      => "varchar(15) NOT NULL default ''"
        ),
        'description' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_service']['description'],
            'inputType'               => 'text',
            'default'                 => '',
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr', 'rte'=>'tinyMCE'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),



        'tier_1_price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_price_tier']['tier_1_price'],
            'inputType'               => 'text',
            'default'                 => '0.00',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "decimal(6,2) NOT NULL default '0.00'"
        ),
        'tier_2_price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_price_tier']['tier_2_price'],
            'inputType'               => 'text',
            'default'                 => '0.00',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "decimal(6,2) NOT NULL default '0.00'"
        ),
        'tier_3_price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_price_tier']['tier_3_price'],
            'inputType'               => 'text',
            'default'                 => '0.00',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "decimal(6,2) NOT NULL default '0.00'"
        ),
        'tier_4_price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_price_tier']['tier_4_price'],
            'inputType'               => 'text',
            'default'                 => '0.00',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "decimal(6,2) NOT NULL default '0.00'"
        ),
        'tier_5_price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_price_tier']['tier_5_price'],
            'inputType'               => 'text',
            'default'                 => '0.00',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "decimal(6,2) NOT NULL default '0.00'"
        ),
        'tier_6_price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_price_tier']['tier_6_price'],
            'inputType'               => 'text',
            'default'                 => '0.00',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "decimal(6,2) NOT NULL default '0.00'"
        ),
        'tier_7_price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_price_tier']['tier_7_price'],
            'inputType'               => 'text',
            'default'                 => '0.00',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "decimal(6,2) NOT NULL default '0.00'"
        ),
        'tier_8_price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_price_tier']['tier_8_price'],
            'inputType'               => 'text',
            'default'                 => '0.00',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "decimal(6,2) NOT NULL default '0.00'"
        ),
        'tier_9_price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_price_tier']['tier_9_price'],
            'inputType'               => 'text',
            'default'                 => '0.00',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "decimal(6,2) NOT NULL default '0.00'"
        ),
        'tier_10_price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_price_tier']['tier_10_price'],
            'inputType'               => 'text',
            'default'                 => '0.00',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "decimal(6,2) NOT NULL default '0.00'"
        ),


        'school_tier_1_price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_price_tier']['school_tier_1_price'],
            'inputType'               => 'text',
            'default'                 => '0.00',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "decimal(6,2) NOT NULL default '0.00'"
        ),
        'school_tier_2_price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_price_tier']['school_tier_2_price'],
            'inputType'               => 'text',
            'default'                 => '0.00',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "decimal(6,2) NOT NULL default '0.00'"
        ),
        'school_tier_3_price' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_price_tier']['school_tier_3_price'],
            'inputType'               => 'text',
            'default'                 => '0.00',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "decimal(6,2) NOT NULL default '0.00'"
        ),


        'published' => array
        (
            'exclude'                 => true,
            'label'                   => &$GLOBALS['TL_LANG']['tl_assignment']['published'],
            'inputType'               => 'checkbox',
            'eval'                    => array('submitOnChange'=>false, 'doNotCopy'=>true),
            'sql'                     => "char(1) NOT NULL default ''"
        )
    )
);



class tl_service extends Backend
{
	public function setRootType(DataContainer $dc)
	{
		if (Input::get('act') != 'create')
		{
			return;
		}
		if (Input::get('pid') == 0)
		{
			$GLOBALS['TL_DCA']['tl_service']['fields']['type']['default'] = 'root';
		}
		elseif (Input::get('mode') == 1)
		{
			$objPage = Database::getInstance()
				->prepare("SELECT * FROM " . $dc->table . " WHERE id=?")
				->limit(1)
				->execute(Input::get('pid'));

			if ($objPage->pid == 0)
			{
				$GLOBALS['TL_DCA']['tl_district']['fields']['type']['default'] = 'root';
			}
		}
	}

    public function addIcon($row, $label, DataContainer|null $dc=null, $imageAttribute='', $blnReturnImage=false, $blnProtected=false, $isVisibleRootTrailPage=false)
	{
		return Backend::addPageIcon($row, $label, $dc, $imageAttribute, $blnReturnImage, $blnProtected, $isVisibleRootTrailPage);
	}
}




