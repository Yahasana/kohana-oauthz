<?php defined('SYSPATH') or die('No direct script access.');
/**
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

    /**
     * Config group name
     *
     * @access	protected
     * @var		string	$_type
     */
    protected $_type = 'default';

    /**
     * Data store handler
     *
     * @access  protected
     * @var     string    $oauth
     */
    protected $oauth;

    /**
     * Request token for OAuth
     *
     * @access  protected
     * @var     mix    $token
     */
    protected $token;

    /**
     * Server settings for OAuth
     *
     * @access  protected
     * @var     mix    $_configs
     */
    protected $_configs;


    /**
     * Verify the request to protected resource.
     * if unauthorized, redirect action to invalid_request
     *
     * @access  public
     * @return  void
     */
    public function before()
    {
        $this->_configs = Kohana::config('oauth_server.'.$this->_type);

        $this->oauth = new Model_Oauth;

        try
        {
            if(empty($this->_configs['request_methods'][Request::$method]))
            {
                throw new Oauth_Exception('invalid-request');
            }

            $parameter = new Oauth_Parameter_Access($this->_configs['access_params']);

            if( ! $client = $this->oauth->lookup_token($parameter->oauth_token))
            {
                throw new Oauth_Exception('invalid-token');
            }

            $client['timestamp'] += $this->_configs['durations']['oauth_token'];

            $parameter->access_token($client);
        }
        catch (Oauth_Exception $e)
        {
            $this->errors = $e->getMessage();
            $this->request->action = 'invalid_credentials';
        }
    }

    /**********************************Server OAuth Discovery for user*******************/

    public function action_xrds()
    {
        $this->request->headers['Content-Type'] = 'application/xrds+xml';
        $this->request->response = View::factory('v_oauth_xrds')->render();
    }

    /**********************************END actions*****************************************/

    /**
     * Unauthorized response
     *
     * @access	public
     * @param	string	$error	default [ error='invalid_client_credentials' ]
     * @return	void
     * @todo    Add list of error codes
     */
    public function action_invalid_credentials()
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

        switch($this->errors)
        {
            case 'invalid-request':
                $this->request->status = 400;   #HTTP/1.1 400 Bad Request
                break;
            case 'invalid-token':
            case 'expired-token':
                $this->request->status = 401;   #HTTP/1.1 401 Unauthorized
                break;
            case 'insufficient-scope':
                $this->request->status = 403;   #HTTP/1.1 403 Forbidden
                break;
            default:
                $this->request->status = 400;   #HTTP/1.1 400 Bad Request
                break;
        }

        $this->request->headers['WWW-Authenticate'] = 'Token realm=\'Service\','.$error;
        $this->request->response = $error;
    }

} // END Oauth Controller
