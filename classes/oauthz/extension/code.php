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

    public $expires_in;

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
        // Parse the "state" paramter
        if(isset($_GET['state']))
        {
            $this->state['state'] = rawurldecode($_GET['state']);

            unset($args['state']);
        }

        // Check all required parameters should not be empty
        foreach($args as $key => $val)
        {
            if($val === TRUE)
            {
                if(isset($_GET[$key]) AND $value = rawurldecode($_GET[$key]))
                {
                    $this->$key = $value;
                }
                else
                {
                    throw new Oauthz_Exception_Authorize('invalid_request', $this->state);
                }
            }
            elseif($val !== FALSE)
            {
                $this->$key = $val;
            }
        }
    }

    /**
     * Populate the oauth token from the request info and client info store in the server
     *
     * @access	public
     * @param	array	$client
     * @return	Oauthz_Token
     * @throw   Oauthz_Exception_Authorize    Error Codes: invalid_scope, unauthorized_client
     */
    public function execute()
    {
        // Verify the client and generate a code if successes
        if($client = Model_Oauthz::factory('Token')
            ->code($this->client_id, $this->token_type, $this->expires_in))
        {
            // audit
        }
        else
        {
            // Invalid client_id
            throw new Oauthz_Exception_Authorize('unauthorized_client', $this->state);
        }

        if($client['redirect_uri'] !== $this->redirect_uri)
        {
            throw new Oauthz_Exception_Authorize('unauthorized_client', $this->state);
        }

        if( ! empty($this->scope) AND ! empty($client['scope']))
        {
            if( ! in_array($this->scope, explode(' ', $client['scope'])))
            {
                // Redirect to client uri
                $params              = $this->state;
                $params['error_uri'] = $this->redirect_uri;

                throw new Oauthz_Exception_Authorize('invalid_scope', $params);
            }
        }

        // Grants Authorization
        $token = new Oauthz_Token;

        $token->code         = $client['code'];
        $token->token_type   = $client['token_type'];
        $token->expires_in   = $client['expires_in'];

        isset($this->state['state']) AND $token->state = $this->state['state'];

        return $this->redirect_uri.'?'.$token->as_query();
    }

} // END Oauthz_Extension_Code
