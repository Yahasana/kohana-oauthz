<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth consumer controller
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link		http://oalite.com
 * @see         Oauthz_Controller
 * *
 */
class Controller_Authorize extends Oauthz_Controller {

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
