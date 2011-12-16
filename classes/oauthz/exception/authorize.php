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
class Oauthz_Exception_Authorize extends Oauthz_Exception {

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
        $desc = parent::$errors[parent::$type]['code_errors'][$this->error];

        $params['error'] = $this->error;

        $params['error_description'] = empty($this->error_description) ? $desc['error_description'] : $this->error_description;

        empty($this->state) OR $params['state'] = $this->state;

        // Set the error uri from config settings if it's not set. e.g. redirect_uri mismatch
        $error_uri = url::site(empty($this->error_uri) ? $desc['error_uri'] : $this->error_uri, TRUE);

        return $error_uri.'?'.http_build_query($params);
	}

} // END Oauthz_Exception_Authorize
