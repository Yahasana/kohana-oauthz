<?php defined('SYSPATH') or die('No direct script access.');
/**
 * TRUE - mandatory/enable, FALSE - option/disable
 */
return array(

    'default' => array(

        'realm'     => 'REST API',

        // '' - no login required, 'basic' - unsecure login, 'digest' - more secure login, 'OAuth' - cool
        'auth'      => 'OAuth',

        /**
         * Set to FALSE to ignore the HTTP Accept and speed up each request a little.
         * Only do this if you are using the follow formats or /format/xml in URLs
         */
        'http_accept'=> FALSE,

        'formats'   => array(
            'json'      => TRUE,    # 'application/json'
            'xml'       => TRUE,    # 'application/xml'
            'form'      => FALSE,   # 'text/plain'
            'html'      => FALSE,   # 'text/html'
            'csv'       => FALSE,   # 'application/csv'
            'php'       => FALSE,   # 'text/plain'
            'serialize' => FALSE    # 'application/vnd.php.serialized'
        ),

        'types'    => array(
            'request'       => 'web_server',
            'deny'          => 'user_denied',
            'access'        => 'username',
            'credential'    => 'client_credentials'
        ),

        'token'    => array(
            'access_token'  => '',
            'expires_in'    => '',
            'refresh_token' => '',
            'scope'         => '',
        ),

        'request_methods'   => array(
            'HEAD'      => TRUE,
            'GET'       => TRUE,
            'POST'      => TRUE,
            'PUT'       => TRUE,
            'DELETE'    => TRUE
        ),

        /**
         * Parameters should be required when request authorization code
         * cryptographic token or bear token
         */
        'code_params'     => array(
            'client_id'     => TRUE,
            'redirect_uri'  => TRUE,
            'scope'         => FALSE,
            'state'         => FALSE
        ),

        /**
         * Parameters should be required when request access token
         */
        'grant_params'     => array(
            'authorization_code'    => array(
                'code'              => TRUE,
                'redirect_uri'      => TRUE,
                'client_id'         => TRUE,
                'client_secret'     => TRUE,
            ),
            'password'     => array(
                'username'          => TRUE,
                'password'          => TRUE,
                'client_id'         => TRUE,
                'client_secret'     => TRUE,
            ),
            'assertion'             => array(
                'assertion_type'    => TRUE,
                'assertion'         => TRUE,
                'client_id'         => TRUE,
                'client_secret'     => TRUE,

            ),
            'refresh_token'         => array(
                'refresh_token'     => TRUE,
                'client_id'         => TRUE,
                'client_secret'     => TRUE,
            ),
            'none'                  => array(
                'client_id'         => TRUE,
                'client_secret'     => TRUE,
            ),
            'signature'             => FALSE
        ),

        /**
         * Parameters should be required when access protected resource
         * cryptographic token or bear token
         */
        'access_params'     => array(
            'oauth_token'   => TRUE,
            'timestamp'     => TRUE,
            'scope'         => FALSE,
            'nonce'         => FALSE,
            'algorithm'     => FALSE,
            'signature'     => FALSE
        ),

        'secret_types'  => array(
            'plaintext' => TRUE,
            'rsa-sha1'  => TRUE,
            'hmac-sha1' => TRUE,
            'md5'       => FALSE
        ),

        'scopes'    => array(
            'get'       => TRUE,
            'create'    => TRUE,
            'update'    => TRUE,
            'delete'    => TRUE
        ),

        'max_requests'  => array(
            500,        // common client
            1000,       // first class client
            1500,       // vip client
        ),

        'durations'     => array(
            'code'          => 120,     // authorization code expires time, default is 2 minutes
            'oauth_token'   => 3600,    // token expires time, default is 1 hour
            'refresh_token' => 86400    // refresh token expires time, default is 1 day
        ),

        // section-3.2.1 Error Codes
        'code_errors'   => array(
            'invalid_request'       => array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'invalid_client'     => array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'unauthorized_client'   => array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'redirect_uri_mismatch' => array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'access_denied'         => array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'unsupported_response_type' => array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'invalid_scope'         => array(
                'error_description' => '',
                'error_uri'         => '',
            )
        ),
        // section-4.3.1 Error Codes
        'token_errors'  => array(
            'invalid_request'       => array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'invalid_client' => array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'unauthorized_client'   => array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'invalid_grant'         => array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'unsupported_grant_type'=> array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'invalid_scope'         => array(
                'error_description' => '',
                'error_uri'         => '',
            )
        ),
        // section-5.2.1 Error Codes
        'access_errors' => array(
            'invalid_request'       => array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'invalid_token'         => array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'expired_token'         => array(
                'error_description' => '',
                'error_uri'         => '',
            ),
            'insufficient_scope'    => array(
                'error_description' => '',
                'error_uri'         => '',
            )
        )
    )

); // END OAuth server config
