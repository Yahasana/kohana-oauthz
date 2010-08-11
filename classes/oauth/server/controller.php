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
        $this->_configs = Kohana::config('oauth_server.'.$this->_type);
        $this->oauth = new Model_Oauth;
    }

    /**********************************Server provider actions for user*******************/

    /**
     * the end-user authenticates directly with the authorization server, and grants client access to its protected resources
     *
     * @access  public
     * @return  void
     */
    public function action_authorize()
    {
        try {
            switch(Oauth::get('response_type'))
            {
                case 'code':
                    $response = $this->code();
                    break;
                case 'token':
                    $response = $this->token();
                    break;
                case 'code_and_token':
                    // TODO
                    return;
                default:
                    $response = new Oauth_Token(array(
                        'error'             => 'unsupported_response_type',
                        'error_description' => '',
                        'error_uri'         => '',
                    ));
                    throw new Oauth_Exception($response->query());
                    break;
            }
            $this->request->status = 302; #HTTP/1.1 302 Found
            $this->request->headers['Content-Type'] = 'application/x-www-form-urlencoded';
            $this->request->redirect($response);
        }
        catch (Oauth_Exception $e)
        {
            $this->request->status = 302; #HTTP/1.1 302 Found
            $this->request->headers['Content-Type'] = 'application/x-www-form-urlencoded';
            $this->request->redirect($e->getMessage());
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
                    $response = new Oauth_Token(array(
                        'error'             => 'unsupported_grant_type',
                        'error_description' => '',
                        'error_uri'         => '',
                    ));
                    throw new Oauth_Exception($response);
                    break;
            }

            $this->request->status = 200; #HTTP/1.1 200 OK
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

    /**********************************END actions*****************************************/


    /**
     * Client Requests Authorization by web_server
     *
     * @author    sumh <oalite@gmail.com>
     * @access    protected
     * @return    string    redirect_uri#[code,state|error=MUST be set to "redirect_uri_mismatch", "bad_authorization_code", "invalid_client"]
     */
    protected function code()
    {
        $parameter = new Oauth_Parameter_Code($this->_configs['code_params']);

        if($client = $this->oauth->lookup_client($parameter->client_id))
        {
            $client['expires_in'] = $this->_configs['durations']['code'];
            $response = $parameter->oauth_token($client);
        }
        else
        {
            $response = new Oauth_Token(array(
                'error'             => 'invalid_client',
                'error_description' => '',
                'error_uri'         => '',
            ));
            throw new Oauth_Exception($response->query());
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
            $response = new Oauth_Token(array(
                'error'             => 'invalid_client',
                'error_description' => '',
                'error_uri'         => '',
            ));
            throw new Oauth_Exception($response->query());
        }

        $response = $parameter->access_token($parameter->client_id);

        return $parameter->redirect_uri.'#'.$response->query();
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
            $response = new Oauth_Token(array(
                'error'             => 'invalid_client',
                'error_description' => '',
                'error_uri'         => '',
            ));

            throw new Oauth_Exception($response->query());
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
            $response = new Oauth_Token(array(
                'error'             => 'invalid_client',
                'error_description' => '',
                'error_uri'         => '',
            ));
            throw new Oauth_Exception($response->query());
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
            $response = new Oauth_Token(array(
                'error'             => 'invalid_client',
                'error_description' => '',
                'error_uri'         => '',
            ));
            throw new Oauth_Exception($response->query());
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
            $response = new Oauth_Token(array(
                'error'             => 'invalid_client',
                'error_description' => '',
                'error_uri'         => '',
            ));
            throw new Oauth_Exception($response->query());
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
            $response = new Oauth_Token(array(
                'error'             => 'invalid_client',
                'error_description' => '',
                'error_uri'         => '',
            ));
            throw new Oauth_Exception($response->query());
        }

        return $response;
    }

} // END Oauth Server Controller
