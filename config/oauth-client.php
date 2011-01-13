<?php defined('SYSPATH') or die('No direct script access.');

return array(
    /**
     * group definition parameters for each provider
     */
    'default' => array(
        'oauth_uri'     => 'http://docs/oauth/code',
        'token_uri'     => 'http://docs/oauth/token',
        'access_uri'    => 'http://docs/api',
        'redirect_uri'  => 'http://docs/client/do',
        /**
         * "code" for requesting an authorization code, or 
         * "token" for requesting an access token, or
         * "code_and_token" to request both.
         */
        'response_type' => 'code',
        /**
         * "authorization_code", "password", "refresh_token", "client_credentials", 
         * or an absolute URI identifying an assertion format supported by the authorization
         */
        'grant_type'    => 'authorization_code',
        'client_id'     => 'OAL_4D2EA62621E4F',
        'client_secret' => 'sss',
        'state'         => '',
        'secret_type'   => 'plaintext', // PLAINTEXT, HMAC-SHA1, RSA-SHA1, MD5
        'nonce'         => uniqid()
    ),

); // END OAuth client config
