<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth consumer controller
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link		http://oalite.com
 * @see         Oauthz_Client_Controller
 * *
 */
class Controller_Client extends Oauthz_Client {

    public function action_index()
    {
        $template = new View('oauthz-template');

        $template->content = new View('oauthz-client');

        $this->request->response = $template->render();
    }

    public function action_test()
    {
        try
        {
            $resource = Remote::get('http://docs/api/get/1',array(
                CURLOPT_POST        => TRUE,
                CURLOPT_HTTPHEADER  => array('Content-Type: application/x-www-form-urlencoded;charset=UTF-8'),
                CURLOPT_POSTFIELDS  => http_build_query(array(
                     'client_id'         => 'OA_4bfbc43769917',
                     //'oauth_token'       => $token->access_token,
                     //'refresh_token'     => $token->refresh_token,
                     //'expires_in'        => $token->expires_in
                ), '', '&')
            ));
        }
        catch (Exception $e)
        {
            $resource = $e->getMessage();
        }
        echo '<pre>'.print_r($resource,TRUE).'</pre>';
    }

} // END Controller Consumer
