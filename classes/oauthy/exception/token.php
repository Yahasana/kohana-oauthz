<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth exception for access token request flow
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthy
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * *
 */
class Oauthy_Exception_Token extends Oauthy_Exception {

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

        empty($desc['error_description']) OR $params['error_description'] = $desc['error_description'];

        empty($desc['error_uri']) OR $params['error_uri'] = $desc['error_uri'];

		return json_encode($params);
	}

} // END Oauthy_Exception_Token
