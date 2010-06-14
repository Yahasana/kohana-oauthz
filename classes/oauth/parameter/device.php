<?php

class Oauth_Parameter_Device extends Oauth_Parameter {

    /**
     * client_id
     *      REQUIRED.  The client identifier as described in Section 2.1.
     */
    public $client_id;

    /**
     * scope
     *      OPTIONAL.  The scope of the access request expressed as a list
     *      of space-delimited strings.  The value of the "scope" parameter
     *      is defined by the authorization server.  If the value contains
     *      multiple space-delimited strings, their order does not matter,
     *      and each string adds an additional access range to the
     *      requested scope.
     * format
     *      OPTIONAL.  The response format requested by the client.  Value
     *      MUST be one of "json", "xml", or "form".  Alternatively, the
     *      client MAY use the HTTP "Accept" header field with the desired
     *      media type.  Defaults to "json" if omitted and no "Accept"
     *      header field is present.
     */

    public function __construct($args = NULL)
    {
        $params = Oauth::parse_query();
        $this->client_id = Oauth::get('client_id');
        $this->format = Oauth::get('format');
        if($args === FALSE)
        {
            $this->scope = Oauth::get('scope');
        }
        else
        {
            $this->code = Oauth::get('code');
        }
    }

    public function oauth_token($client)
    {
        $response = new Oauth_Response;

        if($this->format)
        {
            $response->format = $this->format;
        }

        if( ! empty($client['scope']) AND ! isset($client['scope'][$this->scope]))
        {
            $response->error = 'authorization_declined';
            return $response;
        }

        // REQUIRED.  The verification code.
        $response->code = $client['code'];

        // REQUIRED.  The end-user code.
        $response->user_code = $client['user_code'];

        // REQUIRED.  The end-user verification URI on the authorization server.
        $response->verification_uri = $client['verification_uri'];

        // OPTIONAL.  The duration in seconds of the verification code lifetime.
        $response->expires_in = 300;

        /**
         *OPTIONAL.  The minimum amount of time in seconds that the
         * client SHOULD wait between polling requests to the token endpoint.
         */
        $response->interval = 5;

        return $response;
    }

    public function access_token($client)
    {
        $response = new Oauth_Response;

        if($this->format)
        {
            $response->format = $this->format;
        }

        if($client['code'] !== $this->code)
        {
            $response->error = 'bad_verification_code';
            return $response;
        }

        if($client['interval'] > 5)
        {
            $response->error = 'slow_down';
            return $response;
        }

        if($client['timestamp'] + 300 > time())
        {
            $response->error = 'code_expired';
            return $response;
        }

        $response->expires_in = 3000;
        $response->access_token = $client['access_token'];
        $response->reflash_token = $client['reflash_token'];

        return $response;
    }
}
