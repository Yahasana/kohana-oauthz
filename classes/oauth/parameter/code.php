<?php

class Oauth_Parameter_Code extends Oauth_Parameter {

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
     * Load oauth parameters from GET or POST
     *
     * @access	public
     * @param	string	$flag	default [ FALSE ]
     * @return	void
     */
    public function __construct(array $args)
    {
        $params = array();

        // Check all required parameters should NOT be empty
        foreach($args as $key => $val)
        {
            if($val === TRUE)
            {
                if(isset($_GET[$key]) AND $value = Oauth::urldecode($_GET[$key]))
                {
                    $params[$key] = $value;
                }
                else
                {
                    throw new Oauth_Exception('invalid-request');
                }
            }
        }

        $this->_params      = $params;
        $this->client_id    = $params['client_id'];
        $this->redirect_uri = $params['redirect_uri'];
    }

    public function oauth_token($client)
    {
        $response = new Oauth_Token;

        if(isset($this->_params['state']))
        {
            $response->state = $this->state = $this->_params['state'];
        }

        if($client['redirect_uri'] !== $this->redirect_uri)
        {
            throw new Oauth_Exception('redirect-uri-mismatch');
        }

        if(isset($this->_params['scope']) AND ! isset($client['scope'][$this->_params['scope']]))
        {
            throw new Oauth_Exception('invalid-scope');
        }

        $response->expires_in = $client['expires_in'];

        // Grants Authorization
        $response->code = $client['code'];

        return $response;
    }

} // END Oauth_Parameter_Code
