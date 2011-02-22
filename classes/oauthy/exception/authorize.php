<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Exception
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthy
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * *
 */
class Oauthy_Exception_Authorize extends Oauthy_Exception {

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

        empty($desc['error_description']) OR $params['error_description'] = $desc['error_description'];

        empty($desc['error_uri']) OR $params['error_uri'] = $desc['error_uri'];

        empty($this->state) OR $params['state'] = $this->state;

		return $this->redirect_uri.'?'.http_build_query($params);
	}

} // END Oauthy_Exception_Authorize
