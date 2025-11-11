<?php

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Input;
use Contao\MemberModel;
 
/* Table tl_services */
$GLOBALS['TL_DCA']['tl_review_record'] = array
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
            // Attempt to list as collapsable view
            'mode'                    => DataContainer::MODE_SORTED,
            'rootPaste'               => false,
            'showRootTrails'          => false,
            'icon'                    => 'pagemounts.svg',
            'flag'                    => DataContainer::SORT_DESC,
            'fields'                  => array('date_year', 'date_month'),
            'panelLayout'             => 'sort,filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('date_year', 'date_month', 'psychologist'),
            'format'                  => '%s - %s - %s',
            'label_callback'          => array('tl_review_record', 'addIcon')
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
                'label'               => &$GLOBALS['TL_LANG']['tl_review_record']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_review_record']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_review_record']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),
 
    // Palettes
    'palettes' => array
    (
        'default'                     => '{review_record_legend}, date_month, date_year, psychologist, total_assignments; {transactions_legend}, transactions_total, transactions_total_reviewed, transactions_percentage_reviewed; {misc_transactions_legend}, misc_transactions_total, misc_transactions_total_reviewed, misc_transactions_percentage_reviewed;'
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

        'date_month' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_review_record']['date_month'],
            'inputType'               => 'select',
            'options'                 => array(
                'january' => 'January',
                'february' => 'February',
                'march' => 'March',
                'april' => 'April',
                'may' => 'May',
                'june' => 'June',
                'july' => 'July',
                'august' => 'August',
                'september' => 'September',
                'october' => 'October',
                'november' => 'November',
                'december' => 'December'
            ),
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true, 'includeBlankOption'=>true, 'blankOptionLabel'=>'Select Month'),
            'sql'                     => "varchar(10) NOT NULL default 'january'",
            'default'                 => 'january'
        ),
        'date_year' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_review_record']['date_year'],
            'inputType'               => 'select',
            'options'                 => array(
                '2025' => '2025',
                '2026' => '2026',
                '2027' => '2027',
                '2028' => '2028',
                '2029' => '2029',
                '2030' => '2030',
                '2031' => '2031',
                '2032' => '2032',
                '2033' => '2033',
                '2034' => '2034',
                '2035' => '2035'
            ),
            'filter'                  => true,
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true, 'includeBlankOption'=>true, 'blankOptionLabel'=>'Select Month'),
            'sql'                     => "varchar(10) NOT NULL default '2025'",
            'default'                 => '2025'
        ),

        'psychologist' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_review_record']['psychologist'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            'flag'                    => DataContainer::SORT_ASC,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true),
            'options_callback'	      => array('Bcs\Backend\TransactionMiscBackend', 'getPsychologists'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),

        'total_assignments' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_review_record']['total_assignments'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),


        
        'transactions_total' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_review_record']['transactions_total'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'transactions_total_reviewed' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_review_record']['transactions_total_reviewed'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'transactions_percentage_reviewed' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_review_record']['transactions_percentage_reviewed'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),


        
        'misc_transactions_total' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_review_record']['misc_transactions_total'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'misc_transactions_total_reviewed' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_review_record']['misc_transactions_total_reviewed'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'misc_transactions_percentage_reviewed' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_review_record']['misc_transactions_percentage_reviewed'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        )
    )
);

class tl_review_record extends Backend
{
    public function addIcon($row, $label, DataContainer|null $dc=null, $imageAttribute='', $blnReturnImage=false, $blnProtected=false, $isVisibleRootTrailPage=false)
	{
        // YEAR | MONTH | PSY | Reviewed Transactions: 100% | Reviewed Misc. Transactions: 100% //
        $label = '';

        if($row['transactions_percentage_reviewed'] == '100' && $row['misc_transactions_percentage_reviewed'] == '100') {
            $label .= '<span class="reviewed_full">';
        } else {
            $label .= '<span class="reviewed_partial">';
        }


        $label .= date("m/d/y", $row['tstamp']) . " | ";

        $psy = MemberModel::findBy('id', $row['psychologist']);
        $label .= $psy->firstname . " " . $psy->lastname . " | ";

        $label .= "Reviewed Transactions: " . $row['transactions_percentage_reviewed'] . "% | ";

        $label .= "Reviewed Misc. Transactions: " . $row['misc_transactions_percentage_reviewed'] . "% | ";

        $label .= '</span>';

		return Backend::addPageIcon($row, $label, $dc, $imageAttribute, $blnReturnImage, $blnProtected, $isVisibleRootTrailPage);
	}
}

