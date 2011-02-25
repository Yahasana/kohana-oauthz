<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth client request controller
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthy
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Kohana_Controller
 * *
 */
class Oauthy_Client extends Kohana_Controller {

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
        $this->_configs  = Kohana::config('oauth-client.'.$this->_type);
    }

    public function action_code($uri = NULL, $type = NULL)
    {
        if($uri === NULL) $uri = $this->_configs['oauth_uri'];

        if($type === NULL) $type = $this->_configs['response_type'];

        //~ build base string
        // $identifier = Oauthy::normalize('POST', $uri, $this->_configs);

        //~ build signature string
        // $this->_configs['client_secret']    =
            // Oauthy::signature($this->_configs['secret_type'], $identifier)
            // ->build(new Oauthy_Client($this->_configs['client_id'], NULL), NULL);

        $params = array(
            'response_type' => $type,
            'client_id'     => $this->_configs['client_id'],
            'redirect_uri'  => $this->_configs['redirect_uri']
        );

        if( ! empty($this->_configs['state']))
            $params['state'] = $this->_configs['state'];

        $this->request->redirect($uri.'?'.Oauthy::build_query($params));
    }

    /**/
    public function action_do()
    {
        $params = Oauthy::parse_query();

        try
        {
            if(empty($params['code']) OR isset($params['error']))
            {
                throw new Oauthy_Exception($params['error']);
            }

            $token = Remote::get($this->_configs['token_uri'],array(
                CURLOPT_POST        => TRUE,
                CURLOPT_HTTPHEADER  => array('Content-Type: application/x-www-form-urlencoded;charset=utf-8'),
                CURLOPT_POSTFIELDS  => Oauthy::build_query(array(
                    'grant_type'    => $this->_configs['grant_type'],
                    'code'          => $params['code'],
                    'client_id'     => $this->_configs['client_id'],
                    'redirect_uri'  => $this->_configs['redirect_uri'],
                    'client_secret' => $this->_configs['client_secret'],
                ))
            ));

            $token = json_decode($token);
            if(isset($token->error))
            {
                throw new Oauthy_Exception($token->error);
            }

            // Resource in json format
            $resource = Remote::get($this->_configs['access_uri'],array(
                CURLOPT_POST        => TRUE,
                CURLOPT_HTTPHEADER  => array('Content-Type: application/x-www-form-urlencoded;charset=utf-8'),
                CURLOPT_POSTFIELDS  => Oauthy::build_query(array(
                    'oauth_token'   => $token->access_token,
                    'timestamp'     => $_SERVER['REQUEST_TIME'],
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

} // END Oauthy Client Controller
