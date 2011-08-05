<?php defined('SYSPATH') or die('No direct script access.');

return array(
    /**
     * group definition parameters for each provider
     */
    'default' => array(
        'oauth-uri'     => 'http://docs/oauth/code',
        'token-uri'     => 'http://docs/oauth/token',
        'resource-uri'  => 'http://docs/api/get',

        // Must be code, token, password, client_credentials
        'protocol-flow' => 'code',

        // Parameters for Authorization Code flow
        'code' => array(
            'client_id'     => 'OAL_4D2EA62621E4F',
            'client_secret' => 'sss',
            'token_type'   => 'BEARER', // BEARER, HMAC-SHA1, RSA-SHA1, MD5
            'scope'         => '',
            'state'         => '',
            'redirect_uri'  => 'http://docs/client/do'
        ),
        // Parameters for Implicit Grant Flow
        'token' => array(
            'client_id'     => 'OAL_4D2EA62621E4F',
            'client_secret' => 'sss',
            'token_type'    => 'BEARER', // BEARER, HMAC-SHA1, RSA-SHA1, MD5
            'scope'         => '',
            'state'         => '',
            'redirect_uri'  => 'http://docs/client/do'
        ),
        // Parameters for Resource Owner Password Credentials Flow
        'password' => array(
            'username'      => 'OAL_4D2EA62621E4F',
            'password'      => 'sss',
            'client_id'     => 'OAL_4D2EA62621E4F',
            'client_secret' => 'sss',
            'token_type'   => 'BEARER', // BEARER, HMAC-SHA1, RSA-SHA1, MD5
            'scope'         => '',
            'state'         => '',
        ),
        // Parameters for Client Credentials Flow
        'client_credentials' => array(
            'client_id'     => 'OAL_4D2EA62621E4F',
            'client_secret' => 'sss',
            'token_type'   => 'BEARER', // BEARER, HMAC-SHA1, RSA-SHA1, MD5
            'scope'         => '',
            'state'         => '',
        )
    ),

); // END OAuth client config
