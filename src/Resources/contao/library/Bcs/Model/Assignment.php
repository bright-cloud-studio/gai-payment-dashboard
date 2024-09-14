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

    public static function findAllByShared(array $arrOptions=array())
	{
		$t = static::$strTable;

		return static::findBy(array("$t.psychologists_shared!=''"), null, $arrOptions);
	}
    
}
