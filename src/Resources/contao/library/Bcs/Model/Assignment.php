<?php
 
namespace Bcs\Model;

use Contao\Model;


class Assignment extends Model
{
	
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_assignment';

    public static function findAllByShared($psy, array $arrOptions=array())
	{
		$t = static::$strTable;

		return static::findBy(array("$t.psychologist!='$psy' AND $t.psychologists_shared!='' AND $t.published='1'"), null, $arrOptions);
	}
    
}
