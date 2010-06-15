<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'default'    => array(
        'client_request' => array(
            'type'    => TRUE,
            'client_id'    => TRUE,
            /**
             The redirection URI MUST NOT include a query component
            */
            'redirect_uri'    => TRUE,
            /**
             OPTIONAL.  An opaque value used by the client to maintain state
             between the request and callback.  The authorization server
             includes this value when redirecting the user-agent back to the client.
            */
            'state'    => FALSE,
            /**
             The parameter value must be set to "true" or
             "false".  If set to "true", the authorization server MUST NOT
             prompt the end-user to authenticate or approve access.
            */
            'immediate'    => FALSE,
            'secret_type'    => FALSE
        ),

        'formats' => array(
            'json'    => TRUE,
            'xml'    => TRUE,
            'form'    => FALSE
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
            'token_secret'  => '',
            'scope'         => '',
        ),

        'request_methods'   => array(
            'HEAD'      => TRUE,
            'GET'       => TRUE,
            'POST'      => TRUE,
            'PUT'       => TRUE,
            'DELETE'    => TRUE
        ),

        // cryptographic token or bear token
        'request_params'    => array(
            'oauth_token'   => TRUE,
            'nonce'         => FALSE,
            'timestamp'     => FALSE,
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
            //
        ),

        'max_requests'  => array(
            500,        // common client
            1000,       // first class client
            1500,       // vip client
        ),

        'duration'      => 300 // token expires time, default is five minutes
    )
);