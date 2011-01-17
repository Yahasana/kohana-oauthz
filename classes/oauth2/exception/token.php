<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth exception for access token request flow 
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * *
 */
class Oauth2_Exception_Token extends Oauth2_Exception {

	/**
	 * Magic object-to-string method.
	 *
	 *     echo $exception;
	 *
	 * @return  string
	 */
	public function __toString()
	{
        $code = $this->getMessage();

        $desc = parent::$errors[parent::$type]['token_errors'][$code];

        $params['error'] = $code;

        if( ! empty($desc['error_description'])) $params['error_description'] = $desc['error_description'];

        if( ! empty($desc['error_uri'])) $params['error_uri'] = $desc['error_uri'];

		return json_encode($params);
	}

} // END Oauth_Exception_Token
