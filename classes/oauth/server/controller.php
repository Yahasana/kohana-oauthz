<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Authorization server, the authorization flows can be separated into three groups:
 * user delegation flows, direct credentials flows, and autonomous flows.
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Kohana_Controller
 * *
 */
abstract class Oauth_Server_Controller extends Kohana_Controller {

    protected $_type = 'default';

    /**
     * Configuration  settings for OAuth
     *
     * @access  protected
     * @var     mix    $_configs
     */
    protected $_configs = array();

    /**
     * Data store handler
     *
     * @access  protected
     * @var     string    $oauth
     */
    protected $oauth  = NULL;

    public function before()
    {
        $this->_configs = Kohana::config('oauth-server.'.$this->_type);
        Oauth_Exception::$errors[$this->_type]['code_errors'] = $this->_configs['code_errors'];
        Oauth_Exception::$errors[$this->_type]['token_errors'] = $this->_configs['token_errors'];
        Oauth_Exception::$errors[$this->_type]['access_errors'] = $this->_configs['access_errors'];
    }

    #region authorization_code, oauth_token and access_token request handuler

    /**
     * the end-user authenticates directly with the authorization server, and grants client access to its protected resources
     *
     * @access  public
     * @return  void
     */
    public function action_authorize()
    {
        $response_type = Oauth::get('response_type');
        try
        {
            if(method_exists($this, $response_type))
            {
                $response = $this->$response_type();
            }
            else
            {
                $params = Oauth::parse_query();

                $e = new Oauth_Exception_Authorize('unsupported_response_type');

                $e->state = Arr::get($params, 'state');

                $e->redirect_uri = Arr::get($params, 'redirect_uri');

                throw $e;
            }
        }
        catch (Oauth_Exception $e)
        {
            $response = (string) $e;
        }

        $this->request->status = 302; #HTTP/1.1 302 Found
        $this->request->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $this->request->redirect($response);
    }

    /**
     * Client Requests Access Token
     *
     * @access    public
     * @return    void
     */
    public function action_token()
    {
        $grant_type = Oauth::get('grant_type');
        try
        {
            if(method_exists($this, $grant_type))
            {
                $response = $this->$grant_type();
            }
            else
            {
                // TODO, if is an absolute URI identifying an assertion format supported by the authorization server
                //if(Oauth::is_assertion($grant_type))
                //{
                //    $response = $this->assertion();
                //    break;
                //}
                throw new Oauth_Exception_Token('unsupported_grant_type');
            }

            // HTTP/1.1 200 OK
            $this->request->status = 200;
            $this->request->headers['Content-Type'] = $response->format;
        }
        catch (Oauth_Exception $e)
        {
            $response = $e->getMessage();
            /**
             * HTTP/1.1 401 (Unauthorized) for "Authorization" request header field
             * HTTP/1.1 400 Bad Request for other authentication scheme
             */
            $this->request->status = 400;
            $this->request->headers['Content-Type'] = 'application/json';
        }
        $this->request->headers['Expires']          = 'Sat, 26 Jul 1997 05:00:00 GMT';
        $this->request->headers['Cache-Control']    = 'no-store, must-revalidate';
        $this->request->response = $response;
    }

    #endregion

    #region get authorization code and token

    /**
     * Client Requests Authorization by web_server
     *
     * @access    protected
     * @return    string    redirect_uri#[code,state]
     * @throw     string    Error Codes: invalid_request, invalid_client, unauthorized_client, invalid_grant
     */
    protected function code()
    {
        $parameter = new Oauth_Parameter_Code($this->_configs['code_params']);

        $token = new Model_Oauth_Token;

        if($code_token = $token->code($parameter->client_id))
        {
            $code_token['expires_in'] = $this->_configs['durations']['code'];

            // Populate the code
            $response = $parameter->oauth_token($code_token);
        }
        else
        {
            // Invalid client_id
            $e = new Oauth_Exception_Authorize('invalid_client');

            $e->redirect_uri = $parameter->redirect_uri;

            $e->state = $parameter->state;

            throw $e;
        }

        return $parameter->redirect_uri.'?'.$response->query();
    }

    protected function token()
    {
        $params = $this->_configs['grant_params']['authorization_code'] + $this->_configs['code_params'];

        $parameter = new Oauth_Parameter_Token($params);

        $token = new Model_Oauth_Token;

        if($access_token = $token->access_token($parameter->client_id, $parameter->code))
        {
            $oauth_token['expires_in'] = $this->_configs['durations']['oauth_token'];

            $response = $parameter->access_token($access_token);
        }
        else
        {
            // Invalid client_id
            $e = new Oauth_Exception_Authorize('invalid_client');

            $e->redirect_uri = $parameter->redirect_uri;

            $e->state = $parameter->state;

            throw $e;
        }

        return $parameter->redirect_uri.'#'.$response->query();
    }

    protected function code_and_token()
    {
        // TODO
    }

    #endregion

    #region get access token

    /**
     * Web-server flow
     *
     * @access	protected
     * @return	array
     * @throw   Oauth_Exception_Token invalid_client
     */
    protected function authorization_code()
    {
        $parameter = new Oauth_Parameter_Webserver($this->_configs['grant_params']['authorization_code']);

        $token = new Model_Oauth_Token;

        if($oauth_token = $token->oauth_token($parameter->client_id, $parameter->code))
        {
            //$this->oauth->audit_token($response);

            $response = $parameter->access_token($oauth_token);
        }
        else
        {
            throw new Oauth_Exception_Token('invalid_client');
        }

        return $response;
    }

    // TODO
    protected function password()
    {
        $parameter = new Oauth_Parameter_Autonomous;

        $server = new Model_Oauth_Server;

        if($client = $server->lookup($parameter->client_id))
        {
            $response = $parameter->access_token($client);
        }
        else
        {
            throw new Oauth_Exception_Token('invalid_client');
        }

        return $response;
    }

    // TODO
    protected function assertion()
    {
        $parameter = new Oauth_Parameter_Assertion;

        $token = new Model_Oauth_Token;

        if($client = $token->assertion($parameter->client_id))
        {
            $response = $parameter->access_token($client);
        }
        else
        {
            throw new Oauth_Exception_Token('invalid_client');
        }

        return $response;
    }

    // TODO
    protected function refresh_token()
    {
        $parameter = new Oauth_Parameter_Refresh;

        $token = new Model_Oauth_Token;

        if($refresh_token = $token->refresh_token($parameter->client_id))
        {
            $response = $parameter->access_token($refresh_token);
        }
        else
        {
            throw new Oauth_Exception_Token('invalid_client');
        }

        return $response;
    }

    // TODO
    protected function client_credentials()
    {
        $parameter = new Oauth_Parameter_None;

        $server = new Model_Oauth_Server;

        if($client = $server->lookup($parameter->client_id))
        {
            $response = $parameter->access_token($client);
        }
        else
        {
            throw new Oauth_Exception_Token('invalid_client');
        }

        return $response;
    }

    #endregion

} // END Oauth Server Controller
