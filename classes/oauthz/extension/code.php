<?php
/**
 * Response type is code
 *
 * Oauth parameter handler for authenticate code request
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * @see         Oauthz_Extension
 * *
 */
class Oauthz_Extension_Code extends Oauthz_Extension {

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
                    $exception = new Oauthz_Exception_Authorize('invalid_request');

                    $exception->redirect_uri = isset($params['redirect_uri'])
                        ? $params['redirect_uri']
                        : Oauthz::urldecode($_GET['redirect_uri']);

                    $exception->state = $this->state;

                    throw $exception;
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
    public function execute()
    {
        // Verify the client and generate a code if successes
        if($client = Oauthz_Model::factory('Token')->code($this->client_id))
        {
            $client['expires_in'] = $this->_configs['durations']['code'];
        }
        else
        {
            // Invalid client_id
            $exception = new Oauthz_Exception_Authorize('invalid_client');

            $exception->redirect_uri = $this->redirect_uri;

            $exception->state = $this->state;

            throw $exception;
        }

        $response = new Oauthz_Token;

        if($client['redirect_uri'] !== $this->redirect_uri)
        {
            $e = new Oauthz_Exception_Authorize('redirect_uri_mismatch');

            $e->redirect_uri = $client['redirect_uri'];

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

        return $this->redirect_uri.'?'.$response->as_query();
    }

} // END Oauthz_Extension_Code
