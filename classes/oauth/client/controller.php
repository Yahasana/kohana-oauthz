<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth client controller
 *
 * @author     sumh <oalite@gmail.com>
 * @package    Oauth
 * @copyright  (c) 2009 OALite team
 * @license    http://www.oalite.com/license.txt
 * @version    $id$
 * @link       http://www.oalite.com
 * @see        Kohana_Controller
 * @since      Available since Release 1.0
 * *
 */
class Oauth_Client_Controller extends Kohana_Controller {

    /**
     * provider config group name
     *
     * @access  protected
     * @var     string  $provider
     */
    protected $provider = 'default';

    /**
     * provider's request token uri
     *
     * @access  protected
     * @var     string    $request_uri
     */
    protected $_req_uri;

    /**
     * provider's authorize token uri
     *
     * @access  protected
     * @var     string    $authorize_uri
     */
    protected $_aut_uri;

    /**
     * provider's access token uri
     *
     * @access  protected
     * @var     string    $access_uri
     */
    protected $_acc_uri;

    /**
     * Request settings for OAuth
     *
     * @access  protected
     * @var     string $_params
     */
    protected $_params = null;

    public function before()
    {
        $config = Kohana::config('oauth_provider')->{$this->provider};
        $this->_params  = $config['req_code_params'];
        $this->_req_uri = $config['request_uri'];
        $this->_aut_uri = $config['authorize_uri'];
        $this->_acc_uri = $config['access_uri'];
        // We have a WWW-Authenticate-header with OAuth data. Parse the header
        // and add those overriding any duplicates from GET or POST
        // if (isset($headers['www-authenticate']) && substr($headers['www-authenticate'], 0, 12) === 'Token realm=')
        // {
            // $this->_params = Oauth::parse_header($headers['www-authenticate']) + $this->_params;
        // }
    }

    protected function request_token($uri = NULL, $type = 'user_agent')
    {
        if($uri === NULL) $uri = $this->_req_uri;

        $this->_params['type'] = $type;

        //~ build base string
        $base_string = Oauth::normalize('POST', $uri, $this->_params);

        //~ build signature string
        $this->_params['client_secret']    =
            Oauth_Signature::factory($this->_params['secret_type'], $base_string)
            ->build(new Oauth_Client($this->_params['client_id'], NULL), NULL);

        //~ send request to get request_token with POST
        $response = Remote::get($uri.'?'.Oauth::build_query($this->_params), array(
            // CURLOPT_POST        => TRUE,
            // CURLOPT_HTTPHEADER  => array('Content-Type: application/x-www-form-urlencoded;charset=UTF-8'),
            // CURLOPT_POSTFIELDS  => Oauth::build_query($this->_params)
        ));
        $this->request->redirect($response);
    }

    /**
     * oauth_token:
     *     OPTIONAL. The Request Token obtained in the previous step. The Service Provider MAY declare this parameter as REQUIRED, or accept requests to the User Authorization URL without it, in which case it will prompt the User to enter it manually.
     * oauth_callback:
     *     OPTIONAL. The Consumer MAY specify a URL the Service Provider will use to redirect the User back to the Consumer when Obtaining User Authorization (Obtaining User Authorization) is complete.
     * Additional parameters:
     *     Any additional parameters, as defined by the Service Provider.
     *
     * @access    protected
     * @param     string        $uri
     * @param     Oauth_Token   $token
     * @return    void
     */
    protected function goto_authorize($uri = NULL)
    {
        if($uri === NULL) $uri = $this->_aut_uri;

        $this->_params['code'] = $token->code;
        // $this->_params['token_secret'] = $token->access_token;
        $this->_params['redirect_uri'] = Kohana::config('oauth_provider')->get('oauth_callback');

        //~ build base string
        $base_string = Oauth::normalize('GET', $uri, $this->_params);

        //~ build signature string
        $this->_params['token_secret'] = Oauth::signature($this->_params['secret_type'], $base_string)->build($token);

        $uri = $uri.'?'.Oauth::build_query($this->_params);
        $this->request->redirect($uri);
    }

} //END Oauth Consumer Controller
