<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth client controller
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Kohana_Controller
 * *
 */
class Oauth_Client_Controller extends Kohana_Controller {

    /**
     * provider config group name
     *
     * @access  protected
     * @var     string  $provider
     */
    protected $_type = 'default';

    /**
     * Client's access token
     *
     * @access  protected
     * @var     string    $token
     */
    protected $token;

    /**
     * Request settings for OAuth
     *
     * @access  protected
     * @var     string $_configs
     */
    protected $_configs = null;

    public function before()
    {
        $this->_configs  = Kohana::config('oauth_client.'.$this->_type);
        // We have a WWW-Authenticate-header with OAuth data. Parse the header
        // and add those overriding any duplicates from GET or POST
        // if (isset($headers['www-authenticate']) && substr($headers['www-authenticate'], 0, 12) === 'Token realm=')
        // {
            // $this->_configs = Oauth::parse_header($headers['www-authenticate']) + $this->_configs;
        // }
    }

    protected function request_code($uri = NULL, $type = NULL)
    {
        if($uri === NULL) $uri = $this->_configs['oauth_uri'];

        if($type === NULL) $type = $this->_configs['response_type'];

        //~ build base string
        // $identifier = Oauth::normalize('POST', $uri, $this->_configs);

        //~ build signature string
        // $this->_configs['client_secret']    =
            // Oauth::signature($this->_configs['secret_type'], $identifier)
            // ->build(new Oauth_Client($this->_configs['client_id'], NULL), NULL);

        $params = array(
            'response_type' => $type,
            'client_id'     => $this->_configs['client_id'],
            'redirect_uri'  => $this->_configs['redirect_uri']
        );

        if( ! empty($this->_configs['state']))
            $params['state'] = $this->_configs['state'];

        $this->request->redirect($uri.'?'.Oauth::build_query($params));
    }

    /**/
    public function action_do()
    {
        $params = Oauth::parse_query();

        try
        {
            if(empty($params['code']) OR isset($params['error']))
            {
                throw new Oauth_Exception($params['error']);
            }

            $token = Remote::get($this->_configs['token_uri'],array(
                CURLOPT_POST        => TRUE,
                CURLOPT_HTTPHEADER  => array('Content-Type: application/x-www-form-urlencoded;charset=utf-8'),
                CURLOPT_POSTFIELDS  => Oauth::build_query(array(
                    'grant_type'    => $this->_configs['grant_type'],
                    'code'          => $params['code'],
                    'client_id'     => $this->_configs['client_id'],
                    'redirect_uri'  => $this->_configs['redirect_uri'],
                    'client_secret' => $this->_configs['client_secret'],
                ))
            ));

            $token = json_decode($token);
            if(property_exists($token, 'error'))
            {
                throw new Oauth_Exception($token->error);
            }

            // Resource in json format
            $resource = Remote::get($this->_configs['access_uri'],array(
                CURLOPT_POST        => TRUE,
                CURLOPT_HTTPHEADER  => array('Content-Type: application/x-www-form-urlencoded;charset=utf-8'),
                CURLOPT_POSTFIELDS  => Oauth::build_query(array(
                    'oauth_token'   => $token->access_token,
                    'timestamp'     => time(),
                    'refresh_token' => $token->refresh_token,
                    'expires_in'    => $token->expires_in,
                    'client_id'     => $this->_configs['client_id']
                ))
            ));

            $this->request->response = $resource;
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
        }

        if(isset($error))
        {
            switch($error)
            {
                case 'user_denied':
                    $this->request->response = 'You have denied this request.';
                    break;
                default:
                    $this->request->response = 'There must be some errors happen in this connection, please contact our web master.'."[$error]";
                    break;
            }
        }
    }

} // END Oauth Client Controller
