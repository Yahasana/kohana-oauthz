<?php defined('SYSPATH') or die('No direct script access.');

return array(
    //callback to my site
    'oauth_callback'    => 'http://k/client/okay',

    /**
     * group definition parameters for each provider
     */
    'default' => array(
        'request_uri'    => 'http://k/oauth/authorize',
        'authorize_uri'    => 'http://k/oauth/authorize',
        'access_uri'    => 'http://k/oauth/access',
        'req_code_params'        => array(
            'type'    => 'web_server',
            'client_id'    => 'oa888888',   // provider key
            'client_secret'    => '',
            'redirect_uri'=> 'http://k/client/okay',
            'state'        => '',
            'immediate'    => 'false',
            'secret_type'=> 'plaintext'
            //~ 'oauth_signature_method'=> 'HMAC-SHA1', // PLAINTEXT, HMAC-SHA1, RSA-SHA1, MD5
            //~ 'oauth_timestamp'        => time(),
            //~ 'oauth_nonce'            => md5(microtime().mt_rand()),
            //~ 'oauth_version'            => '1.0',
            //~ 'oauth_signature'        => NULL,
            //~ 'oauth_token'            => NULL,    //
            //~ 'oauth_token_secret'    => NULL,
            //~ 'oauth_verifier'        => NULL,    // for access process
        ),
        'req_token_params'    => array(
            'type'    => 'web_server',
            ''
        )
    ),
    'gContacts' => array(
        'request_uri'    => 'http://k/oauth/request',
        'authorize_uri'    => 'http://k/oauth/authorize',
        'access_uri'    => 'http://k/oauth/access',
        'params'        => array(
            'oauth_consumer_key'    => 'key',   // provider key
            'oauth_signature_method'=> 'HMAC-SHA1', // PLAINTEXT, HMAC-SHA1, RSA-SHA1, MD5
            //~ 'oauth_timestamp'        => time(),
            //~ 'oauth_nonce'            => md5(microtime().mt_rand()),
            //~ 'oauth_version'            => '1.0'
        )
    ),
);