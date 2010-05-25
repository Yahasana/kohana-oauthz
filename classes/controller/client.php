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
        // die('<pre>'.print_r(phpinfo(),true).'</pre>');
        try {
            //~ are you sure to access your infomation data from webservice sp.example.com
            //~ Yes - send request token to sp.example.com
            //~ $uri = 'http://localhost/oauth/request';
            parent::request_token();
        }
        catch(Exception $e)
        {
            $this->request->response = $e->getMessage();
        }

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

} //END Controller Consumer
