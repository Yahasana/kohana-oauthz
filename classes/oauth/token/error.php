<?php

class Oauth_Token_Error extends Oauth_Token {

    /**
     * error
     *     REQUIRED.  The parameter value MUST be set to "user_denied", ...
     *
     * web-server flow: "user_denied","redirect_uri_mismatch","bad_verification_code","incorrect_client_credentials"
     * user-agent flow:
     * device flow: "authorization_declined","bad_verification_code","authorization_pending","slow_down","code_expired"
     * username flow: "incorrect_client_credentials","unauthorized_client"
     * Client Credentials Flow: "incorrect_client_credentials"
     * accession flow: "invalid_assertion","unknown_format"
     * reflesh token: "incorrect_client_credentials","authorization_expired"
     */
    public $error;

    /**
     * state
     *     REQUIRED if the "state" parameter was present in the client
     *     authorization request.  Set to the exact value received from
     *     the client.
     */
}
