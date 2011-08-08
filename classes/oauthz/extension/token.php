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
                        if(empty($params[$key]))
                        {
                            $exception = new Oauthz_Exception_Authorize('invalid_request');

                            if(isset($this->state))
                            {
                                $exception->state = $this->state;
                            }
                            elseif (isset($params['state']))
                            {
                                $exception->state = $value;
                            }

                            throw $exception;
                        }
                        else
                        {
                            $this->$key = $params[$key];
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
                            $this->$key = $value;
                        }
                        else
                        {
                            $exception = new Oauthz_Exception_Authorize('invalid_request');

                            if(isset($this->state))
                            {
                                $exception->state = $this->state;
                            }
                            elseif (isset($_POST['state']) AND $value = Oauthz::urldecode($_POST['state']))
                            {
                                $exception->state = $value;
                            }

                            throw $exception;
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
                            $this->$key = $value;
                        }
                        else
                        {
                            $exception = new Oauthz_Exception_Authorize('invalid_request');

                            if(isset($this->state))
                            {
                                $exception->state = $this->state;
                            }
                            elseif (isset($_GET['state']) AND $value = Oauthz::urldecode($_GET['state']))
                            {
                                $exception->state = $value;
                            }

                            throw $exception;
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
        $response = new Oauthz_Token;

        // Verify the client and the code, load the access token if successes
        if($client = Model_Oauthz::factory('Token')->oauth_token($this->client_id, $this->code))
        {
            $response->token_type = $client['token_type'];
        }
        else
        {
            // Invalid client_id
            $exception = new Oauthz_Exception_Token('invalid_client');

            $exception->redirect_uri = $this->redirect_uri;

            $exception->state = $this->state;

            throw $exception;
        }

        if(isset($this->token_secret) AND $client['token_secret'] !== sha1($this->token_secret))
        {
            throw new Oauthz_Exception_Token('invalid_request');
        }

        if(isset($this->timestamp) AND $client['timestamp'] < $this->timestamp)
        {
            throw new Oauthz_Exception_Token('unauthorized_client');
        }

        $response->expires_in = $this->expires_in;

        return $this->redirect_uri.'#'.$response->as_query();
    }

    /**
     * MUST verify that the verification code, client identity, client secret,
     * and redirection URI are all valid and match its stored association.
     *
     * @access  public
     * @param	array	$client
     * @return  Oauthz_Token
     * @throw   Oauthz_Exception_Token  Error codes: invalid_request, unauthorized_client
     * @todo    impletement timestamp, nonce, signature checking
     */
    public function access_token($client)
    {
        $response = new Oauthz_Token;

        if(isset($this->format))
        {
            $response->format = $this->format;
        }

        if($client['access_token'] !== $this->oauth_token)
        {
            throw new Oauthz_Exception_Token('unauthorized_client');
        }

        if($client['token_type'] !== $this->token_type)
        {
            throw new Oauthz_Exception_Token('unauthorized_client');
        }

        if(isset($this->scope) AND ! empty($client['scope']))
        {
            if( ! in_array($this->scope, explode(' ', $client['scope'])))
                throw new Oauthz_Exception_Token('invalid_scope');
        }

        //switch($response->token_type)
        //{

        //}

        return $response;
    }

} // END Oauthz_Extension_Token
