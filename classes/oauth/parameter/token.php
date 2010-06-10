<?php

class Oauth_Parameter_Token extends Oauth_Parameter {

    /**
     * REQUIRED
     *
     * @access	public
     * @var		string	$oauth_token
     */
    public $oauth_token;

    public function __construct($flag = FALSE)
    {
        switch(Request::$method)
        {
            case 'HEAD':
                $params = parent::parse_header();
                $params['oauth_token'] = isset($params['token']) ? $params['token'] : NULL;
                unset($params['token']);
                break;
            case 'PUT':
            case 'POST':
            case 'DELETE':
                $params = parent::parse_post();
                break;
            case 'GET':
                $params = parent::parse_query();
                break;
            default:
                $params = array();
                break;
        }
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
        return new Oauth_Token;
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
        $token = new Oauth_Token;

        if($this->format)
        {
            $token->format = $this->format;
        }

        if($client['access_token'] !== $this->oauth_token)
        {
            $token->error = 'incorrect_oauth_token';
            return $token;
        }

        if( ! empty($this->token_secret) AND $client['token_secret'] !== sha1($this->token_secret))
        {
            $token->error = 'incorrect_oauth_token';
            return $token;
        }

        if( ! empty($this->nonce) AND $client['nonce'] !== $this->nonce)
        {
            $token->error = 'incorrect_nonce';
            return $token;
        }

        if( ! empty($this->timestamp) AND
            $client['timestamp'] + Kohana::config('oauth_server')->get('duration') < $this->timestamp)
        {
            $token->error = 'incorrect_nonce';
            return $token;
        }

        // verify the signature
        if( ! empty($this->signature))
        {
            $base_url = URL::base(FALSE, TRUE).$this->request->controller.'/'.$this->request->action;

            $string = Oauth::normalize(Request::$method, $base_url, $params);

            if($this->algorithm == 'rsa-sha1' OR $this->algorithm == 'hmac-sha1')
            {
                $token->public_cert = '';
                $token->private_cert = '';
            }

            if ( ! empty($this->algorithm)
                OR ! Oauth::signature($this->algorithm, $string)->check($token, $this->signature))
            {
                $token->error = 'incorrect_signature';
                return $token;
            }
        }

        return new Oauth_Token;
    }
}
