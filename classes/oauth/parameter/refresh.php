<?php

class Oauth_Parameter_Refresh extends Oauth_Parameter {

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

    public function __construct($args = NULL)
    {
        $params = Oauth::parse_query();
        $this->client_id = Arr::get($params, 'client_id');
        $this->client_secret = Arr::get($params, 'client_secret');
        $this->refresh_token = Arr::get($params, 'refresh_token');

        // OPTIONAL.  An opaque value used by the client to maintain state between the request and callback.
        if(NULL !== $state = Arr::get($params, 'state'))
            $this->state = $state;

        if(NULL !== $format = Arr::get($params, 'format'))
            $this->format = $format;
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

        if($client['client_secret'] !== sha1($this->client_secret)
            OR $client['refresh_token'] !== $this->refresh_token)
        {
            $response->error = 'invalid_client_credentials';
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
        return new Oauth_Response;
    }
}
