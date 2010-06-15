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
    public function __construct($args = NULL)
    {
        // Client Requests Authorization
        if($args === NULL)
        {
            $params = Oauth::parse_query();

            $this->client_id = Arr::get($params, 'client_id');
            $this->redirect_uri = Arr::get($params, 'redirect_uri');

            // OPTIONAL.  An opaque value used by the client to maintain state between the request and callback.
            if(NULL !== $state = Arr::get($params, 'state'))
                $this->state = $state;
        }
        // Client Requests Access Token
        else
        {
            $params = $_POST;

            $this->client_id = Arr::get($params, 'client_id');

            // REQUIRED if the client identifier has a matching secret.
            $this->client_secret = Arr::get($params, 'client_secret');

            // REQUIRED.  The verification code received from the authorization server.
            $this->code = Arr::get($params, 'code');

            // OPTIONAL.  The scope of the access request expressed as a list of space-delimited strings.
            if(NULL !== $scope = Arr::get($params, 'scope'))
                $this->scope = $scope;

            /**
             * format
             *     OPTIONAL.  The response format requested by the client.  Valu
             *     MUST be one of "json", "xml", or "form".  Alternatively, the
             *     client MAY use the HTTP "Accept" header field with the desire
             *     media type.  Defaults to "json" if omitted and no "Accept"
             *     header field is present.
             */
            if(NULL !== $format = Arr::get($params, 'format'))
                $this->format = $format;
        }
    }

    public function oauth_token($client)
    {
        $response = new Oauth_Response;

        if(property_exists($this, 'state'))
        {
            $response->state = $this->state;
        }

        if(property_exists($this, 'format'))
        {
            $response->format = $this->format;
        }

        if(property_exists($this, 'scope') AND ! isset($client['scope'][$this->scope]))
        {
            $response->error = 'invalid_client_credentials';
            return $response;
        }

        if($client['redirect_uri'] !== $this->redirect_uri)
        {
            $response->error = 'redirect_uri_mismatch';
            return $response;
        }

        // Grants Authorization
        $response->code = $client['code'];

        return $response;
    }

    public function access_token($client)
    {
        $response = new Oauth_Response;

        if(property_exists($this, 'format'))
        {
            $response->format = $this->format;
        }

        // if($client['redirect_uri'] !== $this->redirect_uri)
        // {
            // $response->error = 'redirect_uri_mismatch';
            // return $response;
        // }

        // if($client['client_secret'] !== sha1($this->client_secret))
        // {
            // $response->error = 'invalid_client_credentials';
            // return $response;
        // }

        if($client['code'] !== $this->code)
        {
            $response->error = 'bad_authorization_code';
            return $response;
        }

        $response->expires_in = 3000;
        $response->access_token = $client['access_token'];
        $response->refresh_token = $client['refresh_token'];

        return $response;
    }
}
