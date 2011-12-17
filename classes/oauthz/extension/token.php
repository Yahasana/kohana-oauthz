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
            $pattern = '/([-_a-z]*)=(?:"([^"]+)"|([^\s,]+)|\'([^\']+)\')/';
            if(preg_match_all($pattern, $_SERVER['HTTP_AUTHORIZATION'], $matches, PREG_SET_ORDER))
            {
                foreach($matches as $match)
                {
                    if($value = Oauthz::urldecode($match[2] ?: $match[3]))
                    {
                        $params[$match[1]]  = $value;
                    }
                }
            }
            // Replace the name of token to oauth_token
            if(isset($params['token']))
            {
                // Parse the "state" paramter
                if(isset($params['state']) AND $state = Oauthz::urldecode($params['state']))
                    $this->state['state'] = $state;

                $params['oauth_token'] = $params['token'];
                unset($params['token'], $args['state']);

                // Check all required parameters should NOT be empty
                foreach($args as $key => $val)
                {
                    if($val === TRUE)
                    {
                        if(empty($params[$key]))
                        {
                            throw new Oauthz_Exception_Authorize('invalid_request', $this->state);
                        }
                        else
                        {
                            $this->$key = $params[$key];
                        }
                    }
                    elseif($val !== FALSE)
                    {
                        $this->$key = $val;
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
                throw new Oauthz_Exception_Token('invalid_request', $this->state);
            }
            else
            {
                // Parse the "state" paramter
                if(isset($_POST['state']))
                {
                    if($state = Oauthz::urldecode($_POST['state']))
                        $this->state['state'] = $state;

                    unset($args['state']);
                }

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
                            throw new Oauthz_Exception_Authorize('invalid_request', $this->state);
                        }
                    }
                    elseif($val !== FALSE)
                    {
                        $this->$key = $val;
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
                throw new Oauthz_Exception_Token('invalid_request', $this->state);
            }
            else
            {
                // Parse the "state" paramter
                if(isset($_GET['state']))
                {
                    if($state = Oauthz::urldecode($_GET['state']))
                        $this->state['state'] = $state;

                    unset($args['state']);
                }

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
                            throw new Oauthz_Exception_Authorize('invalid_request', $this->state);
                        }
                    }
                    elseif($val !== FALSE)
                    {
                        $this->$key = $val;
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
        // Verify the client and the code, load the access token if successes
        if($client = Model_Oauthz::factory('Token')
            ->token($this->client_id, $this->code, $this->expires_in))
        {
            // Audit
        }
        else
        {
            // Invalid client_id
            throw new Oauthz_Exception_Token('unauthorized_client', $this->state);
        }

        if(isset($this->token_secret) AND $client['token_secret'] !== sha1($this->token_secret))
        {
            throw new Oauthz_Exception_Token('invalid_request', $this->state);
        }

        if($client['expires_in'] < $_SERVER['REQUEST_TIME'])
        {
            throw new Oauthz_Exception_Token('unauthorized_client', $this->state);
        }

        $token = new Oauthz_Token;

        $token->token_type      = $client['token_type'];
        $token->access_token    = $client['access_token'];
        $token->refresh_token   = $client['refresh_token'];
        $token->expires_in      = $client['expires_in'];

        isset($this->state) AND $token->state = $this->state['state'];

        return $this->redirect_uri.'#'.$token->as_query();
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
        if($client['access_token'] !== $this->oauth_token)
        {
            throw new Oauthz_Exception_Access('unauthorized_client', $this->state);
        }

        if($client['token_type'] !== $this->token_type)
        {
            throw new Oauthz_Exception_Access('unauthorized_client', $this->state);
        }

        if(isset($this->scope) AND ! empty($client['scope']))
        {
            if( ! in_array($this->scope, explode(' ', $client['scope'])))
                throw new Oauthz_Exception_Access('invalid_scope', $this->state);
        }

        $token = new Oauthz_Token;

        if(isset($this->format))
        {
            $token->format = $this->format;
        }

        //switch($token->token_type)
        //{

        //}

        return $token;
    }

} // END Oauthz_Extension_Token
