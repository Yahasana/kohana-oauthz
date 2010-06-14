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
class Controller_Oauth extends Oauth_Server_Controller {

    public function action_authorize()
    {
        $view = new View('v_oauth_authorize', array('authorized' => TRUE));
        $this->request->response = $view->render();
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
