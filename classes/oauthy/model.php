<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Handle OAuth data storage
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthy
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Kohana_Model
 * *
 */
class Oauthy_Model extends Kohana_Model {

    protected $_db = 'default';

	public static function factory($name, $db = NULL)
	{
		// Add the model prefix
		$class = 'Model_Oauthy_'.$name;

		return new $class($db);
	}

} // END Oauthy_Model
