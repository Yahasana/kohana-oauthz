<?php

class Oauth_Token_Access extends Oauth_Token {

    /**
     * access_token
     *      REQUIRED.  The access token issued by the authorization server.
     */
    public $access_token;

    /**
     * expires_in
     *      OPTIONAL.  The duration in seconds of the access token
     *      lifetime.
     * refresh_token
     *      OPTIONAL.  The refresh token used to obtain new access tokens
     *      using the same end-user access grant as described in Section 3.
     * scope
     *      OPTIONAL.  The scope of the access token as a list of space-
     *      delimited strings.  The value of the "scope" parameter is
     *      defined by the authorization server.  If the value contains
     *      multiple space-delimited strings, their order does not matter,
     *      and each string adds an additional access range to the
     *      requested scope.
     */
}
