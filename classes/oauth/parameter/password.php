<?php
/**
 * Oauth parameter handler for password flow
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauth_Parameter
 * *
 */
class Oauth_Parameter_Password extends Oauth_Parameter {

    /**
     * client_id
     *     REQUIRED.  The client identifier as described in Section 2.1.
     */
    public $client_id;

    /**
     * client_secret
     *     REQUIRED.  The client secret as described in Section 2.1.
     */
    public $client_secret;

    public function __construct(array $args = NULL)
    {
        $params = array();
        /**
         * Load oauth_token from form-encoded body
         */
        if( ! isset($_POST['client_id']))
        {
            throw new Oauth_Exception('invalid_request');
        }

        isset($_SERVER['CONTENT_TYPE']) OR $_SERVER['CONTENT_TYPE'] = getenv('CONTENT_TYPE');

        // oauth_token already send in authorization header or the encrypt Content-Type is not single-part
        if(stripos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') === FALSE)
        {
            throw new Oauth_Exception('invalid_request');
        }

        // Check all required parameters should NOT be empty
        foreach($args as $key => $val)
        {
            if($val === TRUE)
            {
                if(isset($_POST[$key]) AND $value = Oauth::urldecode($_POST[$key]))
                {
                    $params[$key] = $value;
                }
                elseif($key !== 'client_secret')
                {
                    throw new Oauth_Exception('invalid_request');
                }
            }
        }

        // Load oauth token from authorization header
        if( ! isset($params['client_secret']))
        {
            isset($_SERVER['HTTP_AUTHORIZATION']) OR $_SERVER['HTTP_AUTHORIZATION'] = getenv('HTTP_AUTHORIZATION');

            if (substr($_SERVER['HTTP_AUTHORIZATION'], 0, 5) === 'Basic ')
            {
                $params['client_secret'] = base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6));
            }

            if(empty($params['client_secret']))
            {
                throw new Oauth_Exception('invalid_request');
            }
        }

        $this->_params = $params;
    }

    public function oauth_token($client)
    {
        $response = new Oauth_Token;

        if(property_exists($this, 'format'))
        {
            $response->format = $this->format;
        }

        if($client['client_secret'] !== sha1($this->_params['client_secret']))
        {
            throw new Oauth_Exception('unauthorized_client');
        }

        if(isset($this->_params['scope']) AND ! isset($client['scope'][$this->_params['scope']]))
        {
            throw new Oauth_Exception('invalid_scope');
        }

        // Grants Authorization
        $response->expires_in = $client['expires_in'];
        $response->access_token = $client['access_token'];
        $response->refresh_token = $client['refresh_token'];

        return $response;
    }

} // END Oauth_Parameter_Password
