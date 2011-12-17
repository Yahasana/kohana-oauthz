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
	 * @param	string	$state	extras parameters
	 * @param	string	$code	default [ 0 ]
	 * @return	void
	 */
	public function __construct($message, array $state = NULL, $code = 0)
	{
        $this->error = $message;

		// Pass the message to the parent
		parent::__construct($message, $code);
	}

	public function as_json()
	{
        $params = array('error' => $this->error) + (array) $this->state;

        if(isset($params['error_uri']))
        {
            $params['error_uri'] = url::site($params['error_uri'], TRUE);
        }
        else
        {
            $params['error_uri'] = url::site($this->error_uri, TRUE);
        }

        if(isset($params['error_description']))
        {
            $params['error_description'] = __($params['error_description']);
        }
        else
        {
            $params['error_description'] = __($this->error_description);
        }

        return json_encode(array_filter($params));
	}

	public function as_query()
	{
        $params = array('error' => $this->error) + (array) $this->state;

        if(isset($params['error_uri']))
        {
            $error_uri = $params['error_uri'];

            // don't append error_uri to querystring
            unset($params['error_uri']);
        }
        else
        {
            $error_uri = $this->error_uri;
        }

        if(isset($params['error_description']))
        {
            $params['error_description'] = __($params['error_description']);
        }
        else
        {
            $params['error_description'] = __($this->error_description);
        }

        return url::site($error_uri, TRUE).'?'.http_build_query(array_filter($params), '', '&');
	}

} // END OAuth_Exception
