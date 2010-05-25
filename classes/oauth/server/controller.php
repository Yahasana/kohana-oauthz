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
                    $token = $this->user_agent();
                    break;
                case 'web_server':
                    $token = $this->user_server();
                    break;
                case 'device_code':
                    $token = $this->user_device();
                    $this->request->status = 200; #HTTP/1.1 200 OK
                    $this->request->response = $token;
                    $this->request->headers['Cache-Control'] = 'no-store';
                    $this->request->headers['Content-Type'] = 'application/'.$token->format;
                    return;
                default:
                    throw new Oauth_Exception('incorrect_request_type');
                    break;
            }
            $this->request->response = $token;
            // $this->request->redirect($token);
        }
        catch (Oauth_Exception $e)
        {
            $this->request->status = 400; #HTTP/1.1 400 Bad Request
            $this->request->headers['Content-Type'] = 'application/json';
            $this->request->response = json_encode(array('error' => $e->getMessage()));
            // $this->render('v_oauth_authorize', array('error' => $e->getMessage()));
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
            switch(Oauth::post('type'))
            {
                case 'web_server':
                    $token = $this->web_server();
                    break;
                case 'refresh_token':
                    $token = $this->reflesh_token();
                    break;
                case 'device_token':
                    $token = $this->device_token();
                case 'username':
                    $token = $this->username();
                    break;
                case 'client_credentials':
                    $token = $this->client_credentials();
                    break;
                case 'assertion':
                    $token = $this->assertion();
                    break;
                default:
                    throw new Oauth_Exception('incorrect_request_type');
                    break;
            }

            $this->request->status = 200; #HTTP/1.1 200 OK
            $this->request->response = $token;
            $this->request->headers['Content-Type'] = 'application/'.$token->format;
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
        $params = new Oauth_Parameter_Useragent;

        if($client = $this->oauth->lookup_client($params->client_id))
        {
            if(TRUE !== $error = $params->authorization_check())
            {
                return $params->redirect_uri.'#error='.$error;
            }
        }
        else
        {
            return $params->redirect_uri.'#error=incorrect_client_credentials';
        }

        if($token = $this->oauth->access_token($params->client_id))
        {
            if(! empty($params->state))
                $token->state = $params->state;

            $token->redirect_uri = $params->redirect_uri;

            return $token;
        }


        return $params->redirect_uri.'#'.Oauth::build_query(array(
            'access_token'      => $client->access_token, // REQUIRED.  The access token.
            'expires_in'        => $client->expires_in,   // OPTIONAL.  The duration in seconds of the access token lifetime.
            'refresh_token'      => $client->refresh_token,// OPTIONAL.  The refresh token.
            'state'             => $params->state,
            'access_token_secret'=> $client->access_token_secret,// REQUIRED if requested by the client.
        ));
        return $params->redirect_uri.'#error=user_denied';
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
        $params = new Oauth_Parameter_Webserver;

        if($client = $this->oauth->lookup_client($params->client_id))
        {
            if(TRUE !== $error = $params->authorization_check())
            {
                return $params->redirect_uri.'#error='.$error;
            }
        }
        else
        {
            return $params->redirect_uri.'#error=incorrect_client_credentials';
        }
    }

    protected function user_device()
    {
        $params = new Oauth_Parameter_Device;

        //
        return array(
            'code' => 'get_from_query', // REQUIRED.  The verification code.
            'user_code' => 'get_from_query', // REQUIRED.  The user code.
            'verification_uri' => '', // REQUIRED.  The end-user verification URI on the authorization server.
            'expires_in' => 'web_server',   // OPTIONAL.  The duration in seconds of the access token lifetime.
            'interval'=> 'web_server',// OPTIONAL.  The minimum amount of time in seconds that the client SHOULD wait between polling requests to the token endpoint.
        );
        // MUST be set to "authorization_declined".
        throw new Oauth_Exception('authorization_declined');
    }

    protected function web_server()
    {
        $params = new Oauth_Parameter_Webserver;

        if($client = $this->oauth->lookup_server($params->client_id))
        {
            if(TRUE !== $error = $params->access_token_check($client))
            {
                throw new Oauth_Exception($error);
            }
            else
            {
                return array(
                    'access_token'      => 'web_server', // REQUIRED.  The access token.
                    'expires_in'      => 'web_server',   // OPTIONAL.  The duration in seconds of the access token lifetime.
                    'refresh_token'      => 'web_server',// OPTIONAL.  The refresh token.
                    'access_token_secret'=> 'web_server',// REQUIRED if requested by the client.
                    'scope'             => '' //OPTIONAL.  The scope of the access token as a list of space-delimited strings.
                );
            }
        }
        else
        {
            // MUST be set to either "redirect_uri_mismatch", "bad_verification_code", "incorrect_client_credentials"
            throw new Oauth_Exception('');
        }
    }

    protected function device_token()
    {
        $params = new Oauth_Parameter_Device;

        return array(
            'access_token'      => 'web_server', // REQUIRED.  The access token.
            'expires_in'      => 'web_server',   // OPTIONAL.  The duration in seconds of the access token lifetime.
            'refresh_token'      => 'web_server',// OPTIONAL.  The refresh token.
            'access_token_secret'=> 'web_server', // REQUIRED if requested by the client.
            'scope'             => '' //OPTIONAL.  The scope of the access token as a list of space-delimited strings.
        );
        // MUST be set to "invalid_assertion" or "unknown_format"
        throw new Oauth_Exception('invalid_assertion');
    }

    protected function username()
    {
        $params = new Oauth_Parameter_Username;

        return array(
            'access_token'      => 'web_server', // REQUIRED.  The access token.
            'expires_in'      => 'web_server',   // OPTIONAL.  The duration in seconds of the access token lifetime.
            'refresh_token'      => 'web_server',// OPTIONAL.  The refresh token.
            'access_token_secret'=> 'web_server',// REQUIRED if requested by the client.
            'scope'             => '' //OPTIONAL.  The scope of the access token as a list of space-delimited strings.
        );
        // MUST be set to either "incorrect_client_credentials" or "unauthorized_client"
        throw new Oauth_Exception('');
    }

    protected function client_credentials()
    {
        $params = new Oauth_Parameter_Credentials;

        return array(
            'access_token'      => 'web_server', // REQUIRED.  The access token.
            'expires_in'      => 'web_server',   // OPTIONAL.  The duration in seconds of the access token lifetime.
            'refresh_token'      => 'web_server',// OPTIONAL.  The refresh token.
            'access_token_secret'=> 'web_server',// REQUIRED if requested by the client.
            'scope'             => '' //OPTIONAL.  The scope of the access token as a list of space-delimited strings.
        );
        // MUST be set to "incorrect_client_credentials"
        throw new Oauth_Exception('incorrect_client_credentials');
    }

    protected function assertion()
    {
        $params = new Oauth_Parameter_Assertion;

        // The authorization server SHOULD NOT issue a refresh token.
        return array(
            'access_token'      => 'web_server', // REQUIRED.  The access token.
            'expires_in'      => 'web_server',   // OPTIONAL.  The duration in seconds of the access token lifetime.
            'access_token_secret'=> 'web_server',// REQUIRED if requested by the client.
            'scope'             => '' //OPTIONAL.  The scope of the access token as a list of space-delimited strings.
        );
        // MUST be set to "invalid_assertion" or "unknown_format"
        throw new Oauth_Exception('invalid_assertion');
    }

    protected function reflesh_token()
    {
        $params = new Oauth_Parameter_Reflesh;

        /**
         * MAY issue a new refresh token in which case the client MUST NOT use
         * the previous refresh token and replace it with the newly issued
         * refresh token.
         */
        return array(
            'access_token'      => 'web_server', // REQUIRED.  The access token.
            'expires_in'      => 'web_server',   // OPTIONAL.  The duration in seconds of the access token lifetime.
            'refresh_token'      => 'web_server',// OPTIONAL.  The refresh token.
            'access_token_secret'=> 'web_server', // REQUIRED if requested by the client.
            'scope'             => '' //OPTIONAL.  The scope of the access token as a list of space-delimited strings.
        );
        // MUST be set to either "incorrect_credentials", "authorization_expired", or "unsupported_secret_type"
        throw new Oauth_Exception('');
    }

} //END Oauth Server Controller
