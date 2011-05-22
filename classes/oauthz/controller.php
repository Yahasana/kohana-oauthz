<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Authorization server, the authorization flows can be separated into three groups:
 * user delegation flows, direct credentials flows, and autonomous flows.
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.com
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
        Oauthz_Exception::$errors[$this->_type]['code_errors']      = $this->_configs['code_errors'];
        Oauthz_Exception::$errors[$this->_type]['token_errors']     = $this->_configs['token_errors'];
        Oauthz_Exception::$errors[$this->_type]['access_errors']    = $this->_configs['access_errors'];
    }

    #region authorization_code, oauth_token and access_token request handler

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
                if(method_exists($this, $response_type))
                {
                    $response = $this->$response_type();
                }
                // TODO, if is an absolute URI identifying an assertion format supported by the authorization server
                elseif($response_type = Arr::get($this->_configs['extension']['response_type'], $response_type))
                {
                    if(method_exists($this, $response_type))
                    {
                        $response = $this->$response_type();
                    }
                    elseif(class_exists('Oauthz_Extension_'.$response_type))
                    {
                        $response = Oauthz_Extension::factory($response_type)->execute();
                    }
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
            $response = (string) $e;
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
                if(method_exists($this, $grant_type))
                {
                    $response = $this->$grant_type();
                }
                // TODO, if is an absolute URI identifying an assertion format supported by the authorization server
                elseif($grant_type = Arr::get($this->_configs['extension']['grant_type'], $grant_type))
                {
                    if(method_exists($this, $grant_type))
                    {
                        $response = $this->$grant_type();
                    }
                    elseif(class_exists('Oauthz_Extension_'.$grant_type))
                    {
                        $response = Oauthz_Extension::factory($grant_type)->execute();
                    }
                }
            }

            if(isset($response))
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

    #endregion

    #region get authorization code or token ( response_type )

    /**
     * the client directs the resource owner to an authorization server (via its user-agent),
     * which in turns directs the resource owner back to the client with the authorization code.
     *
     * @access    protected
     * @return    string    redirect_uri?[code,state]
     * @throw     Oauthz_Exception_Authorize    Error Codes: invalid_request
     */
    protected function code()
    {
        $type = new Oauthz_Type_Code($this->_configs['code_params']);

        // Verify the client and generate a code if successes
        if($token = Oauthz_Model::factory('Token')->code($type->client_id))
        {
            $token['expires_in'] = $this->_configs['durations']['code'];

            // Populate the code
            $response = $type->oauth_token($token);
        }
        else
        {
            // Invalid client_id
            $exception = new Oauthz_Exception_Authorize('invalid_client');

            $exception->redirect_uri = $type->redirect_uri;

            $exception->state = $type->state;

            throw $exception;
        }

        return $type->redirect_uri.'?'.$response->as_query();
    }

    /**
     * get the access token via Implicit Grant Flow
     *
     * @access	protected
     * @return	string  redirect uri
     * @throw   Oauthz_Exception_Authorize  Error Codes: invalid_client
     */
    protected function token()
    {
        $params = $this->_configs['grant_params']['authorization_code'] + $this->_configs['code_params'];

        $type = new Oauthz_Type_Token($params);

        // Verify the client and the code, load the access token if successes
        if($token = Oauthz_Model::factory('Token')->oauth_token($type->client_id, $type->code))
        {
            $token['expires_in'] = $this->_configs['durations']['oauth_token'];

            $response = $type->oauth_token($token);
        }
        else
        {
            // Invalid client_id
            $exception = new Oauthz_Exception_Token('invalid_client');

            $exception->redirect_uri = $type->redirect_uri;

            $exception->state = $type->state;

            throw $exception;
        }

        return $type->redirect_uri.'#'.$response->as_query();
    }

    #endregion

    #region An authorization grant is used by the client to obtain an access token. ( grant_type )

    /**
     * Obtain an access token via authorization code
     *
     * @access	protected
     * @return	array
     * @throw   Oauthz_Exception_Token invalid_client
     */
    protected function authorization_code()
    {
        $type = new Oauthz_Type_Authorization_Code($this->_configs['grant_params']['authorization_code']);

        if($oauth_token = Oauthz_Model::factory('Token')->oauth_token($type->client_id, $type->code))
        {
            //$audit = new Model_Oauthz_Audit;
            //$audit->audit_token($response);

            // Verify the oauth token send by client
            $response = $type->access_token($oauth_token);
        }
        else
        {
            throw new Oauthz_Exception_Token('invalid_client');
        }

        return $response;
    }

    // can be used directly as an authorization grant to obtain an access token
    protected function password()
    {
        $type = new Oauthz_Type_Password;

        if($client = Oauthz_Model::factory('Server')->lookup($type->client_id))
        {
            // Verify the user information send by client
            $response = $type->access_token($client);
        }
        else
        {
            throw new Oauthz_Exception_Token('invalid_client');
        }

        return $response;
    }

    // the client is acting on its own behalf (the client is also the resource owner)
    protected function client_credentials()
    {
        $type = new Oauthz_Type_Client_Credentials;

        if($client = Oauthz_Model::factory('Server')->lookup($type->client_id))
        {
            // Verify the user information send by client
            $response = $type->access_token($client);
        }
        else
        {
            throw new Oauthz_Exception_Token('invalid_client');
        }

        return $response;
    }

    // TODO
    protected function refresh_token()
    {
        $type = new Oauthz_Type_Client_Credentials;

        if($refresh_token = Oauthz_Model::factory('Token')->refresh_token($type->client_id))
        {
            // Verify the oauth token send by client
            $response = $type->refresh_token($refresh_token);
        }
        else
        {
            throw new Oauthz_Exception_Token('invalid_client');
        }

        return $response;
    }

    #endregion

} // END Oauthz Server Controller
