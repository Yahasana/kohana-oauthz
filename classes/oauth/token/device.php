<?php

class Oauth_Token_Access extends Oauth_Token {

    /**
     * code
     *     REQUIRED.  The verification code.
     */
    public $code;

    /**
     * user_code
     *     REQUIRED.  The end-user code.
     */
    public $user_code;

    /**
     * verification_uri
     *     REQUIRED.  The end-user verification URI on the authorization
     *     server.  The URI should be short and easy to remember as end-
     *     users will be asked to manually type it into their user-agent.
     */
    public $verification_uri;

    /**
     * expires_in
     *     OPTIONAL.  The duration in seconds of the verification code
     *     lifetime.
     * interval
     *     OPTIONAL.  The minimum amount of time in seconds that the
     *     client SHOULD wait between polling requests to the token
     *     endpoint.
     */
}
