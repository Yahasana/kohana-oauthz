<?php
/**
 * Oauth parameter handler for authenticate token request
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthy
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauthy_Type
 * *
 */
class Oauthy_Type_Token extends Oauthy_Type {

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
     * @throw   Oauthy_Exception_Token  Error code: invalid_request
     */
    public function __construct(array $args)
    {
        $params = array();
        /**
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
                if($value = Oauthy::urldecode(isset($matches[5]) ? $matches[5][0] : $matches[4][0]))
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
                            throw new Oauthy_Exception_Token('invalid_request');
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
                throw new Oauthy_Exception_Token('invalid_request');
            }
            else
            {
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
                        if(isset($_POST[$key]) AND $value = Oauthy::urldecode($_POST[$key]))
                        {
                            $params[$key] = $value;
                        }
                        else
                        {
                            throw new Oauthy_Exception_Token('invalid_request');
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
                throw new Oauthy_Exception_Token('invalid_request');
            }
            else
            {
                // Check all required parameters should NOT be empty
                foreach($args as $key => $val)
                {
                    if($val === TRUE)
                    {
                        if(isset($_GET[$key]) AND $value = Oauthy::urldecode($_GET[$key]))
                        {
                            $params[$key] = $value;
                        }
                        else
                        {
                            throw new Oauthy_Exception_Token('invalid_request');
                        }
                    }
                }
            }

            $this->method = 'GET';
        }

        if(empty($params))
        {
            throw new Oauthy_Exception_Token('invalid_request');
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
     * @return	Oauthy_Token
     * @throw   Oauthy_Exception_Token Error codes: invalid_scope, redirect_uri_mismatch
     */
    public function oauth_token($client)
    {
        $response = new Oauthy_Token;

        if(isset($this->_params['token_secret']) AND $client['token_secret'] !== sha1($this->_params['token_secret']))
        {
            throw new Oauthy_Exception_Token('invalid_request');
        }

        //if(isset($this->_params['nonce']) AND $client['nonce'] !== $this->nonce)
        //{
        //    throw new Oauthy_Exception_Token('invalid_request');
        //}

        if(isset($this->_params['timestamp']) AND $client['timestamp'] < $this->_params['timestamp'])
        {
            throw new Oauthy_Exception_Token('unauthorized_client');
        }

        /**
         * Verify the signature
         * Note: this feature should only need when non-TLS http request
         */
        if( ! empty($this->_params['signature']) AND ! empty($this->_params['algorithm']))
        {
            $uri = URL::base(FALSE, TRUE).Request::$instance->uri;

            $string = Oauthy::normalize(Request::$method, $uri, $this->_params);

            if($this->_params['algorithm'] == 'rsa-sha1' OR $this->_params['algorithm'] == 'hmac-sha1')
            {
                $response->public_cert = $client['ssh_key'];
                $response->private_cert = $this->_params['signature'];
            }

            if ( ! Oauthy::signature($this->_params['algorithm'], $string)->check($response, $this->_params['signature']))
            {
                throw new Oauthy_Exception_Token('invalid_signature');
            }
        }

        return $response;
    }

    /**
     * MUST verify that the verification code, client identity, client secret,
     * and redirection URI are all valid and match its stored association.
     *
     * @access  public
     * @param	array	$client
     * @return  Oauthy_Token
     * @throw   Oauthy_Exception_Token  Error codes: invalid_request, unauthorized_client
     * @todo    impletement timestamp, nonce, signature checking
     */
    public function access_token($client)
    {
        $response = new Oauthy_Token;

        if(isset($this->_params['format']))
        {
            $response->format = $this->_params['format'];
        }

        //if(isset($this->_params['nonce']) AND $client['nonce'] !== $this->_params['nonce'])
        //{
        //    throw new Oauthy_Exception_Token('invalid_request');
        //}

        if($client['access_token'] !== $this->oauth_token)
        {
            throw new Oauthy_Exception_Token('unauthorized_client');
        }

        if(isset($this->_params['scope']) AND ! empty($client['scope']))
        {
            if( ! in_array($this->_params['scope'], explode(' ', $client['scope'])))
                throw new Oauthy_Exception_Token('invalid_scope');
        }

        if(isset($this->_params['timestamp']) AND $client['timestamp'] < $this->_params['timestamp'])
        {
            throw new Oauthy_Exception_Token('unauthorized_client');
        }

        // Verify the signature
        if( ! empty($this->_params['signature']) AND ! empty($this->_params['algorithm']))
        {
            $uri = URL::base(FALSE, TRUE).Request::$instance->uri;

            $string = Oauthy::normalize(Request::$method, $uri, $this->_params);

            $this->_params['algorithm'] = strtolower($this->_params['algorithm']);

            if($this->_params['algorithm'] === 'rsa-sha1' OR $this->_params['algorithm'] === 'hmac-sha1')
            {
                $response->public_cert = $client['ssh_key'];
                $response->private_cert = $this->_params['signature'];
            }

            if( ! Oauthy::signature($this->_params['algorithm'], $string)->check($response, $this->_params['signature']))
            {
                throw new Oauthy_Exception_Token('invalid_request');
            }
        }

        return $response;
    }

} // END Oauthy_Type_Token
