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
 * @since      Available since Release 1.0
 * *
 */
class Controller_Oauth extends Oauth_Server_Controller {

    public function action_index()
    {
        $template = new View('v_oauth');
        $template->content = '<h3>Hello guest.</h3>';
        $this->request->response = $template;
    }

    public function action_code()
    {
        $query = URL::query();
        $view = new View('v_oauth_authorize', array('authorized' => TRUE, 'query' => $query));
        $this->request->response = $view->render();
    }

    public function action_error($error_code = NULL)
    {
        $errors = array();

        $config = $this->_configs;

        if(isset($config['req_code_errors'][$error_code]))
        {
            $errors['req_code_errors'][$error_code] = $config['req_code_errors'][$error_code];
        }

        if(isset($config['req_token_errors'][$error_code]))
        {
            $errors['req_token_errors'][$error_code] = $config['req_code_errors'][$error_code];
        }

        if(isset($config['access_res_errors'][$error_code]))
        {
            $errors['access_res_errors'][$error_code] = $config['req_code_errors'][$error_code];
        }

        if($errors)
        {
            $errors['error_code'] = $error_code;
        }
        else
        {
            $errors['req_code_errors'] = $config['req_code_errors'];
            $errors['req_token_errors'] = $config['req_code_errors'];
            $errors['access_res_errors'] = $config['req_code_errors'];
        }

        $template = new View('v_oauth');
        $view = new View('v_oauth_error', $errors);
        $template->content = $view->render();
        $this->request->response = $template;
    }

    public function action_signin()
    {
        if( ! empty($_POST['usermail']) AND Validate::email($_POST['usermail']))
        {
            $user = array(
                'uid' => $_SERVER['REQUEST_TIME'],
                'mail' => $_POST['usermail']
            );
            Cookie::set('user', json_encode($user));
            Session::instance()->set('user', $user);
            $this->request->redirect('server/index');
        }
        elseif($user = Cookie::get('user'))
        {
            Session::instance()->set('user', json_decode($user, TRUE));
            $this->request->redirect('server/index');
        }

        $template = new View('v_oauth');
        $view = new View('v_oauth_signin');
        $template->content = $view->render();
        $this->request->response = $template;
    }

    public function action_logout()
    {
        Session::instance()->delete('user');
        $this->request->redirect('oauth/index');
    }

    public function action_okay()
    {
        echo $this->request->referrer;
    }

    public function action_test()
    {
        extract(array('hel' => 'helo'), EXTR_PREFIX_ALL, 'this->');
        $this->request->response = $this->_hel;
    }

} // END Controller Consumer
