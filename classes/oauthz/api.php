<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth protect layout for all API controller, which can be accessed through access_token
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
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
            $config = Kohana::config('oauth-api')->get($this->_type);

            try
            {
                // Verify the request method supported in the config settings
                if(empty($config['methods'][Request::$method]))
                {
                    // get state parameter send from client
                    $params = isset($_POST['state']) ? array('state' => $_POST['state']) : NULL;

                    throw new Oauthz_Exception_Access('invalid_request', $params);
                }

                // Process the access token from the request header or body
                $authorization = Oauthz_Authentication::factory($config['token']);

                // Load the token information from database
                if( ! $client = Model_Oauthz::factory('Token')
                    ->access_token($authorization->client_id(), $authorization->token()))
                {
                    // get state parameter send from client
                    $params = isset($_POST['state']) ? array('state' => $_POST['state']) : NULL;

                    throw new Oauthz_Exception_Access('unauthorized_client', $params);
                }

                // Verify the access token
                $authorization->authenticate($client);
            }
            catch (Oauthz_Exception $e)
            {
                $this->exception = $e;

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
        if($this->exception->error === 'invalid_client')
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
        $this->request->response                    = $this->exception->as_json();
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
