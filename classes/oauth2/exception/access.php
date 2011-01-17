<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth exception for oauth_token verify flow
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * *
 */
class OAuth2_Exception_Access extends Oauth2_Exception {

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

        $desc = parent::$errors[parent::$type]['access_errors'][$code];

        $params['error'] = $code;

        if( ! empty($desc['error_description'])) $params['error_description'] = $desc['error_description'];

        if( ! empty($desc['error_uri'])) $params['error_uri'] = $desc['error_uri'];

		return json_encode($params);
	}

} // END OAuth_Exception_Access
