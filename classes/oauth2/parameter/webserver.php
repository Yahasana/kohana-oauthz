<?php
/**
 * Oauth parameter handler for webserver flow
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauth_Parameter
 * *
 */
class Oauth2_Parameter_Webserver extends Oauth2_Parameter {

    /**
     * REQUIRED.  The client identifier as described in Section 2.1.
     *
     * @access	public
     * @var		string	$client_id
     */
    public $client_id;

    /**
     * REQUIRED.  The redirection URI used in the initial request.
     *
     * @access	public
     * @var		string	$redirect_uri
     */
    public $redirect_uri;

    /**
     * Load oauth parameters from GET or POST
     *
     * @access	public
     * @param	string	$flag	default [ FALSE ]
     * @return	void
     */
    public function __construct(array $args)
    {
        $params = array();

        // Load oauth_token from form-encoded body
        isset($_SERVER['CONTENT_TYPE']) OR $_SERVER['CONTENT_TYPE'] = getenv('CONTENT_TYPE');

        // oauth_token already send in authorization header or the encrypt Content-Type is not single-part
        if(stripos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') === FALSE)
        {
            throw new Oauth2_Exception_Token('invalid_request');
        }
        else
        {
            // Check all required parameters should NOT be empty
            foreach($args as $key => $val)
            {
                if($val === TRUE)
                {
                    if(isset($_POST[$key]) AND $value = Oauth2::urldecode($_POST[$key]))
                    {
                        $params[$key] = $value;
                    }
                    else
                    {
                        throw new Oauth2_Exception_Token('invalid_request');
                    }
                }
            }
        }

        $this->code = $params['code'];

        $this->client_id = $params['client_id'];

        unset($params['code'], $params['client_id']);

        $this->_params = $params;
    }

    public function access_token($client)
    {
        $response = new Oauth2_Token;

        if($client['redirect_uri'] !== $this->_params['redirect_uri'])
        {
            throw new Oauth2_Exception_Token('redirect_uri_mismatch');
        }

        if($client['client_secret'] !== sha1($this->_params['client_secret']))
        {
            throw new Oauth2_Exception_Token('invalid_client');
        }

        $response->expires_in = 3000;
        $response->access_token = $client['access_token'];
        $response->refresh_token = $client['refresh_token'];

        return $response;
    }

} // END Oauth_Parameter_Webserver
