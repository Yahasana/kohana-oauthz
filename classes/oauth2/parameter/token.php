<?php
/**
 * Oauth parameter handler for authenticate token request
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauth_Parameter
 * *
 */
class Oauth2_Parameter_Token extends Oauth2_Parameter {

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
     * @param	string	$args	default [ NULL ]
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

        $this->_params = $params;
    }

    /**
     * No need to authorization any more
     *
     * @access	public
     * @param	string	$client
     * @return	Oauth_Token
     */
    public function oauth_token($client)
    {
        $response = new Oauth2_Token;

        if(isset($this->_params['scope']) AND ! empty($client['scope']))
        {
            if( ! in_array($this->_params['scope'], explode(' ', $client['scope'])))
                throw new Oauth2_Exception_Token('invalid_scope');
        }

        if($client['redirect_uri'] !== $this->_params['redirect_uri'])
        {
            throw new Oauth2_Exception_Token('redirect_uri_mismatch');
        }

        // Grants Authorization
        $response->code = $client['code'];

        return $response;
    }

    /**
     * MUST verify that the verification code, client identity, client secret,
     * and redirection URI are all valid and match its stored association.
     *
     * @access  public
     * @return  Oauth_Token
     * @todo    impletement timestamp, nonce, signature checking
     */
    public function access_token($client)
    {
        $response = new Oauth2_Token;

        if(isset($this->_params['token_secret']) AND $client['token_secret'] !== sha1($this->_params['token_secret']))
        {
            throw new Oauth2_Exception_Token('invalid_request');
        }

        //if(isset($this->_params['nonce']) AND $client['nonce'] !== $this->nonce)
        //{
        //    throw new Oauth_Exception_Token('invalid_request');
        //}

        if(isset($this->_params['timestamp']) AND $client['timestamp'] < $this->_params['timestamp'])
        {
            throw new Oauth2_Exception_Token('invalid_token');
        }

        // Verify the signature
        if( ! empty($this->_params['signature']) AND ! empty($this->_params['algorithm']))
        {
            $uri = URL::base(FALSE, TRUE).Request::$instance->uri;

            $string = Oauth2::normalize(Request::$method, $uri, $this->_params);

            if($this->_params['algorithm'] == 'rsa-sha1' OR $this->_params['algorithm'] == 'hmac-sha1')
            {
                $response->public_cert = $client['ssh_key'];
                $response->private_cert = $this->_params['signature'];
            }

            if (! Oauth2::signature($this->_params['algorithm'], $string)->check($response, $this->_params['signature']))
            {
                throw new Oauth2_Exception_Token('invalid_signature');
            }
        }

        return $response;
    }

} // END Oauth_Parameter_Token
