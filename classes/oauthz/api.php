<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth protect layout for all API controller, which can be accessed through access_token
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.com
 * @see         Kohana_Controller
 * *
 */
abstract class Oauthz_Api extends Kohana_Controller {

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
    public function __construct(Request $request)
    {
        // Exclude actions do NOT need to protect
        if( ! in_array($request->action, $this->_exclude))
        {
            $this->_configs = Kohana::config('oauth-server.'.$this->_type);

            try
            {
                // Verify the request method supported in the config settings
                if(empty($this->_configs['request_methods'][Request::$method]))
                {
                    throw new Oauthz_Exception_Token('invalid_request');
                }

                // Process the access token from the request header or body
                $type = new Oauthz_Type_Token($this->_configs['access_params']);

                $token = new Model_Oauthz_Token;

                // Load the token information from database
                if( ! $access_token = $token->access_token($type->client_id, $type->oauth_token))
                {
                    throw new Oauthz_Exception_Token('unauthorized_client');
                }

                $access_token['timestamp'] += $this->_configs['durations']['oauth_token'];

                // Verify the access token
                $type->access_token($access_token);
            }
            catch (Oauthz_Exception $e)
            {
                $this->error = $e->getMessage();

                // Redirect the action to unauthenticated
                $request->action = 'unauthenticated';
            }
        }

        parent::__construct($request);
    }

    /**
     * Unauthorized response, only be called from internal
     *
     * @access	public
     * @param	string	$error
     * @return	void
     * @todo    Add list of error codes
     */
    public function action_unauthenticated()
    {
        $error['error'] = $this->error;

        // Get the error description from config settings
        $error += $this->_configs['access_errors'][$error['error']];

        if($error['error'] === 'invalid_client')
        {
            // HTTP/1.1 401 Unauthorized
            $this->request->status = 401;
            // TODO: If the client attempted to authenticate via the "Authorization" request header field
            // the "WWW-Authenticate" response header field matching the authentication scheme used by the client.
            // $this->request->headers['WWW-Authenticate'] = 'OAuth2 realm=\'Service\','.http_build_query($error, '', ',');
        }
        else
        {
            // HTTP/1.1 400 Bad Request
            $this->request->status = 400;
        }

        $this->request->headers['Content-Type']     = 'application/json';
        $this->request->headers['Expires']          = 'Sat, 26 Jul 1997 05:00:00 GMT';
        $this->request->headers['Cache-Control']    = 'no-store, must-revalidate';
        $this->request->response = json_encode($error);
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
        $this->request->response = View::factory('oauth-server-xrds')->render();
    }

} // END Oauthz_Api
