<?php

class Oauth_Parameter_Token extends Oauth_Parameter {

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
     * @param	string	$args	default [ NULL ]
     * @return	void
     */
    public function __construct($args = NULL)
    {
        switch(Request::$method)
        {
            case 'HEAD':
                $params = Oauth::parse_header();
                $params['oauth_token'] = isset($params['token']) ? $params['token'] : NULL;
                unset($params['token']);
                break;
            case 'PUT':
            case 'POST':
            case 'DELETE':
                $params = $_POST;
                break;
            case 'GET':
                $params = Oauth::parse_query();
                break;
            default:
                $params = array();
                break;
        }

        if(is_array($args)) $params += $args;

        foreach($params as $key => $val)
        {
            $this->$key = $val;
        }
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
        return new Oauth_Response;
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
        $response = new Oauth_Response;

        if(property_exists($this, 'format'))
        {
            $response->format = $this->format;
        }

        if(property_exists($this, 'error'))
        {
            $response->error = $this->error;
            return $response;
        }

        if($client['access_token'] !== $this->oauth_token)
        {
            $response->error = 'invalid_oauth_token';
            return $response;
        }

        if(property_exists($this, 'token_secret') AND $client['token_secret'] !== sha1($this->token_secret))
        {
            $response->error = 'invalid_oauth_token';
            return $response;
        }

        if(property_exists($this, 'nonce') AND $client['nonce'] !== $this->nonce)
        {
            $response->error = 'invalid_nonce';
            return $response;
        }

        if(property_exists($this,'timestamp') AND
            $client['timestamp'] + Kohana::config('oauth_server')->get('duration') < $this->timestamp)
        {
            $response->error = 'invalid_timestamp';
            return $response;
        }

        // verify the signature
        if(property_exists($this, 'signature') AND property_exists($this, 'algorithm'))
        {
            $base_url = URL::base(FALSE, TRUE).Request::$current->uri;

            $string = Oauth::normalize(Request::$method, $base_url, $params);

            if($this->algorithm == 'rsa-sha1' OR $this->algorithm == 'hmac-sha1')
            {
                $response->public_cert = '';
                $response->private_cert = '';
            }

            if (! Oauth::signature($this->algorithm, $string)->check($token, $this->signature))
            {
                $response->error = 'invalid_signature';
                return $response;
            }
        }

        return $response;
    }
}
