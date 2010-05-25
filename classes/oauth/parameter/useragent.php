<?php

class Oauth_Parameter_Useragent extends Oauth_Parameter {

    /**
     * type
     *      REQUIRED.  The parameter value MUST be set to "user_agent".
     *
     * client_id
     *      REQUIRED.  The client identifier as described in Section 3.1.
     *
     * redirect_uri
     *      REQUIRED unless a redirection URI has been established between
     *      the client and authorization server via other means.  An
     *      absolute URI to which the authorization server will redirect
     *      the user-agent to when the end-user authorization step is
     *      completed.  The authorization server SHOULD require the client
     *      to pre-register their redirection URI.  Authorization servers
     *      MAY restrict the redirection URI to not include a query
     *      component as defined by [RFC3986] section 3.
     *
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
     * secret_type
     *      OPTIONAL.  The access token secret type as described by
     *      Section 5.3.  If omitted, the authorization server will issue a
     *      bearer token (an access token without a matching secret) as
     *      described by Section 5.2.
     *
     * @author    sumh <oalite@gmail.com>
     * @date      2010-05-14 16:35:35
     * @access    public
     * @return    void
     */
    public function __construct()
    {
        $this->client_id = $this->get('client_id');
        $this->redirect_uri = $this->get('redirect_uri');
    }

    public function authorization_check($client)
    {
        if(! $tmp = $this->get('redirect_uri') or $tmp != $client['redirect_uri'])
        {
            return $this->error = 'redirect_uri_mismatch';
        }
        else if($format = $this->get('format') and Kohana::config('oalite_server.default')->format[$format] === TRUE)
        {
            $this->format = $format;
        }

            return TRUE;
    }

    public function access_token_check($client)
    {
        return TRUE;
    }
}
