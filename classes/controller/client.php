<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth consumer controller
 *
 * @author     sumh <oalite@gmail.com>
 * @package    Oauth
 * @copyright  (c) 2009 OALite team
 * @license    http://www.oalite.com/license.txt
 * @version    $id$
 * @link       http://www.oalite.com
 * @see        Oauth_Consumer_Controller
 * @since      Available since Release 1.0
 * *
 */
class Controller_Client extends Oauth_Client_Controller {

    public function action_index()
    {
        $params = array(
            'type'  => 'web_server',
            'client_id' => 'OA_4bfbc43769917',
            'redirect_uri'  => 'http://docs/client/okay'
        );
        $this->request->redirect('http://docs/oauth/authorize?'.Oauth::build_query($params));
    }

    public function action_okay()
    {
        $params = Oauth::parse_query();
        if(! empty($params['code']))
        {
            $token_uri = 'http://docs/oauth/token';
            $token = Remote::get($token_uri,array(
                CURLOPT_POST        => TRUE,
                CURLOPT_HTTPHEADER  => array('Content-Type: application/x-www-form-urlencoded;charset=UTF-8'),
                CURLOPT_POSTFIELDS  => Oauth::build_query(array(
                    'client_id' => 'OA_4bfbc43769917',
                    'code'      => $params['code'],
                    'type'      => 'web_server'
                ))
            ));

            $token = json_decode($token);
            if( ! property_exists($token, 'error'))
            {
                $resource_uri = 'http://docs/api';
                $resource = Remote::get($resource_uri,array(
                    CURLOPT_POST        => TRUE,
                    CURLOPT_HTTPHEADER  => array('Content-Type: application/x-www-form-urlencoded;charset=UTF-8'),
                    CURLOPT_POSTFIELDS  => Oauth::build_query(array(
                        'client_id' => 'OA_4bfbc43769917',
                        'oauth_token'       => $token->access_token,
                        'refresh_token'     => $token->refresh_token,
                        'expires_in'        => $token->expires_in
                    ))
                ));
                echo '<pre>'.print_r($resource,TRUE).'</pre>';
            }
            else
            {
                $error = $token->error;
            }
        }
        else
        {
            $error = $params['error'];
        }

        if(isset($error))
        {
            switch($error)
            {
                case 'user_denied':
                    $this->request->response = 'You have denied this request.';
                    break;
                default:
                    $this->request->response = 'There must be some errors happen in this connection, please contact our web master.';
                    break;
            }
        }
    }

} //END Controller Consumer
