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
        
        // Error info base uri
        'error_uri'  => '/oauth/error/',

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
         *
         * TRUE: the value of this parameter is obtained from request parameters
         * FALSE: this parameter is disabled
         * otherwise: this parameter will be binded to token object
         */
        'params'     => array(
            // Parameters should be required for response_type endpoint
            'code'                  => array(
                'client_id'         => TRUE,
                'redirect_uri'      => TRUE,
                'scope'             => FALSE,
                'state'             => FALSE,
                // authorization code expires time, default is 2 minutes
                'expires_in'        => 120,

                // The follow Parameters are used for access token request
                'token_type'        => 'bearer'
            ),
            // Parameters should be required for grant_type endpoint
            'authorization_code'    => array(
                'code'              => TRUE,
                'redirect_uri'      => TRUE,
                'client_id'         => TRUE,
                'client_secret'     => TRUE,
                // authorization code expires time, default is 2 minutes
                'expires_in'        => 120
            ),
            'token'                 => array(
                'code'              => TRUE,
                'redirect_uri'      => TRUE,
                'client_id'         => TRUE,
                'client_secret'     => TRUE,
                'scope'             => FALSE,
                'state'             => FALSE,
                // token expires time, default is 1 hour
                'expires_in'        => 3600
            ),
            'password'              => array(
                'username'          => TRUE,
                'password'          => TRUE,
                'client_id'         => TRUE,
                'client_secret'     => TRUE,
                // token expires time, default is 1 hour
                'expires_in'        => 3600
            ),
            'refresh_token'         => array(
                'refresh_token'     => TRUE,
                'client_id'         => TRUE,
                'client_secret'     => TRUE,
                // refresh token expires time, default is 1 day
                'expires_in'        => 86400
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
        )
    )

); // END OAuth server config
