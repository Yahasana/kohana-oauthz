<?php
/**
 * Oauth parameter handler for authenticate code request
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * @see         Oauthz_Type
 * *
 */
class Oauthz_Type_Code extends Oauthz_Type {

    /**
     * REQUIRED.  The client identifier as described in Section 2.1.
     *
     * @access	public
     * @var		string	$client_id
     */
    public $client_id;

    /**
     * REQUIRED.  The redirection URI used in the initial request.
     *
     * @access	public
     * @var		string	$redirect_uri
     */
    public $redirect_uri;

    /**
     * REQUIRED if the "state" parameter was present in the client authorization request.
     *
     * @access	public
     * @var		string	$state
     */
    public $state;

    /**
     * Load oauth parameters from GET or POST
     *
     * @access	public
     * @param	string	$flag	default [ FALSE ]
     * @return	void
     * @throw   Oauthz_Exception_Authorize    Error Codes: invalid_request
     */
    public function __construct(array $args)
    {
        $params = array();

        // Parse the "state" paramter
        if(isset($_GET['state']) AND $state = Oauthz::urldecode($_GET['state']))
        {
            $this->state = $state;
            unset($_GET['state']);
        }

        // Check all required parameters should not be empty
        foreach($args as $key => $val)
        {
            if($val === TRUE)
            {
                if(isset($_GET[$key]) AND $value = Oauthz::urldecode($_GET[$key]))
                {
                    $params[$key] = $value;
                }
                else
                {
                    $e = new Oauthz_Exception_Authorize('invalid_request');

                    $e->redirect_uri = isset($params['redirect_uri'])
                        ? $params['redirect_uri']
                        : Oauthz::urldecode($_GET['redirect_uri']);

                    $e->state = $this->state;

                    throw $e;
                }
            }
        }

        $this->client_id    = $params['client_id'];
        $this->redirect_uri = $params['redirect_uri'];

        // Remove all required parameters
        unset($params['client_id'], $params['redirect_uri']);

        $this->_params = $params;
    }

    /**
     * Populate the oauth token from the request info and client info store in the server
     *
     * @access	public
     * @param	array	$client
     * @return	Oauthz_Token
     * @throw   Oauthz_Exception_Authorize    Error Codes: invalid_scope, redirect_uri_mismatch
     */
    public function oauth_token($client)
    {
        $response = new Oauthz_Token;

        if($client['redirect_uri'] !== $this->redirect_uri)
        {
            $e = new Oauthz_Exception_Authorize('redirect_uri_mismatch');

            $e->redirect_uri = $this->redirect_uri;

            $e->state = $this->state;

            throw $e;
        }

        if( ! empty($this->_params['scope']) AND ! empty($client['scope']))
        {
            if( ! in_array($this->_params['scope'], explode(' ', $client['scope'])))
            {
                $e = new Oauthz_Exception_Authorize('invalid_scope');

                $e->redirect_uri = $this->redirect_uri;

                $e->state = $this->state;

                throw $e;
            }
        }

        $response->expires_in = $client['expires_in'];

        // Grants Authorization
        $response->code = $client['code'];

        return $response;
    }

} // END Oauthz_Type_Code
