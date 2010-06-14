<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Authorization server, the authorization flows can be separated into three groups:
 * user delegation flows, direct credentials flows, and autonomous flows.
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
abstract class Oauth_Server_Controller extends Kohana_Controller {

    protected $_type = 'default';

    /**
     * Request  settings for OAuth
     *
     * @access  protected
     * @var     mix    $_params
     */
    protected $_params = array();

    /**
     * Data store handler
     *
     * @access  protected
     * @var     string    $oauth
     */
    protected $oauth  = NULL;

    public function before()
    {
        $this->oauth = new Model_Oauth;
    }

    /**********************************Server provider actions for user*******************/

    /**
     * the end-user authenticates directly with the authorization server, and grants client access to its protected resources
     *
     * @access    public
     * @return    void
     */
    public function action_authorize()
    {
        try {
            switch(Oauth::get('type'))
            {
                case 'user_agent':
                    $response = $this->user_agent();
                    break;
                case 'web_server':
                    $response = $this->user_server();
                    break;
                case 'device_code':
                    $response = $this->user_device();
                    $this->request->status = 200; #HTTP/1.1 200 OK
                    $this->request->response = $response;
                    $this->request->headers['Cache-Control'] = 'no-store';
                    $this->request->headers['Content-Type'] = $response->format;
                    return;
                default:
                    throw new Oauth_Exception('incorrect_request_type');
                    break;
            }
            $this->request->response = $response;
            //$this->request->redirect($response);
        }
        catch (Oauth_Exception $e)
        {
            $this->request->status = 400; #HTTP/1.1 400 Bad Request
            $this->request->headers['Content-Type'] = 'application/json';
            $this->request->response = json_encode(array('error' => $e->getMessage()));
        }
    }

    /********************************Server provider actions for consumer*****************/
    /**
     * Client Requests Access Token
     *
     * oauth_token:
     *      The Access Token.
     * oauth_token_secret:
     *      The Token Secret.
     * Additional parameters:
     *      Any additional parameters, as defined by the Service Provider.
     *
     * @access    public
     * @return    void
     */
    public function action_token()
    {
        try {
            switch(Oauth::get('type'))
            {
                case 'web_server':
                    $response = $this->web_server();
                    break;
                case 'refresh_token':
                    $response = $this->reflesh_token();
                    break;
                case 'device_token':
                    $response = $this->device_token();
                case 'username':
                    $response = $this->username();
                    break;
                case 'client_credentials':
                    $response = $this->client_credentials();
                    break;
                case 'assertion':
                    $response = $this->assertion();
                    break;
                default:
                    throw new Oauth_Exception('incorrect_request_type');
                    break;
            }

            $this->request->status = 200; #HTTP/1.1 200 OK
            $this->request->response = $response;
            $this->request->headers['Content-Type'] = $response->format;
        }
        catch (Oauth_Exception $e)
        {
            $this->request->status = 400; #HTTP/1.1 400 Bad Request
            $this->request->headers['Content-Type'] = 'application/json';
            $this->request->response = json_encode(array('error' => $e->getMessage()));
        }
        $this->request->headers['Cache-Control'] = 'no-store';
    }

    /**********************************END actions*****************************************/

    protected function user_agent()
    {
        $parameter = new Oauth_Parameter_Useragent;

        if($client = $this->oauth->lookup_client($parameter->client_id))
        {
            $response = $parameter->oauth_token($client);
        }
        else
        {
            $response = new Oauth_Response(array('format' => 'form'));
            $response->error = 'incorrect_client_credentials';
        }

        if(empty($response->error))
        {
            $response = $this->oauth->access_token($parameter->client_id);
        }

        return $parameter->redirect_uri.'#'.$response;
    }

    /**
     * Client Requests Authorization by web_server
     *
     * @author    sumh <oalite@gmail.com>
     * @access    protected
     * @return    string    redirect_uri#[code,state|error=MUST be set to "redirect_uri_mismatch", "bad_verification_code", "incorrect_client_credentials"]
     */
    protected function user_server()
    {
        $parameter = new Oauth_Parameter_Webserver;

        if($client = $this->oauth->lookup_client($parameter->client_id))
        {
            $response = $parameter->oauth_token($client);
        }
        else
        {
            $response = new Oauth_Response(array('format' => 'form'));
            $response->error = 'incorrect_client_credentials';
        }

        return $parameter->redirect_uri.'#'.$response;
    }

    protected function user_device()
    {
        $parameter = new Oauth_Parameter_Device;

        if($client = $this->oauth->lookup_server($parameter->client_id))
        {
            $response = $parameter->access_token($client);
        }
        else
        {
            $response = new Oauth_Response;
            $response->error = 'incorrect_client_credentials';
        }

        return $response;
    }

    protected function web_server()
    {
        $parameter = new Oauth_Parameter_Webserver(TRUE);

        if($client = $this->oauth->lookup_server($parameter->client_id))
        {
            $response = $parameter->access_token($client);
        }
        else
        {
            $response = new Oauth_Response;
            $response->error = 'incorrect_client_credentials';
        }

        return $response;
    }

    protected function device_token()
    {
        $parameter = new Oauth_Parameter_Device;

        if($client = $this->oauth->lookup_server($parameter->client_id))
        {
            $response = $parameter->access_token($client);
        }
        else
        {
            $response = new Oauth_Response;
            $response->error = 'incorrect_client_credentials';
        }

        return $response;
    }

    protected function username()
    {
        $parameter = new Oauth_Parameter_Username;

        if($client = $this->oauth->lookup_server($parameter->client_id))
        {
            $response = $parameter->access_token($client);
        }
        else
        {
            $response = new Oauth_Response;
            $response->error = 'incorrect_client_credentials';
        }

        return $response;
    }

    protected function client_credentials()
    {
        $parameter = new Oauth_Parameter_Credentials;

        if($client = $this->oauth->lookup_server($parameter->client_id))
        {
            $response = $parameter->access_token($client);
        }
        else
        {
            $response = new Oauth_Response;
            $response->error = 'incorrect_client_credentials';
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
            $response = new Oauth_Response;
            $response->error = 'incorrect_client_credentials';
        }

        return $response;
    }

    protected function reflesh_token()
    {
        $parameter = new Oauth_Parameter_Reflesh;

        if($client = $this->oauth->lookup_server($parameter->client_id))
        {
            $response = $parameter->access_token($client);
        }
        else
        {
            $response = new Oauth_Response;
            $response->error = 'incorrect_client_credentials';
        }

        return $response;
    }

} //END Oauth Server Controller
