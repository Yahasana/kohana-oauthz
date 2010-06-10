<?php defined('SYSPATH') or die('No direct script access.');
/**
 *
 * 5.  Accessing a Protected Resource . . . . . . . . . . . . . . . . 38
     5.1.  The Authorization Request Header . . . . . . . . . . . . . 38
     5.2.  Bearer Token Requests  . . . . . . . . . . . . . . . . . . 40
       5.2.1.  URI Query Parameter  . . . . . . . . . . . . . . . . . 40
       5.2.2.  Form-Encoded Body Parameter  . . . . . . . . . . . . . 41
     5.3.  Cryptographic Tokens Requests  . . . . . . . . . . . . . . 42
       5.3.1.  The 'hmac-sha256' Algorithm  . . . . . . . . . . . . . 42
   6.  Identifying a Protected Resource . . . . . . . . . . . . . . . 45
     6.1.  The WWW-Authenticate Response Header . . . . . . . . . . . 45
       6.1.1.  The 'realm' Attribute  . . . . . . . . . . . . . . . . 46
       6.1.2.  The 'authorization-uri' Attribute  . . . . . . . . . . 46
       6.1.3.  The 'algorithms' Attribute . . . . . . . . . . . . . . 46
       6.1.4.  The 'error' Attribute  . . . . . . . . . . . . . . . . 46
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2009 OALite team
 * @license     http://www.oalite.com/license.txt
 * @version     $id$
 * @link        http://www.oalite.com
 * @see         Kohana_Controller
 * @since       Available since Release 1.0
 * *
 */
abstract class Oauth_Controller extends Kohana_Controller {

    protected $_type = 'default';

    /**
     * Request  settings for OAuth
     *
     * @access  protected
     * @var     mix    $_params
     */
    protected $_params;

    /**
     * Server  settings for OAuth
     *
     * @access  protected
     * @var     mix    $_configs
     */
    protected $_configs;

    /**
     * Data store handler
     *
     * @access  protected
     * @var     string    $oauth
     */
    protected $oauth  = NULL;

    /**
     * Verify the request to protected resource.
     * if unauthorized, redirect action to invalid_request
     *
     * @access  public
     * @return  void
     */
    public function before()
    {
        $this->_configs = Kohana::config('oauth_server')->{$this->_type};

        $this->oauth = new Model_Oauth;

        try {

            if(empty($this->_configs['request_methods'][Request::$method]))
            {
                throw new Oauth_Exception('invalid_request_method');
            }

            $params = new Oauth_Parameter_Token($this->oauth);

            foreach($this->_configs['request_params'] as $key => $val)
            {
                if($val === TRUE)
                {
                    $params->$key = $val;
                }
            }

            if( ! $client = $this->oauth->lookup_token($params->oauth_token))
            {
                throw new Oauth_Exception('invalid_request_token');
            }

            $params->access_token_check($client);
        }
        catch (Oauth_Exception $e)
        {
            $this->errors = $e->getMessage();
            $this->request->action = 'invalid_request';
        }
    }

    /**********************************Server OAuth Discovery for user*******************/

    public function action_xrds()
    {
        $this->request->headers['Content-Type'] = 'application/xrds+xml';
        $this->request->response = View::factory('v_oauth_xrds');
    }

    /**********************************END actions*****************************************/

    /**
     * Unauthorized response
     *
     * @access	public
     * @param	string	$error	default [ error='incorrect_client_credentials' ]
     * @return	void
     * @todo    Add list of error codes
     */
    public function action_invalid_request()
    {
        $error = 'error=\''.$this->errors.'\'';

        //space-delimited list of the cryptographic algorithms supported by the resource server
        $challenge = '';
        foreach($this->_configs['secret_types'] as $key => $val)
        {
            if($val === TRUE)
            {
                $challenge .= $key.' ';
            }
        }

        if($challenge !== '')
        {
            $error .= ',algorithms=\''.rtrim($challenge).'\'';
        }

        //space-delimited list of URIs (relative or absolute)
        $challenge = '';
        foreach($this->_configs['scopes'] as $key => $val)
        {
            if($val === TRUE)
            {
                $challenge .= $key.' ';
            }
        }

        if($challenge !== '')
        {
            $error .= ',scope=\''.rtrim($challenge).'\'';
        }

        $this->request->status = 401;   #HTTP/1.1 401 Unauthorized
        $this->request->headers['WWW-Authenticate'] = 'Token realm=\'Service\','.$error;
        $this->request->response = NULL;
    }

} //END Oauth Controller
