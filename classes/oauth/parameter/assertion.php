<?php

class Oauth_Parameter_Assertion extends Oauth_Parameter {

    /**
     * type
     *      REQUIRED.  The parameter value MUST be set to "assertion".
     */
    public $type;

    /**
     * assertion_format
     *      REQUIRED.  The format of the assertion as defined by the
     *      authorization server.  The value MUST be an absolute URI.
     */
    public $assertion_format;

    /**
     * assertion
     *      REQUIRED.  The assertion.
     */
    public $assertion;

    /**
     * client_id
     *      OPTIONAL.  The client identifier as described in Section 2.1.
     *      The authorization server MAY require including the client
     *      credentials with the request based on the assertion properties.
     * client_secret
     *      OPTIONAL.  The client secret as described in Section 2.1.  MUST
     *      NOT be included if the "client_id" parameter is omitted.
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
        $this->assertion_format = $this->get('username');
        $this->assertion = $this->get('password');
        $this->client_id = $this->get('client_id');
        $this->client_secret = $this->get('client_secret');
        $this->scope = $this->get('scope');
        $this->format = $this->get('format');
    }

    public function oauth_token($client)
    {
        $token = new Oauth_Token;

        if(! empty($this->assertion_format) AND $client['format'] !== $this->assertion_format)
        {
            $token->error = 'unknown_format';
            return $token;
        }
        else
        {
            $token->assertion_format = $this->assertion_format;
        }

        if($client['assertion'] !== $this->assertion
            OR (! empty($this->client_id) AND $client['client_id'] !== $this->client_id)
            OR (! empty($this->client_secret) AND $client['client_secret'] !== sha1($this->client_secret))
            OR (! empty($client['scope']) AND ! isset($client['scope'][$this->scope]))
        {
            $token->error = 'invalid_assertion';
            return $token;
        }

        // Grants Authorization
        // The authorization server SHOULD NOT issue a refresh token.
        $token->expires_in = 3000;
        $token->access_token = $client['access_token'];

        return $token;
    }

    public function access_token($client)
    {
        return new Oauth_Token;
    }
}
