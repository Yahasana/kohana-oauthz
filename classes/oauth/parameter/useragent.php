<?php

class Oauth_Parameter_Useragent extends Oauth_Parameter {

    /**
     * type
     *      REQUIRED.  The parameter value MUST be set to "user_agent".
     */
    public $type;

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
     * immediate
     *      OPTIONAL.  The parameter value must be set to "true" or
     *      "false".  If set to "true", the authorization server MUST NOT
     *      prompt the end-user to authenticate or approve access.
     *      Instead, the authorization server attempts to establish the
     *      end-user's identity via other means (e.g. browser cookies) and
     *      checks if the end-user has previously approved an identical
     *      access request by the same client and if that access grant is
     *      still active.  If the authorization server does not support an
     *      immediate check or if it is unable to establish the end-user's
     *      identity or approval status, it MUST deny the request without
     *      prompting the end-user.  Defaults to "false" if omitted.
     *
     * @access    public
     * @return    void
     */
    public function __construct($flag = FALSE)
    {
        $this->type     = $this->get('type');
        $this->client_id = $this->get('client_id');
        $this->redirect_uri = $this->get('redirect_uri');

        // OPTIONAL.  An opaque value used by the client to maintain state between the request and callback.
        $this->state = $this->get('state');

        // OPTIONAL.  The scope of the access request expressed as a list of space-delimited strings.
        $this->scope = $this->get('scope');

        // OPTIONAL.  The parameter value must be set to "true" or "false".
        $this->immediate = $this->get('immediate');
    }

    public function oauth_token($client)
    {
        $response = new Oauth_Response;

        if($this->state)
        {
            $response->state = $this->state;
        }

        if($client['redirect_uri'] !== $this->redirect_uri)
        {
            $response->error = 'redirect_uri_mismatch';
            return $response;
        }

        if( ! empty($client['scope']) AND ! isset($client['scope'][$this->scope]))
        {
            $response->error = 'incorrect_client_credentials';
            return $response;
        }

        if($this->immediate)
        {
            // TODO
        }

        // Grants Authorization
        $response->expires_in = 3000;
        $response->access_token = $client['access_token'];
        $response->reflash_token = $client['reflash_token'];

        return $response;
    }

    public function access_token($client)
    {
        return new Oauth_Token;
    }
}
