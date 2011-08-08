<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Exception
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * *
 */
class Oauthz_Exception extends Exception {

	public static $type = 'default';

    public static $errors = array();

	/**
	 * REQUIRED.  A single error code
	 *
	 * @access	public
	 * @var		string	$error
	 */
    public $error;

	/**
	 * OPTIONAL.  A human-readable text providing additional information,
	 *   used to assist in the understanding and resolution of the error occurred.
	 *
	 * @access	public
	 * @var		string	$error_description
	 */
    public $error_description;

	/**
	 * OPTIONAL.  A URI identifying a human-readable web page with
     *   information about the error, used to provide the resource owner
     *   with additional information about the error.
	 *
	 * @access	public
	 * @var		string	$error_uri
	 */
    public $error_uri;

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

        $this->error = $message;

		// Pass the message to the parent
		parent::__construct($message, $code);
	}

} // END OAuth_Exception
