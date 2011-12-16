<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Configuration settings for restful API
 *
 * TRUE - mandatory/enable, FALSE - option/disable
 */
return array(

    'default' => array(

        'formats'   => array(
            'json'      => FALSE,   // 'application/json'
            'xml'       => FALSE,   // 'application/xml'
            'form'      => FALSE,   // 'text/plain'
            'html'      => FALSE,   // 'text/html'
            'csv'       => FALSE,   // 'application/csv'
            'php'       => FALSE,   // 'text/plain'
            'serialize' => FALSE    // 'application/vnd.php.serialized'
        ),

        'methods'   => array(
            'HEAD'      => TRUE,
            'GET'       => TRUE,
            'POST'      => TRUE,
            'PUT'       => TRUE,
            'DELETE'    => TRUE
        ),

        /**
         * Parameters should be required when access protected resource
         * cryptographic token or bear token
         */
        'token'    => array(
            'access_token'  => '',
            'token_type'    => '',
            'refresh_token' => '',
            'scope'         => '',
        ),

        'max_requests'  => array(
            500,        // common client
            1000,       // first class client
            1500,       // vip client
        ),

    )

); // END OAuth API config
