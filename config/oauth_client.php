<?php defined('SYSPATH') or die('No direct script access.');

return array(
    /**
     * group definition parameters for each provider
     */
    'default' => array(
        'oauth_uri'     => 'http://docs/oauth/authorize',
        'token_uri'     => 'http://docs/oauth/token',
        'access_uri'    => 'http://docs/api',
        'redirect_uri'  => 'http://docs/client/do',
        // parameters for oauth request
        'type'          => 'web_server',
        'grant_type'    => 'authorization_code',
        'client_id'     => 'OA_4bfbc43769917',
        'client_secret' => '',
        'state'         => '',
        'secret_type'   => 'plaintext', // PLAINTEXT, HMAC-SHA1, RSA-SHA1, MD5
        'nonce'         => md5(microtime().mt_rand())
    ),
);