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
 * @see        Oauth_Server_Controller
 * *
 */
class Controller_Authorize extends Oauth_Server_Controller {

    public function action_index()
    {
        //~ are you sure to access your infomation data from webservice sp.example.com
        //~ Yes - send request token to sp.example.com
        //~ $uri = 'http://localhost/oauth/request';
        //$token = parent::request_token();

        //~ $uri = 'http://localhost/oauth/authorize';
        parent::action_authorize();
    }

    public function action_okay()
    {
        echo 'ha ha hahahha!';
    }

    public function action_test()
    {
        extract(array('hel'=>'helo'),EXTR_PREFIX_ALL,'this->');
        $this->request->response = $this->_hel;
    }

} // END Controller Consumer
