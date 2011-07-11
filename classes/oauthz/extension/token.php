<?php
/**
 * Response type is token
 *
 * Oauth parameter handler for authenticate token request
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * @see         Oauthz_Extension
 * *
 */
class Oauthz_Extension_Token extends Oauthz_Extension {

    /**
     * REQUIRED
     *
     * @access	public
     * @var		string	$oauth_token
     */
    public $oauth_token;

    /**
     * Parameters parsed from Form-Encoded Body
     *
     * @access	protected
     * @var		string	$_params
     */
    protected $_params;

    /**
     * Load request parameters from Authorization header, URI-Query parameters, Form-Encoded Body
     *
     * @access	public
     * @param	array	$args
     * @return	void
     * @throw   Oauthz_Exception_Token  Error code: invalid_request
     */
    public function __construct(array $args)
    {
        $params = array();
        /**
         * TODO move this request data detect into authorization handler
         * Load oauth token from authorization header
         */
        if (isset($_SERVER['HTTP_AUTHORIZATION']) OR $_SERVER['HTTP_AUTHORIZATION'] = getenv('HTTP_AUTHORIZATION'))
        {
            $offset = 0;
            $pattern = '/(([-_a-z]*)=("([^"]*)"|([^,]*)),?)/';
            while(preg_match($pattern, $_SERVER['HTTP_AUTHORIZATION'], $matches, PREG_OFFSET_CAPTURE, $offset) > 0)
            {
                $match  = $matches[0];
                $name   = $matches[2][0];
                $offset = $match[1] + strlen($match[0]);
                if($value = Oauthz::urldecode(isset($matches[5]) ? $matches[5][0] : $matches[4][0]))
                {
                    $params[$name]  = $value;
                }
            }

            // Replace the name of token to oauth_token
            if(isset($params['token']))
            {
                $params['oauth_token'] = $params['token'];
                unset($params['token']);

                // Check all required parameters should NOT be empty
                foreach($args as $key => $val)
                {
                    if($val === TRUE)
                    {
                        if( ! empty($params[$key]))
                        {
                            throw new Oauthz_Exception_Token('invalid_request');
                        }
                    }
                }
            }

            $this->method = 'HEADER';
        }

        /**
         * Load oauth_token from form-encoded body
         */
        if(isset($_POST['oauth_token']))
        {
            isset($_SERVER['CONTENT_TYPE']) OR $_SERVER['CONTENT_TYPE'] = getenv('CONTENT_TYPE');

            // oauth_token already send in authorization header or the encrypt Content-Type is not single-part
            if(isset($params['oauth_token']) OR stripos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') === FALSE)
            {
                throw new Oauthz_Exception_Token('invalid_request');
            }
            else
            {
                // TODO move this request data detect into authorization handler
                if(isset($_SERVER['PHP_AUTH_USER']) AND isset($_SERVER['PHP_AUTH_PW']))
                {
                    $_POST += array('client_id' => $_SERVER['PHP_AUTH_USER'], 'client_secret' => $_SERVER['PHP_AUTH_PW']);
                }
                // TODO Digest HTTP authentication
                //else if( ! empty($_SERVER['PHP_AUTH_DIGEST']) AND $digest = parent::parse_digest($_SERVER['PHP_AUTH_DIGEST']))
                //{
                //    $_POST += array('client_id' => $digest['username'], 'client_secret' => $digest['']);
                //}

                // Check all required parameters should NOT be empty
                foreach($args as $key => $val)
                {
                    if($val === TRUE)
                    {
                        if(isset($_POST[$key]) AND $value = Oauthz::urldecode($_POST[$key]))
                        {
                            $params[$key] = $value;
                        }
                        else
                        {
                            throw new Oauthz_Exception_Token('invalid_request');
                        }
                    }
                }
            }

            $this->method = 'POST';
        }

        /**
         * Load oauth_token from uri-query component
         */
        if(isset($_GET['oauth_token']))
        {
            // oauth_token already send in authorization header or form-encoded body
            if(isset($params['oauth_token']))
            {
                throw new Oauthz_Exception_Token('invalid_request');
            }
            else
            {
                // Check all required parameters should NOT be empty
                foreach($args as $key => $val)
                {
                    if($val === TRUE)
                    {
                        if(isset($_GET[$key]) AND $value = Oauthz::urldecode($_GET[$key]))
                        {
                            $params[$key] = $value;
                        }
                        else
                        {
                            throw new Oauthz_Exception_Token('invalid_request');
                        }
                    }
                }
            }

            $this->method = 'GET';
        }

        if(empty($params))
        {
            throw new Oauthz_Exception_Token('invalid_request');
        }

        $this->oauth_token = $params['oauth_token'];

        unset($params['oauth_token']);

        $this->_params = $params;
    }

    /**
     * No need to authorization any more
     *
     * @access	public
     * @param	array	$client
     * @return	Oauthz_Token
     * @throw   Oauthz_Exception_Token Error codes: invalid_scope, redirect_uri_mismatch
     */
    public function execute()
    {
        // Verify the client and the code, load the access token if successes
        if($client = Oauthz_Model::factory('Token')->oauth_token($this->client_id, $this->code))
        {
            $client['expires_in'] = $this->_configs['durations']['oauth_token'];
        }
        else
        {
            // Invalid client_id
            $exception = new Oauthz_Exception_Token('invalid_client');

            $exception->redirect_uri = $this->redirect_uri;

            $exception->state = $this->state;

            throw $exception;
        }

        $response = new Oauthz_Token;

        if(isset($this->_params['token_secret']) AND $client['token_secret'] !== sha1($this->_params['token_secret']))
        {
            throw new Oauthz_Exception_Token('invalid_request');
        }

        if(isset($this->_params['timestamp']) AND $client['timestamp'] < $this->_params['timestamp'])
        {
            throw new Oauthz_Exception_Token('unauthorized_client');
        }

        return $this->redirect_uri.'#'.$response->as_query();
    }

} // END Oauthz_Extension_Token
