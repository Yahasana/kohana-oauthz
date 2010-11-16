<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Exception
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * *
 */
class Oauth_Exception extends Exception {

	public static $type = 'default';

    public static $errors = array();

	/**
	 * Initial OAuth error codes from config settings
	 *
	 * @access	public
	 * @param	string	$message
	 * @param	string	$group	default [ 'default' ]
	 * @param	string	$code	default [ 0 ]
	 * @return	void
	 */
	public function __construct($message, $code = 0)
	{
        if( ! isset(self::$errors[self::$type]))
        {
            $config = Kohana::config('oauth-server.'.self::$type);
            self::$errors[self::$type]['code_errors'] = $config['code_errors'];
            self::$errors[self::$type]['token_errors'] = $config['token_errors'];
            self::$errors[self::$type]['access_errors'] = $config['access_errors'];
        }

		// Pass the message to the parent
		parent::__construct($message, $code);
	}

} // END OAuth_Exception
