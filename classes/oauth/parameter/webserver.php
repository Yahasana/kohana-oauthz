<?php

class Oauth_Parameter_Webserver extends Oauth_Parameter {

    /**
     * REQUIRED.  The parameter value MUST be set to "web_server".
     *
     * @access	public
     * @var		string	$type
     */
    public $type;

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
    public function __construct($flag = FALSE)
    {
        $this->type     = $this->get('type');
        $this->client_id = $this->get('client_id');
        $this->redirect_uri = $this->get('redirect_uri');

        // Client Requests Authorization
        if($flag === FALSE)
        {
            // OPTIONAL.  An opaque value used by the client to maintain state between the request and callback.
            $this->state = $this->get('state');

            // OPTIONAL.  The scope of the access request expressed as a list of space-delimited strings.
            $this->scope = $this->get('scope');

            // OPTIONAL.  The parameter value must be set to "true" or "false".
            $this->immediate = $this->get('immediate');
        }
        // Client Requests Access Token
        else
        {
            // REQUIRED if the client identifier has a matching secret.
            $this->client_secret = $this->get('client_secret');

            // REQUIRED.  The verification code received from the authorization server.
            $this->code = $this->get('code');

            /**
             * format
             *     OPTIONAL.  The response format requested by the client.  Valu
             *     MUST be one of "json", "xml", or "form".  Alternatively, the
             *     client MAY use the HTTP "Accept" header field with the desire
             *     media type.  Defaults to "json" if omitted and no "Accept"
             *     header field is present.
             */
            $this->format = $this->get('format');
        }
    }

    public function oauth_token($client)
    {
        $token = new Oauth_Token;

        if($this->state)
        {
            $token->state = $this->state;
        }

        if($client['redirect_uri'] !== $this->redirect_uri)
        {
            $token->error = 'redirect_uri_mismatch';
            return $token;
        }

        if( ! empty($client['scope']) AND ! isset($client['scope'][$this->scope]))
        {
            $token->error = 'incorrect_client_credentials';
            return $token;
        }

        if($this->immediate)
        {
            // TODO
        }

        // Grants Authorization
        $token->code = $client['code'];

        return $token;
    }

    public function access_token($client)
    {
        $token = new Oauth_Token;

        if($this->format)
        {
            $token->format = $this->format;
        }

        if($client['redirect_uri'] !== $this->redirect_uri)
        {
            $token->error = 'redirect_uri_mismatch';
            return $token;
        }

        if($client['client_secret'] !== sha1($this->client_secret))
        {
            $token->error = 'incorrect_client_credentials';
            return $token;
        }

        if($client['code'] !== $this->code)
        {
            $token->error = 'bad_verification_code';
            return $token;
        }

        $token->expires_in = 3000;
        $token->access_token = $client['access_token'];
        $token->reflash_token = $client['reflash_token'];

        return $token;
    }
}
