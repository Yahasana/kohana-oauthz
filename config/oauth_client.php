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
        // parameters for oauth request
        'response_type' => 'code',
        'grant_type'    => 'authorization_code',
        'client_id'     => 'OA_4bfbc43769917',
        'client_secret' => 'asdf',
        'state'         => '',
        'secret_type'   => 'plaintext', // PLAINTEXT, HMAC-SHA1, RSA-SHA1, MD5
        'nonce'         => uniqid()
    ),
);