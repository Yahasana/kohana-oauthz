<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth protect layout for all API controller, which can be accessed through access_token
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Kohana_Controller
 * *
 */
abstract class Oauth2_Controller extends Kohana_Controller {

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
     * methods exclude from OAuth protection
     *
     * @access  protected
     * @var     array    $_exclude
     */
    protected $_exclude = array('xrds');

    /**
     * Verify the request to protected resource.
     * if unauthorized, redirect action to invalid_request
     *
     * @access  public
     * @return  void
     */
    public function before()
    {
        // Exclude actions do NOT need to protect
        if( ! in_array($this->request->action, $this->_exclude))
        {
            $this->_configs = Kohana::config('oauth-server.'.$this->_type);

            try
            {
                // Verify the request method supported in the config settings
                if(empty($this->_configs['request_methods'][Request::$method]))
                {
                    throw new Oauth2_Exception_Access('invalid_request');
                }

                // Process the access token from the request header or body
                $parameter = new Oauth2_Parameter_Access($this->_configs['access_params']);

                $token = new Model_Oauth2_Access;

                // Load the token information from database
                if( ! $access_token = $token->access_token($parameter->client_id, $parameter->oauth_token))
                {
                    throw new Oauth2_Exception_Access('invalid_token');
                }

                $access_token['timestamp'] += $this->_configs['durations']['oauth_token'];

                // Verify the access token
                $parameter->access_token($access_token);
            }
            catch (Oauth2_Exception $e)
            {
                $this->error_code = $e->getMessage();

                // Redirect the action to unauthenticated
                $this->request->action = 'unauthenticated';
            }
        }
    }

    /**
     * Unauthorized response, only be called from internal
     *
     * @access	public
     * @param	string	$error	default [ error='invalid_client' ]
     * @return	void
     * @todo    Add list of error codes
     */
    public function action_unauthenticated()
    {
        $error['error'] = $this->error_code;

        // Get the error description from config settings
        $error += $this->_configs['access_errors'][$error['error']];

        switch($error['error'])
        {
            case 'invalid_request':
                $this->request->status = 400;   #HTTP/1.1 400 Bad Request
                break;
            case 'invalid_token':
                $this->request->status = 401;   #HTTP/1.1 401 Unauthorized
                break;
            case 'insufficient_scope':
                $this->request->status = 403;   #HTTP/1.1 403 Forbidden
                break;
            default:
                $this->request->status = 400;   #HTTP/1.1 400 Bad Request
                break;
        }

        $this->request->headers['WWW-Authenticate'] = 'OAuth2 realm=\'Service\','.http_build_query($error, '', ',');
        $this->request->response = json_encode($error);
    }

    abstract public function action_get();

    abstract public function action_create();

    abstract public function action_update();

    abstract public function action_delete();

    /**
     * OAuth server auto-discovery for user
     *
     * @access	public
     * @return	void
     * @todo    Add list of error codes
     */
    public function action_xrds()
    {
        $this->request->headers['Content-Type'] = 'application/xrds+xml';
        $this->request->response = View::factory('oauth-server-xrds')->render();
    }

} // END Oauth Controller
