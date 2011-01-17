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
class Oauth2_Exception_Authorize extends Oauth2_Exception {

    public $redirect_uri;

    public $state;

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

        $desc = parent::$errors[parent::$type]['code_errors'][$code];

        $params['error'] = $code;

        if( ! empty($desc['error_description'])) $params['error_description'] = $desc['error_description'];

        if( ! empty($desc['error_uri'])) $params['error_uri'] = $desc['error_uri'];

        if( ! empty($this->state)) $params['state'] = $this->state;

		return $this->redirect_uri.'?'.http_build_query($params);
	}

} // END Oauth_Exception_Authorize
