<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth client request controller
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * @see         Kohana_Controller
 * *
 */
class Oauthz_Client extends Kohana_Controller {

    /**
     * provider config group name
     *
     * @access  protected
     * @var     string  $provider
     */
    protected $_type = 'default';

    /**
     * Request settings for OAuth
     *
     * @access  protected
     * @var     string $_configs
     */
    protected $_configs = null;

    public function before()
    {
        $this->_configs = Kohana::config('oauth-client')->get($this->_type);
    }

    /* Obtain an an Authorization Code, ONLY for Authorization Code flow */
    public function action_code($uri = NULL)
    {
        $type = $this->_configs['protocol-flow'];

        if($type !== 'code')
        {
            // Redirect to correct action
            if(isset($this->_configs[$type]))
            {
                $this->request->redirect('client/do'.($uri ? "/$uri" : ''));
            }
            else
            {
                return $this->request->response = 'Unsupported protocal flow, please check your oauth-client.php config.';
            }
        }

        empty($uri) AND $uri = $this->_configs['oauth-uri'];

        // Load the paramtes and remove all empty ones
        $params = array_filter($this->_configs['code']);
        $params['response_type'] = 'code';

        $this->request->redirect($uri.'?'.http_build_query($params, '', '&'));
    }

    /* Request an access token and then access the protected resource */
    public function action_do($uri = NULL)
    {
        try
        {
            $access_token = $this->token($uri);
            
            // Resource in json format
            $this->request->response = Remote::get($this->_configs['resource-uri'], array(
                CURLOPT_POST        => TRUE,
                CURLOPT_HTTPHEADER  => array('Content-Type: application/x-www-form-urlencoded;charset=utf-8'),
                CURLOPT_POSTFIELDS  => http_build_query($access_token, '', '&')
            ));
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();

            switch($error)
            {
                case 'access_denied':
                    $this->request->response = 'You have denied this request.';
                    break;
                default:
                    $this->request->response = 'There must be some errors happen in this connection, please contact our web master.'."[$error]";
                    break;
            }
        }
    }

    /* Obtain an Access Token */
    protected function token($uri = NULL)
    {
        empty($uri) OR $uri = $this->_configs['oauth-uri'];

        $type = $this->_configs['protocol-flow'];

        if(isset($this->_configs[$type]))
        {
            $params = $this->_configs[$type];
            switch($type)
            {
                case 'code':
                    if(empty($_GET['code']))
                    {
                        throw new Oauthz_Exception(isset($_GET['error']) ? $_GET['error'] : 'Unknow error');
                    }
                    $params['code']         = $_GET['code'];
                    $params['grant_type']   = 'authorization_code';
                    break;
                case 'token':
                    $params['response_type'] = 'token';
                    break;
                case 'password':
                    $params['grant_type'] = 'password';
                    break;
                case 'client_credentials':
                    $params['grant_type'] = 'client_credentials';
                    break;
            }

            // Request access token
            $token = Remote::get($this->_configs['token-uri'],array(
                CURLOPT_POST        => TRUE,
                CURLOPT_HTTPHEADER  => array('Content-Type: application/x-www-form-urlencoded;charset=utf-8'),
                CURLOPT_POSTFIELDS  => Ohttp_build_query($params, '', '&')
            ));

            $token = json_decode($token, TRUE);

            if(isset($token['error']))
            {
                throw new Oauthz_Exception($token['error']);
            }

            // Store the client info into the token
            $token['client_id']       = $params['client_id'];
            $token['client_secret']   = $params['client_secret'];

            return $token;
        }

        throw new Oauthz_Exception('Unsupported protocal flow, please check your oauth-client.php config.');
    }

    /* Refreshing an Access Token */
    protected function refresh_token(array $token)
    {
        // TODo
    }

} // END Oauthz Client Controller
