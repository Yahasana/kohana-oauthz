<?php

class Oauth_Parameter_Device extends Oauth_Parameter {

    /**
     * type
     *      REQUIRED.  The parameter value MUST be set to "device_code".
     */
    public $type;

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

    public function __construct($flag = FALSE)
    {
        $this->type = $this->get('type');
        $this->client_id = $this->get('client_id');
        $this->format = $this->get('format');
        if($flag === FALSE)
        {
            $this->scope = $this->get('scope');
        }
        else
        {
            $this->code = $this->get('code');
        }
    }

    public function oauth_token($client)
    {
        $token = new Oauth_Token;

        if($this->format)
        {
            $token->format = $this->format;
        }

        if( ! empty($client['scope']) AND ! isset($client['scope'][$this->scope]))
        {
            $token->error = 'authorization_declined';
            return $token;
        }

        // REQUIRED.  The verification code.
        $token->code = $client['code'];

        // REQUIRED.  The end-user code.
        $token->user_code = $client['user_code'];

        // REQUIRED.  The end-user verification URI on the authorization server.
        $token->verification_uri = $client['verification_uri'];

        // OPTIONAL.  The duration in seconds of the verification code lifetime.
        $token->expires_in = 300;

        /**
         *OPTIONAL.  The minimum amount of time in seconds that the
         * client SHOULD wait between polling requests to the token endpoint.
         */
        $token->interval = 5;

        return $token;
    }

    public function access_token($client)
    {
        $token = new Oauth_Token;

        if($this->format)
        {
            $token->format = $this->format;
        }

        if($client['code'] !== $this->code)
        {
            $token->error = 'bad_verification_code';
            return $token;
        }

        if($client['interval'] > 5)
        {
            $token->error = 'slow_down';
            return $token;
        }

        if($client['timestamp'] + 300 > time())
        {
            $token->error = 'code_expired';
            return $token;
        }

        $token->expires_in = 3000;
        $token->access_token = $client['access_token'];
        $token->reflash_token = $client['reflash_token'];

        return $token;
    }
}
