<?php

class Oauth_Parameter_Useragent extends Oauth_Parameter {

    /**
     * client_id
     *      REQUIRED.  The client identifier as described in Section 3.1.
     */
    public $client_id;

    /**
     * redirect_uri
     *      REQUIRED unless a redirection URI has been established between
     *      the client and authorization server via other means.  An
     *      absolute URI to which the authorization server will redirect
     *      the user-agent to when the end-user authorization step is
     *      completed.  The authorization server SHOULD require the client
     *      to pre-register their redirection URI.  Authorization servers
     *      MAY restrict the redirection URI to not include a query
     *      component as defined by [RFC3986] section 3.
     */
    public $redirect_uri;

    /**
     * state
     *      OPTIONAL.  An opaque value used by the client to maintain state
     *      between the request and callback.  The authorization server
     *      includes this value when redirecting the user-agent back to the
     *      client.
     *
     * scope
     *      OPTIONAL.  The scope of the access request expressed as a list
     *      of space-delimited strings.  The value of the "scope" parameter
     *      is defined by the authorization server.  If the value contains
     *      multiple space-delimited strings, their order does not matter,
     *      and each string adds an additional access range to the
     *      requested scope.
     *
     * @access    public
     * @return    void
     */
    public function __construct($args = NULL)
    {
        $params = Oauth::parse_query();
        $this->client_id = Arr::get($params, 'client_id');
        $this->redirect_uri = Arr::get($params, 'redirect_uri');

        // OPTIONAL.  An opaque value used by the client to maintain state between the request and callback.
        if(NULL !== $state = Arr::get($params, 'state'))
            $this->state = $state;

        // OPTIONAL.  The scope of the access request expressed as a list of space-delimited strings.
        if(NULL !== $scope = Arr::get($params, 'scope'))
            $this->scope = $scope;
    }

    public function oauth_token($client)
    {
        $response = new Oauth_Token;

        if(property_exists($this, 'state'))
        {
            $response->state = $this->state;
        }

        if($client['redirect_uri'] !== $this->redirect_uri)
        {
            $response->error = 'redirect_uri_mismatch';
            return $response;
        }

        if(property_exists($this, 'scope') AND ! isset($client['scope'][$this->scope]))
        {
            $response->error = 'invalid_client_credentials';
            return $response;
        }

        // Grants Authorization
        $response->expires_in = 3000;
        $response->access_token = $client['access_token'];
        $response->reflash_token = $client['reflash_token'];

        return $response;
    }

} // END Oauth_Parameter_Useragent
