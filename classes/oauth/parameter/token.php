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
        $this->oauth = $oauth;
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
     * @return	boolean
     */
    public function oauth_token($client)
    {
        return TRUE;
    }

    /**
     * MUST verify that the verification code, client identity, client secret,
     * and redirection URI are all valid and match its stored association.
     *
     * @access  protected
     * @throw   redirect_uri_mismatch, bad_verification_code, incorrect_client_credentials
     * @return  boolean
     * @todo    impletement timestamp, nonce, signature checking
     */
    public function access_token($client)
    {
        $params = array(
            'oauth_token'   => 'web_server',
            'timestamp'     => 'get_from_query',
            'nonce'         => $this->post('client_secret'),
            'signature'     => $this->post('code'),
            'algorithm'     => '',
            'format'        => 'json' // OPTIONAL. "json", "xml", or "form"
        );

        if(empty($params['oauth_token']))
            throw new Oauth_Exception('incorrect_client_credentials');

        $token = new Oauth_Token($params['oauth_token']);

        if(isset($params['token_secret']))
        {
            $token->token_secret = $params['token_secret'];
        }

        // verify that timestamp is recentish
        if(isset($params['timestamp']))
        {
            if (time() - $params['timestamp'] > Kohana::config('oauth_server')->get('duration'))
            {
                throw new Oauth_Exception('incorrect_client_credentials');
            }
            $token->timestamp = $params['timestamp'];
        }

        // verify that the nonce is uniqueish
        if(isset($params['nonce']))
        {
            if ($this->oauth->lookup_nonce($params['oauth_token'], $params['nonce'], $params['timestamp']))
            {
                throw new Oauth_Exception('incorrect_client_credentials');
            }
            $token->nonce = $params['nonce'];
        }

        // verify the signature
        if(isset($params['signature']))
        {
            $base_url = URL::base(FALSE, TRUE).$this->request->controller.'/'.$this->request->action;

            $string = Oauth::normalize($params['method'], $base_url, $params);

            if($params['algorithm'] == 'rsa-sha1' OR $params['algorithm'] == 'hmac-sha1')
            {
                $token->public_cert = '';
                $token->private_cert = '';
            }

            if ( ! empty($params['algorithm']) OR ! Oauth::signature(
                    $params['algorithm'], $string
                )->check($token, $params['signature']))
            {
                throw new Oauth_Exception('bad_verification_code');
            }
            $token->signature = $params['signature'];
            $token->algorithm = $params['algorithm'];
        }

        return TRUE;
    }
}
