<?php

class Oauth_Parameter_Reflesh extends Oauth_Parameter {

    /**
     * type
     *      REQUIRED.  The parameter value MUST be set to "refresh".
     */
    public $type;

    /**
     * client_id
     *      REQUIRED.  The client identifier as described in Section 2.1.
     */
    public $client_id;

    /**
     * client_secret
     *      REQUIRED if the client was issued a secret.  The client secret.
     */
    public $client_secret;

    /**
     * refresh_token
     *      REQUIRED.  The refresh token associated with the access token to be refreshed.
     */
    public $refresh_token;

    /**
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
        $this->client_secret = $this->get('client_secret');
        $this->refresh_token = $this->get('refresh_token');
        $this->format = $this->get('format');
    }

    public function oauth_token($client)
    {
        $response = new Oauth_Response;

        if($this->format)
        {
            $response->format = $this->format;
        }

        if($client['client_secret'] !== sha1($this->client_secret)
            OR $client['refresh_token'] !== $this->refresh_token)
        {
            $response->error = 'incorrect_client_credentials';
            return $response;
        }

        if($client['timestamp'] + 300 < time())
        {
            $response->error = 'authorization_expired';
            return $response;
        }

        // Grants Authorization
        $response->expires_in = 3000;
        $response->access_token = $client['access_token'];

        return $response;
    }

    public function access_token($client)
    {
        return new Oauth_Token;
    }
}
