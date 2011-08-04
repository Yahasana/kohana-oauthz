<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Authorization server, the authorization flows can be separated into three groups:
 * user delegation flows, direct credentials flows, and autonomous flows.
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * @see         Kohana_Controller
 * *
 */
abstract class Oauthz_Controller extends Kohana_Controller {

    /**
     * Configuration group name
     *
     * @access	protected
     * @var		string	$_type
     */
    protected $_type = 'default';

    /**
     * Configuration settings for OAuth
     *
     * @access  protected
     * @var     mix    $_configs
     */
    protected $_configs = array();

    public function before()
    {
        $this->_configs = Kohana::config('oauth-server.'.$this->_type);
        // TODO refactor this stupid code
        Oauthz_Exception::$errors[$this->_type]['code_errors']      = $this->_configs['code_errors'];
        Oauthz_Exception::$errors[$this->_type]['token_errors']     = $this->_configs['token_errors'];
        Oauthz_Exception::$errors[$this->_type]['access_errors']    = $this->_configs['access_errors'];
    }

    /**
     * The end-user authenticates directly with the authorization server, and grants client access to its protected resources
     *
     * @access  public
     * @return  void
     */
    public function action_authorize()
    {
        $params = Oauthz::parse_query();

        try
        {
            // There is handler  for this response type
            if($response_type = Arr::get($params, 'response_type'))
            {
                $arguments = Arr::get($this->_configs['params'], $response_type, array());

                if($extension = Oauthz_Extension::factory($response_type, $arguments))
                {
                    $extension->expires_in = $this->_configs['durations']['code'];
                    $response = $extension->execute();
                }
            }

            // This response type is unsupported
            if( ! isset($response))
            {
                $exception = new Oauthz_Exception_Authorize('unsupported_response_type');

                $exception->state = Arr::get($params, 'state');

                $exception->redirect_uri = Arr::get($params, 'redirect_uri');

                throw $exception;
            }
        }
        catch (Oauthz_Exception $e)
        {
            $response = $e->getMessage();
        }

        $this->request->status = 302; #HTTP/1.1 302 Found
        $this->request->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $this->request->redirect($response);
    }

    /**
     * Access token handler for the client requests
     *
     * @access    public
     * @return    void
     */
    public function action_token()
    {
        try
        {
            // There is handler for this grant type
            if($grant_type = Oauthz::get('grant_type'))
            {
                $arguments = Arr::get($this->_configs['params'], $grant_type, array());

                if($extension = Oauthz_Extension::factory($grant_type, $arguments))
                {
                    $extension->expires_in = $this->_configs['durations']['oauth_token'];
                    $response = $extension->execute();
                }
            }

            if(isset($response) AND $response instanceOf Oauthz_Token)
            {
                // HTTP/1.1 200 OK
                $this->request->status = 200;
                $this->request->headers['Content-Type'] = $response->format;
            }
            else
            {
                /**
                 * HTTP/1.1 401 (Unauthorized) for "Authorization" request header field
                 * HTTP/1.1 400 Bad Request for other authentication scheme
                 */
                $this->request->status = 400;
                $this->request->headers['Content-Type'] = 'application/json';

                throw new Oauthz_Exception_Token('unsupported_grant_type');
            }
        }
        catch (Oauthz_Exception $e)
        {
            $response = $e->getMessage();
        }
        $this->request->headers['Expires']          = 'Sat, 26 Jul 1997 05:00:00 GMT';
        $this->request->headers['Cache-Control']    = 'no-store, must-revalidate';
        $this->request->response = $response;
    }

} // END Oauthz Server Controller
