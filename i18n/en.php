<?php defined('SYSPATH') or die('No direct script access.');

return array (
    'Authorization Response'    => 'Authorization Response',
    'Access Token Response'     => 'Access Token Response',
    'Access Protected Resource' => 'Access Protected Resource',

    // Error Response
    'Authorization Errors Response'   => array(
        'invalid_request'       => 'The request is missing a required parameter, includes an
                unsupported parameter or parameter value, or is otherwise malformed.',

        'unauthorized_client'   => 'The client is not authorized to request an authorization
                code using this method.',

        'access_denied'         => 'The resource owner or authorization server denied the request.',

        'unsupported_response_type' => 'The authorization server does not support obtaining an
                authorization code using this method.',

        'invalid_scope'         => 'The requested scope is invalid, unknown, or malformed.',

        'server_error'         => 'The authorization server encountered an unexpected condition which prevented it from fulfilling the request.',

        'temporarily_unavailable'=> 'The authorization server is currently unable to handle the
                                    request due to a temporary overloading or maintenance of the server.'
    ),
    // Error Response
    'Token Errors Response'  => array(
        'invalid_request'       => 'The request is missing a required parameter, includes an
                unsupported parameter or parameter value, or is otherwise malformed.',

        'unauthorized_client'   => 'The client is not authorized to request an access token using this method.',

        'access_denied'         => 'The resource owner or authorization server denied the request.',

        'unsupported_response_type' => 'The authorization server does not support obtaining an access token using this method.',

        'invalid_scope'         => 'The requested scope is invalid, unknown, or malformed.',

        'server_error'         => 'The authorization server encountered an unexpected condition which prevented it from fulfilling the request.',

        'temporarily_unavailable'=> 'The authorization server is currently unable to handle the
                                    request due to a temporary overloading or maintenance of the server.'
    ),
    // Error Response
    'Access Errors Response' => array(
        'invalid_request'       => 'The request is missing a required parameter, includes an
                unsupported parameter or parameter value, repeats a
                parameter, includes multiple credentials, utilizes more
                than one mechanism for authenticating the client, or is
                otherwise malformed.',

        'invalid_client'        => 'Client authentication failed (e.g. unknown client, no
                client credentials included, multiple client credentials
                included, or unsupported credentials type).',

        'invalid_grant'        => 'The provided authorization grant is invalid, expired,
                revoked, or does not match the redirection URI used in
                the authorization request.',

        'unauthorized_client'   => 'The authenticated client is not authorized to use this
                authorization grant type.',

        'unsupported_grant_type'=> 'The authorization grant type is not supported by the
                authorization server.',

        'invalid_scope'    => 'The requested scope is invalid, unknown, malformed, or
                exceeds the previously granted scope.'
    )

);
