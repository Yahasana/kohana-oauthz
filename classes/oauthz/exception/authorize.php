<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Exception
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.com
 * *
 */
class Oauthz_Exception_Authorize extends Oauthz_Exception {

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
        $desc = parent::$errors[parent::$type]['token_errors'][$this->error];

        $params['error'] = $this->error;

		// Set the error uri from config settings if it's not set. e.g. redirect_uri mismatch
        $params['error_uri'] = empty($this->error_uri) ? $desc['error_uri'] : $this->error_uri;

        $params['error_description'] = empty($this->error_description) ? $desc['error_description'] : $this->error_description;

        empty($this->state) OR $params['state'] = $this->state;

		return $this->redirect_uri.'?'.http_build_query($params);
	}

} // END Oauthz_Exception_Authorize
