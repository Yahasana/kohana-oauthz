<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Handle OAuth data storage
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * @see         Kohana_Model
 * *
 */
class Oauthz_Model extends Kohana_Model {

    protected $_db = 'default';

	public static function factory($name, $db = NULL)
	{
		// Add the model prefix
		$class = 'Model_Oauthz_'.$name;

		return new $class($db);
	}

} // END Oauthz_Model
