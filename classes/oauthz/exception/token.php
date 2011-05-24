<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth exception for access token request flow
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * *
 */
class Oauthz_Exception_Token extends Oauthz_Exception {

	/**
	 * Magic object-to-string method.
	 *
	 *     echo $exception;
	 *
	 * @return  string
	 */
	public function __toString()
	{
        $desc = parent::$errors[parent::$type]['token_errors'][$this->error];

        $params['error'] = $this->error;

		// Set the error uri from config settings if it's not set. e.g. redirect_uri mismatch
        $params['error_uri'] = empty($this->error_uri) ? $desc['error_uri'] : $this->error_uri;

        $params['error_description'] = empty($this->error_description) ? $desc['error_description'] : $this->error_description;

		return json_encode($params);
	}

} // END Oauthz_Exception_Token
