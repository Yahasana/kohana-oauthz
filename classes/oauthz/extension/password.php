<?php
/**
 * Grant type is password
 *
 * Oauth parameter handler for password credentials flow
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * @see         Oauthz_Extension
 * *
 */
class Oauthz_Extension_Password extends Oauthz_Extension {

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

        // Parse the "state" paramter
        if(isset($_POST['state']))
        {
            if($state = rawurldecode($_POST['state']))
                $this->state['state'] = $state;

            unset($args['state']);
        }

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
                    if(isset($_POST[$key]) AND $value = rawurldecode($_POST[$key]))
                    {
                        $this->$key = $value;
                    }
                    else
                    {
                        throw new Oauthz_Exception_Token('invalid_request', $this->state);
                    }
                }
            }
        }
    }

    /**
     * Populate the access token thu the request info and client info stored in the server
     *
     * @access	public
     * @param	array	$client
     * @return	Oauthz_Token
     * @throw   Oauthz_Exception_Token    Error Codes: unauthorized_client, invalid_request, invalid_scope
     */
    public function execute()
    {
        if($client = Model_Oauthz::factory('Client')->lookup($this->client_id))
        {
            // Audit
        }
        else
        {
            // Invalid client_id
            throw new Oauthz_Exception_Token('unauthorized_client', $this->state);
        }

        // TODO password should be hashed with much more stronger method
        if($client['client_secret'] !== sha1($this->client_secret)
            OR $client['username'] !== $this->username
            OR $client['password'] !== sha1($this->password)
            OR $client['redirect_uri'] !== $this->redirect_uri)
        {
            throw new Oauthz_Exception_Token('unauthorized_client', $this->state);
        }

        if(isset($this->scope) AND ! empty($client['scope'])
            AND ! in_array($this->scope, explode(' ', $client['scope'])))
        {
            throw new Oauthz_Exception_Token('invalid_scope', $this->state);
        }

        if($client['expires_in'] < $_SERVER['REQUEST_TIME'])
        {
            $params              = $this->state;
            $params['error_uri'] = $this->redirect_uri;

            throw new Oauthz_Exception_Access('invalid_grant', $params);
        }

        $token = new Oauthz_Token;

        // TODO: issue "mac" OAuth Access Token Type
        $token->token_type       = $client['token_type'];
        $token->access_token     = $client['access_token'];
        $token->refresh_token    = $client['refresh_token'];

        // merge other token properties, e.g. {"mac_key":"adijq39jdlaska9asud","mac_algorithm":"hmac-sha-256"}
        if($client['options'] AND $option = json_decode($client['options'], TRUE))
        {
            foreach($option as $key => $val)
            {
                $token->$key = $val;
            }
        }

        isset($this->state['state']) AND $token->state = $this->state['state'];

        return $token;
    }

} // END Oauthz_Extension_Password
