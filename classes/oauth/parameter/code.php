<?php
/**
 * Oauth parameter handler for authenticate code request
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauth_Parameter
 * *
 */
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
                    throw new Oauth_Exception('invalid_request');
                }
            }
        }

        $this->client_id    = $params['client_id'];
        $this->redirect_uri = $params['redirect_uri'];

        // Remove all required parameters
        unset($params['client_id'], $params['redirect_uri']);

        $this->_params = $params;
    }

    public function oauth_token($client)
    {
        $response = new Oauth_Token;

        if(isset($this->_params['state']))
        {
            $response->state = $this->_params['state'];
        }

        if($client['redirect_uri'] !== $this->redirect_uri)
        {
            throw new Oauth_Exception('redirect_uri_mismatch');
        }

        if(isset($this->_params['scope']) AND ! empty($client['scope']))
        {
            if( ! in_array($this->_params['scope'], explode(' ', $client['scope'])))
                throw new Oauth_Exception('invalid_scope');
        }

        $response->expires_in = $client['expires_in'];

        // Grants Authorization
        $response->code = $client['code'];

        return $response;
    }

} // END Oauth_Parameter_Code
