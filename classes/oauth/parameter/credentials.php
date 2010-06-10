<?php

class Oauth_Parameter_Credentials extends Oauth_Parameter {

    /**
     * type
     *     REQUIRED.  The parameter value MUST be set to "client_credentials".
     */
    public $type;

    /**
     * client_id
     *     REQUIRED.  The client identifier as described in Section 2.1.
     */
    public $client_id;

    /**
     * client_secret
     *     REQUIRED.  The client secret as described in Section 2.1.
     */
    public $client_secret;

    /**
     * scope
     *     OPTIONAL.  The scope of the access request expressed as a list
     *     of space-delimited strings.  The value of the "scope" parameter
     *     is defined by the authorization server.  If the value contains
     *     multiple space-delimited strings, their order does not matter,
     *     and each string adds an additional access range to the
     *     requested scope.
     * format
     *     OPTIONAL.  The response format requested by the client.  Value
     *     MUST be one of "json", "xml", or "form".  Alternatively, the
     *     client MAY use the HTTP "Accept" header field with the desired
     *     media type.  Defaults to "json" if omitted and no "Accept"
     *     header field is present.
     */

    public function __construct($flag = FALSE)
    {
        $this->type = $type;
        $this->client_id = $this->get('client_id');
        $this->client_secret = $this->get('client_secret');
        $this->scope = $this->get('scope');
        $this->format = $this->get('format');
    }

    public function oauth_token($client)
    {
        $token = new Oauth_Token;

        if($this->format)
        {
            $token->format = $this->format;
        }

        if($client['client_secret'] !== sha1($this->client_secret))
        {
            $token->error = 'incorrect_client_credentials';
            return $token;
        }

        if( ! empty($client['scope']) AND ! isset($client['scope'][$this->scope]))
        {
            $token->error = 'incorrect_client_credentials';
            return $token;
        }

        // Grants Authorization
        $token->expires_in = 3000;
        $token->access_token = $client['access_token'];
        $token->reflash_token = $client['reflash_token'];

        return $token;
    }

    public function access_token($client)
    {
        return new Oauth_Token;
    }
}
