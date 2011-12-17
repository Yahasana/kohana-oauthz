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
        $this->_configs = Kohana::config('oauth-server')->get($this->_type);
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
                    $response = $extension->execute();
                }
            }

            // This response type is unsupported
            if( ! isset($response))
            {
                $params = isset($params['state']) ? array('state' => $params['state']) : NULL;

                throw new Oauthz_Exception_Authorize('unsupported_response_type', $params);
            }
        }
        catch (Oauthz_Exception $e)
        {
            $response = $e->as_query();
        }

        // HTTP/1.1 302 Found
        $this->request->status = 302;
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

                $params = Oauthz::get('state') ? array('state' => Oauthz::get('state')) : NULL;

                throw new Oauthz_Exception_Token('unsupported_grant_type', $params);
            }
        }
        catch (Oauthz_Exception $e)
        {
            $this->request->headers['Content-Type'] = 'application/json';

            $response = $e->as_json();
        }

        $this->request->headers['Expires']          = 'Sat, 26 Jul 1997 05:00:00 GMT';
        $this->request->headers['Cache-Control']    = 'no-store, must-revalidate';
        $this->request->response = $response;
    }

} // END Oauthz Server Controller
