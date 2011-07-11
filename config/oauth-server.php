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

        /**
         * Parameters should be required when access protected resource
         * cryptographic token or bear token
         */
        'token'    => array(
            'access_token'  => '',
            'expires_in'    => '',
            'refresh_token' => '',
            'scope'         => '',
        ),

        /**
         *  TODO: Authentication methods for each flows
         */
        'methods'   => array(
            'authorization_code' => array('basic', 'digest', 'mac'),
            'access_token'       => array('bearer', 'mac'),
        ),

        /**
         * Parameters should be required when request authorization code
         * cryptographic token or bear token
         */
        'params'     => array(
            // Parameters should be required for response_type endpoint
            'code'                  => array(
                'client_id'         => TRUE,
                'redirect_uri'      => TRUE,
                'scope'             => FALSE,
                'state'             => FALSE
            ),
            'token'                 => array(
                'code'              => TRUE,
                'redirect_uri'      => TRUE,
                'client_id'         => TRUE,
                'client_secret'     => TRUE,
                'scope'             => FALSE,
                'state'             => FALSE
            ),
            // Parameters should be required for grant_type endpoint
            'authorization_code'    => array(
                'code'              => TRUE,
                'redirect_uri'      => TRUE,
                'client_id'         => TRUE,
                'client_secret'     => TRUE,
            ),
            'password'              => array(
                'username'          => TRUE,
                'password'          => TRUE,
                'client_id'         => TRUE,
                'client_secret'     => TRUE,
            ),
            'refresh_token'         => array(
                'refresh_token'     => TRUE,
                'client_id'         => TRUE,
                'client_secret'     => TRUE,
            ),
            // TODO
            'assertion' => array(
                'assertion_type'    => TRUE,
                'assertion'         => TRUE,
                'client_id'         => TRUE,
                'client_secret'     => TRUE
            )
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

        // section-4.1.2.1.  Error Response
        'code_errors'   => array(
            'invalid_request'       => array(
                'error_description' => 'The request is missing a required parameter, includes an
                    unsupported parameter or parameter value, or is otherwise malformed.',
                'error_uri'         => '',
            ),
            'unauthorized_client'   => array(
                'error_description' => 'The client is not authorized to request an authorization
                    code using this method.',
                'error_uri'         => '',
            ),
            'access_denied'         => array(
                'error_description' => 'The resource owner or authorization server denied the request.',
                'error_uri'         => '',
            ),
            'unsupported_response_type' => array(
                'error_description' => 'The authorization server does not support obtaining an
                    authorization code using this method.',
                'error_uri'         => '',
            ),
            'invalid_scope'         => array(
                'error_description' => 'The requested scope is invalid, unknown, or malformed.',
                'error_uri'         => '',
            ),
            'server_error'         => array(
                'error_description' => 'The authorization server encountered an unexpected condition which prevented it from fulfilling the request.',
                'error_uri'         => '',
            ),
            'temporarily_unavailable'=> array(
                'error_description' => 'The authorization server is currently unable to handle the
                                        request due to a temporary overloading or maintenance of the server.',
                'error_uri'         => '',
            )
        ),
        // section-4.2.2.1.  Error Response
        'token_errors'  => array(
            'invalid_request'       => array(
                'error_description' => 'The request is missing a required parameter, includes an
                    unsupported parameter or parameter value, or is otherwise malformed.',
                'error_uri'         => '',
            ),
            'unauthorized_client'   => array(
                'error_description' => 'The client is not authorized to request an access token using this method.',
                'error_uri'         => '',
            ),
            'access_denied'         => array(
                'error_description' => 'The resource owner or authorization server denied the request.',
                'error_uri'         => '',
            ),
            'unsupported_response_type' => array(
                'error_description' => 'The authorization server does not support obtaining an access token using this method.',
                'error_uri'         => '',
            ),
            'invalid_scope'         => array(
                'error_description' => 'The requested scope is invalid, unknown, or malformed.',
                'error_uri'         => '',
            ),
            'server_error'         => array(
                'error_description' => 'The authorization server encountered an unexpected condition which prevented it from fulfilling the request.',
                'error_uri'         => '',
            ),
            'temporarily_unavailable'=> array(
                'error_description' => 'The authorization server is currently unable to handle the
                                        request due to a temporary overloading or maintenance of the server.',
                'error_uri'         => '',
            )
        ),
        // section-5.2.  Error Response
        'access_errors' => array(
            'invalid_request'       => array(
                'error_description' => 'The request is missing a required parameter, includes an
                    unsupported parameter or parameter value, repeats a
                    parameter, includes multiple credentials, utilizes more
                    than one mechanism for authenticating the client, or is
                    otherwise malformed.',
                'error_uri'         => '',
            ),
            'invalid_client'        => array(
                'error_description' => 'Client authentication failed (e.g. unknown client, no
                    client credentials included, multiple client credentials
                    included, or unsupported credentials type).',
                'error_uri'         => '',
            ),
            'invalid_grant'        => array(
                'error_description' => 'The provided authorization grant is invalid, expired,
                    revoked, or does not match the redirection URI used in
                    the authorization request.',
                'error_uri'         => '',
            ),
            'unauthorized_client'   => array(
                'error_description' => 'The authenticated client is not authorized to use this
                    authorization grant type.',
                'error_uri'         => '',
            ),
            'unsupported_grant_type'=> array(
                'error_description' => 'The authorization grant type is not supported by the
                    authorization server.',
                'error_uri'         => '',
            ),
            'invalid_scope'    => array(
                'error_description' => 'The requested scope is invalid, unknown, malformed, or
                    exceeds the previously granted scope.',
                'error_uri'         => '',
            )
        )
    )

); // END OAuth server config
