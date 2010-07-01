<?php

class Oauth_Parameter_Credentials extends Oauth_Parameter {

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

    public function __construct($args = NULL)
    {
        $params = Oauth::parse_query();
        $this->client_id = Arr::get($params, 'client_id');
        $this->client_secret = Arr::get($params, 'client_secret');

        if(NULL !== $scope = Arr::get($params, 'scope'))
            $this->scope = $scope;

        if(NULL !== $format = Arr::get($params, 'format'))
            $this->format = $format;
    }

    public function oauth_token($client)
    {
        $response = new Oauth_Token;

        if(property_exists($this, 'format'))
        {
            $response->format = $this->format;
        }

        if($client['client_secret'] !== sha1($this->client_secret))
        {
            $response->error = 'unauthorized-client';
            return $response;
        }

        if(property_exists($this, 'scope') AND ! isset($client['scope'][$this->scope]))
        {
            $response->error = 'invalid-scope';
            return $response;
        }

        // Grants Authorization
        $response->expires_in = 3000;
        $response->access_token = $client['access_token'];
        $response->refresh_token = $client['refresh_token'];

        return $response;
    }

} // END Oauth_Parameter_Credentials
