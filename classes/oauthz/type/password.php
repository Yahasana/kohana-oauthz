<?php
/**
 * Oauth parameter handler for password credentials flow
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * @see         Oauthz_Type
 * *
 */
class Oauthz_Type_Password extends Oauthz_Type {

    /**
     * client_id
     *      REQUIRED.  The client identifier
     */
    public $client_id;

    /**
     * client_secret
     *      REQUIRED.  The client secret
     *      OPTIONAL if no client secret was issued.
     */
    public $client_secret;

    /**
     * username
     *       REQUIRED.  The end-user's username.
     */
    public $username;

    /**
     * password
     *      REQUIRED.  The end-user's password.
     */
    public $password;

    /**
     * redirect_uri
     *      REQUIRED unless a redirection URI has been established between
     *      the client and authorization server via other means.  An
     *      absolute URI to which the authorization server will redirect
     *      the user-agent to when the end-user authorization step is
     *      completed.  The authorization server SHOULD require the client
     *      to pre-register their redirection URI.  Authorization servers
     *      MAY restrict the redirection URI to not include a query
     *      component as defined by [RFC3986] section 3.
     */
    public $redirect_uri;

    /**
     * state
     *      OPTIONAL.  An opaque value used by the client to maintain state
     *      between the request and callback.  The authorization server
     *      includes this value when redirecting the user-agent back to the
     *      client.
     *
     * scope
     *      OPTIONAL.  The scope of the access request expressed as a list
     *      of space-delimited strings.  The value of the "scope" parameter
     *      is defined by the authorization server.  If the value contains
     *      multiple space-delimited strings, their order does not matter,
     *      and each string adds an additional access range to the
     *      requested scope.
     *
     * @access  public
     * @return  void
     * @throw   Oauthz_Exception_Token    Error Codes: invalid_request
     */
    public function __construct($args = NULL)
    {
        isset($_SERVER['CONTENT_TYPE']) OR $_SERVER['CONTENT_TYPE'] = getenv('CONTENT_TYPE');

        // oauth_token already send in authorization header or the encrypt Content-Type is not single-part
        if(empty($_POST) OR stripos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') === FALSE)
        {
            throw new Oauthz_Exception_Token('invalid_request');
        }
        else
        {
            // TODO move this request data detect into authorization handler
            if(isset($_SERVER['PHP_AUTH_USER']) AND isset($_SERVER['PHP_AUTH_PW']))
            {
                $_POST += array('username' => $_SERVER['PHP_AUTH_USER'], 'password' => $_SERVER['PHP_AUTH_PW']);
            }
            // TODO Digest HTTP authentication
            //else if( ! empty($_SERVER['PHP_AUTH_DIGEST']) AND $digest = parent::parse_digest($_SERVER['PHP_AUTH_DIGEST']))
            //{
            //    $_POST += array('username' => $digest['username'], 'password' => $digest['']);
            //}

            // Check all required parameters from form-encoded body
            foreach($args as $key => $val)
            {
                if($val === TRUE)
                {
                    if(isset($_POST[$key]) AND $value = Oauthz::urldecode($_POST[$key]))
                    {
                        $params[$key] = $value;
                    }
                    else
                    {
                        throw new Oauthz_Exception_Token('invalid_request');
                    }
                }
            }
        }

        if(empty($params))
        {
            throw new Oauthz_Exception_Token('invalid_request');
        }

        $this->username = $params['username'];
        $this->password = $params['password'];
        $this->client_id = $params['client_id'];
        $this->client_secret = $params['client_secret'];
        $this->redirect_uri = $params['redirect_uri'];

        $this->_params = $params;
    }

    /**
     * Populate the access token thu the request info and client info stored in the server
     *
     * @access	public
     * @param	array	$client
     * @return	Oauthz_Token
     * @throw   Oauthz_Exception_Authorize    Error Codes: invalid_request, invalid_scope
     */
    public function access_token($client)
    {
        $response = new Oauthz_Token;

        isset($this->_params['state']) AND $response->state = $this->state;

        if($client['client_secret'] !== sha1($this->client_secret)
            OR $client['username'] !== $this->username
            OR $client['password'] !== sha1($this->password)
            OR $client['redirect_uri'] !== $this->redirect_uri)
        {
            throw new Oauthz_Exception_Token('invalid_request');
        }

        if(isset($this->_params['scope']) AND ! empty($client['scope']))
        {
            if( ! in_array($this->_params['scope'], explode(' ', $client['scope'])))
                throw new Oauthz_Exception_Token('invalid_scope');
        }

        // Grants Authorization
        $response->expires_in = 3600;
        // TODO configurable token type
        $response->token_type = 'BEARER';
        $response->access_token = $client['access_token'];
        $response->refresh_token = $client['refresh_token'];

        return $response;
    }

} // END Oauthz_Type_Password
