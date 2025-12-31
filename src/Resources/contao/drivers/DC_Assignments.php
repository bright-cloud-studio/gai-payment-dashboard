<?php

/*
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009 - 2019 terminal42 gmbh & Isotope eCommerce Workgroup
 *
 * @link       https://isotopeecommerce.org
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */

use Contao\ArrayUtil;
use Contao\Backend;
use Contao\BackendTemplate;
use Contao\BackendUser;
use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Exception\InternalServerErrorException;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\Database;
use Contao\Date;
use Contao\DC_Table;
use Contao\Encryption;
use Contao\Environment;
use Contao\FilesModel;
use Contao\Image;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;
use Contao\System;
use Contao\Versions;
use Contao\Widget;
use Doctrine\DBAL\Exception\DriverException;
use Isotope\Model\Group;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\String\UnicodeString;

class DC_ProductData extends DC_Table
{
  
    protected function filterMenu($intFilterPanel)
    {
        /** @var AttributeBagInterface $objSessionBag */
        $objSessionBag = System::getContainer()->get('session')->getBag('contao_backend');

        $fields = '';
        $sortingFields = array();
        $session = $objSessionBag->all();
        $filter = Input::get('id') ? $this->strTable . '_' . CURRENT_ID : $this->strTable;

        // Get the sorting fields
        foreach ($GLOBALS['TL_DCA'][$this->strTable]['fields'] as $k=>$v)
        {
            if (($v['filter'] ?? null) == $intFilterPanel)
            {
                $sortingFields[] = $k;
            }
        }

        // Return if there are no sorting fields
        if (empty($sortingFields))
        {
            return '';
        }

        // Set filter from user input
        if (Input::post('FORM_SUBMIT') == 'tl_filters')
        {
            foreach ($sortingFields as $field)
            {
                if (Input::post($field, true) != 'tl_' . $field)
                {
                    $session['filter'][$filter][$field] = Input::post($field, true);
                }
                else
                {
                    unset($session['filter'][$filter][$field]);
                }
            }

            $objSessionBag->replace($session);
        }

        // Set filter from table configuration
        else
        {
            foreach ($sortingFields as $field)
            {
                $what = Database::quoteIdentifier($field);

                if (isset($session['filter'][$filter][$field]))
                {
                    // Sort by day
                    if (\in_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'] ?? null, array(5, 6)))
                    {
                        if (!$session['filter'][$filter][$field])
                        {
                            $this->procedure[] = $what . "=''";
                        }
                        else
                        {
                            $objDate = new Date($session['filter'][$filter][$field]);
                            $this->procedure[] = $what . ' BETWEEN ? AND ?';
                            $this->values[] = $objDate->dayBegin;
                            $this->values[] = $objDate->dayEnd;
                        }
                    }

                    // Sort by month
                    elseif (\in_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'] ?? null, array(7, 8)))
                    {
                        if (!$session['filter'][$filter][$field])
                        {
                            $this->procedure[] = $what . "=''";
                        }
                        else
                        {
                            $objDate = new Date($session['filter'][$filter][$field]);
                            $this->procedure[] = $what . ' BETWEEN ? AND ?';
                            $this->values[] = $objDate->monthBegin;
                            $this->values[] = $objDate->monthEnd;
                        }
                    }

                    // Sort by year
                    elseif (\in_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'] ?? null, array(9, 10)))
                    {
                        if (!$session['filter'][$filter][$field])
                        {
                            $this->procedure[] = $what . "=''";
                        }
                        else
                        {
                            $objDate = new Date($session['filter'][$filter][$field]);
                            $this->procedure[] = $what . ' BETWEEN ? AND ?';
                            $this->values[] = $objDate->yearBegin;
                            $this->values[] = $objDate->yearEnd;
                        }
                    }

                    // Manual filter
                    elseif ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['eval']['multiple'] ?? null)
                    {
                        // CSV lists (see #2890)
                        if (isset($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['eval']['csv']))
                        {
                            $this->procedure[] = $this->Database->findInSet('?', $field, true);
                            $this->values[] = $session['filter'][$filter][$field] ?? null;
                        }
                        else
                        {
                            $this->procedure[] = $what . ' LIKE ?';
                            $this->values[] = '%"' . $session['filter'][$filter][$field] . '"%';
                        }
                    }

                    // Other sort algorithm
                    else
                    {
                        $this->procedure[] = $what . '=?';
                        $this->values[] = $session['filter'][$filter][$field] ?? null;
                    }
                }
            }
        }

        // Add sorting options
        foreach ($sortingFields as $cnt=>$field)
        {
            $arrValues = array();
            $arrProcedure = array();

            if (($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['mode'] ?? null) == 4)
            {
                $arrProcedure[] = 'pid=?';
                $arrValues[] = CURRENT_ID;
            }

            if (!empty($this->root) && \is_array($this->root))
            {
                $arrProcedure[] = "id IN(" . implode(',', array_map('\intval', $this->root)) . ")";
            }

            // Check for a static filter (see #4719)
            if (\is_array($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['filter'] ?? null))
            {
                foreach ($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['filter'] as $fltr)
                {
                    if (\is_string($fltr))
                    {
                        $arrProcedure[] = $fltr;
                    }
                    else
                    {
                        $arrProcedure[] = $fltr[0];
                        $arrValues[] = $fltr[1];
                    }
                }
            }

            // Support empty ptable fields
            if ($GLOBALS['TL_DCA'][$this->strTable]['config']['dynamicPtable'] ?? null)
            {
                $arrProcedure[] = ($this->ptable == 'tl_article') ? "(ptable=? OR ptable='')" : "ptable=?";
                $arrValues[] = $this->ptable;
            }

            $what = Database::quoteIdentifier($field);

            // Optimize the SQL query (see #8485)
            if (isset($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag']))
            {
                // Sort by day
                if (\in_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'], array(5, 6)))
                {
                    $what = "IF($what!='', FLOOR(UNIX_TIMESTAMP(FROM_UNIXTIME($what , '%%Y-%%m-%%d'))), '') AS $what";
                }

                // Sort by month
                elseif (\in_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'], array(7, 8)))
                {
                    $what = "IF($what!='', FLOOR(UNIX_TIMESTAMP(FROM_UNIXTIME($what , '%%Y-%%m-01'))), '') AS $what";
                }

                // Sort by year
                elseif (\in_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'], array(9, 10)))
                {
                    $what = "IF($what!='', FLOOR(UNIX_TIMESTAMP(FROM_UNIXTIME($what , '%%Y-01-01'))), '') AS $what";
                }
            }

            $table = ($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['mode'] == 6) ? $this->ptable : $this->strTable;

            // Limit the options if there are root records
            if (isset($GLOBALS['TL_DCA'][$table]['list']['sorting']['root']) && $GLOBALS['TL_DCA'][$table]['list']['sorting']['root'] !== false)
            {
                $rootIds = array_map('\intval', $GLOBALS['TL_DCA'][$table]['list']['sorting']['root']);

                // Also add the child records of the table (see #1811)
                if (($GLOBALS['TL_DCA'][$table]['list']['sorting']['mode'] ?? null) == 5)
                {
                    $rootIds = array_merge($rootIds, $this->Database->getChildRecords($rootIds, $table));
                }

                if (($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['mode'] ?? null) == 6)
                {
                    $arrProcedure[] = "pid IN(" . implode(',', $rootIds) . ")";
                }
                else
                {
                    $arrProcedure[] = "id IN(" . implode(',', $rootIds) . ")";
                }
            }

            $objFields = $this->Database->prepare("SELECT DISTINCT " . $what . " FROM " . $this->strTable . ((\is_array($arrProcedure) && isset($arrProcedure[0])) ? ' WHERE ' . implode(' AND ', $arrProcedure) : ''))
                                        ->execute($arrValues);

            // Begin select menu
            $fields .= '
<select name="' . $field . '" id="' . $field . '" class="tl_select tl_chosen' . (isset($session['filter'][$filter][$field]) ? ' active' : '') . '">
  <option value="tl_' . $field . '">' . (\is_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['label'] ?? null) ? $GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['label'][0] : ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['label'] ?? null)) . '</option>
  <option value="tl_' . $field . '">---</option>';

            if ($objFields->numRows)
            {
                $options = $objFields->fetchEach($field);

                // Sort by day
                if (\in_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'] ?? null, array(5, 6)))
                {
                    ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'] ?? null) == 6 ? rsort($options) : sort($options);

                    foreach ($options as $k=>$v)
                    {
                        if ($v === '')
                        {
                            $options[$v] = '-';
                        }
                        else
                        {
                            $options[$v] = Date::parse(Config::get('dateFormat'), $v);
                        }

                        unset($options[$k]);
                    }
                }

                // Sort by month
                elseif (\in_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'] ?? null, array(7, 8)))
                {
                    ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'] ?? null) == 8 ? rsort($options) : sort($options);

                    foreach ($options as $k=>$v)
                    {
                        if ($v === '')
                        {
                            $options[$v] = '-';
                        }
                        else
                        {
                            $options[$v] = date('Y-m', $v);
                            $intMonth = (date('m', $v) - 1);

                            if (isset($GLOBALS['TL_LANG']['MONTHS'][$intMonth]))
                            {
                                $options[$v] = $GLOBALS['TL_LANG']['MONTHS'][$intMonth] . ' ' . date('Y', $v);
                            }
                        }

                        unset($options[$k]);
                    }
                }

                // Sort by year
                elseif (\in_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'] ?? null, array(9, 10)))
                {
                    ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'] ?? null) == 10 ? rsort($options) : sort($options);

                    foreach ($options as $k=>$v)
                    {
                        if ($v === '')
                        {
                            $options[$v] = '-';
                        }
                        else
                        {
                            $options[$v] = date('Y', $v);
                        }

                        unset($options[$k]);
                    }
                }

                // Manual filter
                if ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['eval']['multiple'] ?? null)
                {
                    $moptions = array();

                    // TODO: find a more effective solution
                    foreach ($options as $option)
                    {
                        // CSV lists (see #2890)
                        if (isset($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['eval']['csv']))
                        {
                            $doptions = StringUtil::trimsplit($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['eval']['csv'], $option);
                        }
                        else
                        {
                            $doptions = StringUtil::deserialize($option);
                        }

                        if (\is_array($doptions))
                        {
                            $moptions = array_merge($moptions, $doptions);
                        }
                    }

                    $options = $moptions;
                }

                $options = array_unique($options);
                $options_callback = array();

                // Call the options_callback
                if (!($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['reference'] ?? null) && (\is_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['options_callback'] ?? null) || \is_callable($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['options_callback'] ?? null)))
                {
                    if (\is_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['options_callback'] ?? null))
                    {
                        $strClass = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['options_callback'][0];
                        $strMethod = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['options_callback'][1];

                        $this->import($strClass);
                        $options_callback = $this->$strClass->$strMethod($this);
                    }
                    elseif (\is_callable($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['options_callback'] ?? null))
                    {
                        $options_callback = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['options_callback']($this);
                    }

                    // Sort options according to the keys of the callback array
                    $options = array_intersect(array_keys($options_callback), $options);
                }

                $options_sorter = array();
                $blnDate = \in_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'] ?? null, array(5, 6, 7, 8, 9, 10));

                // Options
                foreach ($options as $kk=>$vv)
                {
                    $value = $blnDate ? $kk : $vv;

                    // Options callback
                    if (!empty($options_callback) && \is_array($options_callback))
                    {
                        $vv = $options_callback[$vv];
                    }

                    // Replace the ID with the foreign key
                    elseif (isset($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['foreignKey']))
                    {
                        $key = explode('.', $GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['foreignKey'], 2);

                        $objParent = $this->Database->prepare("SELECT " . Database::quoteIdentifier($key[1]) . " AS value FROM " . $key[0] . " WHERE id=?")
                                                    ->limit(1)
                                                    ->execute($vv);

                        if ($objParent->numRows)
                        {
                            $vv = $objParent->value;
                        }
                    }

                    // Replace boolean checkbox value with "yes" and "no"
                    elseif (($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['eval']['isBoolean'] ?? null) || (($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['inputType'] ?? null) == 'checkbox' && !($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['eval']['multiple'] ?? null)))
                    {
                        $vv = $vv ? $GLOBALS['TL_LANG']['MSC']['yes'] : $GLOBALS['TL_LANG']['MSC']['no'];
                    }

                    // Get the name of the parent record (see #2703)
                    elseif ($field == 'pid')
                    {
                        $this->loadDataContainer($this->ptable);
                        $showFields = $GLOBALS['TL_DCA'][$this->ptable]['list']['label']['fields'] ?? array();

                        if (!($showFields[0] ?? null))
                        {
                            $showFields[0] = 'id';
                        }

                        $objShowFields = $this->Database->prepare("SELECT " . Database::quoteIdentifier($showFields[0]) . " FROM " . $this->ptable . " WHERE id=?")
                                                        ->limit(1)
                                                        ->execute($vv);

                        if ($objShowFields->numRows)
                        {
                            $vv = $objShowFields->{$showFields[0]};
                        }
                    }

                    $option_label = '';

                    // Use reference array
                    if (isset($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['reference']))
                    {
                        $option_label = \is_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['reference'][$vv] ?? null) ? $GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['reference'][$vv][0] : ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['reference'][$vv] ?? null);
                    }

                    // Associative array
                    elseif (($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['eval']['isAssociative'] ?? null) || array_is_assoc($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['options'] ?? null))
                    {
                        $option_label = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['options'][$vv] ?? null;
                    }

                    // No empty options allowed
                    if (!$option_label)
                    {
                        if (isset($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['foreignKey']))
                    {
                        $option_label = $vv ?: '-';
                    }
                        else
                        {
                            $option_label = (string) $vv !== '' ? $vv : '-';
                        }
                    }

                    $options_sorter[$option_label . '_' . $field] = '  <option value="' . StringUtil::specialchars($value) . '"' . ((isset($session['filter'][$filter][$field]) && $value == $session['filter'][$filter][$field]) ? ' selected="selected"' : '') . '>' . StringUtil::specialchars($option_label) . '</option>';
                }

                // Sort by option values
                if (!$blnDate)
                {
                    uksort($options_sorter, static function ($a, $b)
                    {
                        $a = (new UnicodeString($a))->folded();
                        $b = (new UnicodeString($b))->folded();

                        if ($a->toString() === $b->toString())
                        {
                            return 0;
                        }

                        return strnatcmp($a->ascii()->toString(), $b->ascii()->toString());
                    });

                    if (\in_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$field]['flag'] ?? null, array(2, 4, 12)))
                    {
                        $options_sorter = array_reverse($options_sorter, true);
                    }
                }

                $fields .= "\n" . implode("\n", array_values($options_sorter));
            }

            // End select menu
            $fields .= '
</select> ';

            // Force a line-break after six elements (see #3777)
            if ((($cnt + 1) % 6) == 0)
            {
                $fields .= '<br>';
            }
        }

        return '
<div class="tl_filter tl_subpanel">
<strong>' . $GLOBALS['TL_LANG']['MSC']['filter'] . ':</strong> ' . $fields . '
</div>';
    }


  
}
