<?php
/**
 * Oauth parameter handler for refresh token flow
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauth_Parameter
 * *
 */
class Oauth2_Parameter_Refresh extends Oauth2_Parameter {

    /**
     * client_id
     *      REQUIRED.  The client identifier as described in Section 2.1.
     */
    public $client_id;

    /**
     * client_secret
     *      REQUIRED if the client was issued a secret.  The client secret.
     */
    public $client_secret;

    /**
     * refresh_token
     *      REQUIRED.  The refresh token associated with the access token to be refreshed.
     */
    public $refresh_token;

    /**
     * format
     *      OPTIONAL.  The response format requested by the client.  Value
     *      MUST be one of "json", "xml", or "form".
     */

    public function __construct($args = NULL)
    {
        $params = Oauth2::parse_query();
        $this->client_id = Arr::get($params, 'client_id');
        $this->client_secret = Arr::get($params, 'client_secret');
        $this->refresh_token = Arr::get($params, 'refresh_token');

        // OPTIONAL.  An opaque value used by the client to maintain state between the request and callback.
        if(NULL !== $state = Arr::get($params, 'state'))
            $this->state = $state;

        if(NULL !== $format = Arr::get($params, 'format'))
            $this->format = $format;

        if(empty($this->client_id) OR empty($this->client_secret) OR empty($this->refresh_token))
        {
            throw new Oauth2_Exception_Token('invalid_request');
        }
    }

    public function oauth_token($client)
    {
        $response = new Oauth2_Token;

        if(property_exists($this, 'state'))
        {
            $response->state = $this->state;
        }

        if(property_exists($this, 'format'))
        {
            $response->format = $this->format;
        }

        if($client['client_secret'] !== sha1($this->client_secret))
        {
            $response->error = 'unauthorized_client';
            return $response;
        }

        if($client['refresh_token'] !== $this->refresh_token)
        {
            $response->error = 'invalid_token';
            return $response;
        }

        if($client['timestamp'] + 300 < $_SERVER['REQUEST_TIME'])
        {
            $response->error = 'invalid_grant';
            return $response;
        }

        // Grants Authorization
        $response->expires_in = 3000;
        $response->access_token = $client['access_token'];

        return $response;
    }

} // END Oauth_Parameter_Refresh
