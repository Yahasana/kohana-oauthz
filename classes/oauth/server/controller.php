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
        $this->oauth = new Model_Oauth;
        Oauth_Exception::$errors[$this->_type]['code_errors'] = $this->_configs['code_errors'];
        Oauth_Exception::$errors[$this->_type]['token_errors'] = $this->_configs['token_errors'];
        Oauth_Exception::$errors[$this->_type]['access_errors'] = $this->_configs['access_errors'];
    }

    /**
     * the end-user authenticates directly with the authorization server, and grants client access to its protected resources
     *
     * @access  public
     * @return  void
     */
    public function action_authorize()
    {
        $this->request->status = 302; #HTTP/1.1 302 Found
        $this->request->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        try
        {
            switch(Oauth::get('response_type'))
            {
                case 'code':
                    $response = $this->code();
                    break;
                case 'token':
                    $response = $this->token();
                    break;
                case 'code_and_token':
                    $response = $this->code_and_token();
                    break;
                default:
                    throw new Oauth_Exception_Authorize('unsupported_response_type');
                    break;
            }
            $this->request->redirect($response);
        }
        catch (Oauth_Exception $e)
        {
            // $error = $e->getMessage();
            // $description = $this->_configs['code_errors'][$error];
            // print_r($description);
            $this->request->redirect($e);
        }
    }

    /**
     * Client Requests Access Token
     *
     * @access    public
     * @return    void
     */
    public function action_token()
    {
        try
        {
            switch(Oauth::get('grant_type'))
            {
                case 'authorization_code':
                    $response = $this->authorization_code();
                    break;
                case 'password':
                    $response = $this->password_credentials();
                    break;
                case 'assertion':
                    $response = $this->assertion();
                    break;
                case 'refresh_token':
                    $response = $this->refresh_token();
                    break;
                case 'none':
                    $response = $this->none();
                    break;
                default:
                    throw new Oauth_Exception_Authorize('unsupported_grant_type');
                    break;
            }

            // HTTP/1.1 200 OK
            $this->request->status = 200;
            $this->request->response = $response;
            $this->request->headers['Content-Type'] = $response->format;
        }
        catch (Oauth_Exception $e)
        {
            /**
             * HTTP/1.1 401 (Unauthorized) for "Authorization" request header field
             * HTTP/1.1 400 Bad Request for other authentication scheme
             */
            $this->request->status = 400;
            $this->request->headers['Content-Type'] = 'application/json';
            $this->request->response = $e->getMessage();
        }
        $this->request->headers['Expires'] = 'Sat, 26 Jul 1997 05:00:00 GMT';
        $this->request->headers['Cache-Control'] = 'no-store, must-revalidate';
    }

    /**
     * Client Requests Authorization by web_server
     *
     * @author    sumh <oalite@gmail.com>
     * @access    protected
     * @return    string    redirect_uri#[code,state]
     * @throw     string    Error Codes: invalid_request, invalid_client, unauthorized_client, invalid_grant
     */
    protected function code()
    {
        $parameter = new Oauth_Parameter_Code($this->_configs['code_params']);

        if($client = $this->oauth->lookup_client($parameter->client_id))
        {
            $client['expires_in'] = $this->_configs['durations']['code'];

            // Populate the code
            $response = $parameter->oauth_token($client);
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

        if($client = $this->oauth->lookup_client($parameter->client_id))
        {
            $client['expires_in'] = $this->_configs['durations']['oauth_token'];

            $parameter->oauth_token($client);
        }
        else
        {
            // Invalid client_id
            $e = new Oauth_Exception_Authorize('invalid_client');
            $e->redirect_uri = $parameter->redirect_uri;
            $e->state = $parameter->state;
            throw $e;
        }

        $response = $parameter->access_token($parameter->client_id);

        return $parameter->redirect_uri.'#'.$response->query();
    }

    protected function code_and_token()
    {
        // TODO
    }

    protected function authorization_code()
    {
        $parameter = new Oauth_Parameter_Webserver($this->_configs['grant_params']['authorization_code']);

        if($client = $this->oauth->lookup_code($parameter->code))
        {
            $response = $parameter->access_token($client);

            $this->oauth->audit_token($response);
        }
        else
        {
            throw new Oauth_Exception_Token('invalid_client');
        }

        return $response;
    }

    protected function password_credentials()
    {
        $parameter = new Oauth_Parameter_Password;

        if($client = $this->oauth->lookup_server($parameter->client_id))
        {
            $response = $parameter->access_token($client);
        }
        else
        {
            throw new Oauth_Exception_Token('invalid_client');
        }

        return $response;
    }

    protected function assertion()
    {
        $parameter = new Oauth_Parameter_Assertion;

        if($client = $this->oauth->lookup_server($parameter->client_id))
        {
            $response = $parameter->access_token($client);
        }
        else
        {
            throw new Oauth_Exception_Token('invalid_client');
        }

        return $response;
    }

    protected function refresh_token()
    {
        $parameter = new Oauth_Parameter_Refresh;

        if($client = $this->oauth->lookup_server($parameter->client_id))
        {
            $response = $parameter->access_token($client);
        }
        else
        {
            throw new Oauth_Exception_Token('invalid_client');
        }

        return $response;
    }

    protected function none()
    {
        $parameter = new Oauth_Parameter_None;

        if($client = $this->oauth->lookup_server($parameter->client_id))
        {
            $response = $parameter->access_token($client);
        }
        else
        {
            throw new Oauth_Exception_Token('invalid_client');
        }

        return $response;
    }

} // END Oauth Server Controller
