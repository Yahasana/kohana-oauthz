<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth protected layout for all API controller
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Kohana_Controller
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
     * methods exclude from OAuth protection
     *
     * @access  protected
     * @var     array    $_exclude
     */
    protected $_exclude = array();

    /**
     * Verify the request to protected resource.
     * if unauthorized, redirect action to invalid_request
     *
     * @access  public
     * @return  void
     */
    public function before()
    {
        if( ! in_array($this->request->action, $this->_exclude))
        {
            $this->_configs = Kohana::config('oauth-server.'.$this->_type);

            $this->oauth = new Model_Oauth;

            try
            {
                if(empty($this->_configs['request_methods'][Request::$method]))
                {
                    throw new Oauth_Exception('invalid_request');
                }

                $parameter = new Oauth_Parameter_Access($this->_configs['access_params']);

                if( ! $client = $this->oauth->lookup_token($parameter->oauth_token))
                {
                    throw new Oauth_Exception('invalid_token');
                }

                $client['timestamp'] += $this->_configs['durations']['oauth_token'];

                $parameter->access_token($client);
            }
            catch (Oauth_Exception $e)
            {
                $this->error_code = $e->getMessage();
                $this->request->action = 'un_authenticated';
            }
        }
    }

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
        $this->request->response = View::factory('oauth-xrds')->render();
    }

    /**
     * Unauthorized response, only be called from internal
     *
     * @access	public
     * @param	string	$error	default [ error='invalid_client' ]
     * @return	void
     * @todo    Add list of error codes
     */
    public function action_un_authenticated()
    {
        $error['error'] = $this->error_code;
        $error += $this->_configs['access_res_errors'][$this->error_code];

        switch($this->error_code)
        {
            case 'invalid_request':
                $this->request->status = 400;   #HTTP/1.1 400 Bad Request
                break;
            case 'invalid_token':
            case 'expired_token':
                $this->request->status = 401;   #HTTP/1.1 401 Unauthorized
                break;
            case 'insufficient_scope':
                $this->request->status = 403;   #HTTP/1.1 403 Forbidden
                break;
            default:
                $this->request->status = 400;   #HTTP/1.1 400 Bad Request
                break;
        }

        $this->request->headers['WWW-Authenticate'] = 'OAuth realm=\'Service\','.http_build_query($error, '', ',');
        $this->request->response = json_encode($error);
    }

    abstract public function action_get();

    abstract public function action_create();

    abstract public function action_update();

    abstract public function action_delete();

} // END Oauth Controller
