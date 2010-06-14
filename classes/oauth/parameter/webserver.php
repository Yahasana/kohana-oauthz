<?php

class Oauth_Parameter_Webserver extends Oauth_Parameter {

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
        $this->client_id = Oauth::get('client_id');
        $this->redirect_uri = Oauth::get('redirect_uri');

        // Client Requests Authorization
        if($flag === FALSE)
        {
            // OPTIONAL.  An opaque value used by the client to maintain state between the request and callback.
            $this->state = Oauth::get('state');

            // OPTIONAL.  The scope of the access request expressed as a list of space-delimited strings.
            $this->scope = Oauth::get('scope');
        }
        // Client Requests Access Token
        else
        {
            // REQUIRED if the client identifier has a matching secret.
            $this->client_secret = Oauth::get('client_secret');

            // REQUIRED.  The verification code received from the authorization server.
            $this->code = Oauth::get('code');

            /**
             * format
             *     OPTIONAL.  The response format requested by the client.  Valu
             *     MUST be one of "json", "xml", or "form".  Alternatively, the
             *     client MAY use the HTTP "Accept" header field with the desire
             *     media type.  Defaults to "json" if omitted and no "Accept"
             *     header field is present.
             */
            $this->format = Oauth::get('format');
        }
    }

    public function oauth_token($client)
    {
        $response = new Oauth_Response;

        if($this->state)
        {
            $response->state = $this->state;
        }

        if($client['redirect_uri'] !== $this->redirect_uri)
        {
            $response->error = 'redirect_uri_mismatch';
            return $response;
        }

        if( ! empty($client['scope']) AND ! isset($client['scope'][$this->scope]))
        {
            $response->error = 'incorrect_client_credentials';
            return $response;
        }

        // Grants Authorization
        $response->code = $client['code'];

        return $response;
    }

    public function access_token($client)
    {
        $response = new Oauth_Response;

        if($this->format)
        {
            $response->format = $this->format;
        }

        if($client['redirect_uri'] !== $this->redirect_uri)
        {
            $response->error = 'redirect_uri_mismatch';
            return $response;
        }

        if($client['client_secret'] !== sha1($this->client_secret))
        {
            $response->error = 'incorrect_client_credentials';
            return $response;
        }

        if($client['code'] !== $this->code)
        {
            $response->error = 'bad_verification_code';
            return $response;
        }

        $response->expires_in = 3000;
        $response->access_token = $client['access_token'];
        $response->reflash_token = $client['reflash_token'];

        return $response;
    }
}
