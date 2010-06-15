<?php

class Oauth_Parameter_Username extends Oauth_Parameter {

    /**
     * client_id
     *      REQUIRED.  The client identifier as described in Section 2.1.
     */
    public $client_id;

    /**
     * client_secret
     *      REQUIRED.  The client secret as described in Section 2.1.
     *      OPTIONAL if no client secret was issued.
     */
    public $client_secret;

    /**
     * username
     *       REQUIRED.  The end-user’s username.
     */
    public $username;

    /**
     * password
     *      REQUIRED.  The end-user’s password.
     */
    public $password;

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
        $this->client_id        = Arr::get($params, 'client_id');
        $this->client_secret    = Arr::get($params, 'client_secret');
        $this->username         = Arr::get($params, 'username');
        $this->password         = Arr::get($params, 'password');

        // OPTIONAL.  An opaque value used by the client to maintain state between the request and callback.
        if(NULL !== $state = Arr::get($params, 'state'))
            $this->state = $state;

        // OPTIONAL.  The scope of the access request expressed as a list of space-delimited strings.
        if(NULL !== $scope = Arr::get($params, 'scope'))
            $this->scope = $scope;

        // OPTIONAL.  The scope of the access request expressed as a list of space-delimited strings.
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
            OR $client['password'] !== sha1($this->password)
            OR $client['username'] !== $this->username)
        {
            $response->error = 'invalid_client_credentials';
            return $response;
        }

        if(property_exists($this, 'scope') AND ! isset($client['scope'][$this->scope]))
        {
            $response->error = 'unauthorized_client';
            return $response;
        }

        // Grants Authorization
        $response->expires_in = 3000;
        $response->access_token = $client['access_token'];
        $response->reflash_token = $client['reflash_token'];

        return $response;
    }

    public function access_token($client)
    {
        return new Oauth_Response;
    }
}
