<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth server controller
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link		http://www.oalite.cn
 * @see        Oauth_Server_Controller
 * *
 */
class Controller_Oauth extends Oauth_Server_Controller {

    public function action_index()
    {
        $template = new View('oauth');
        $template->content = '<h3>Hello guest.</h3>';
        $this->request->response = $template;
    }

    public function action_code()
    {
        $query = URL::query();
        $template = new View('oauth');
        $view = new View('oauth-authorize', array('authorized' => TRUE, 'query' => $query));
        $template->content = $view->render();
        $this->request->response = $template;
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

        $template = new View('oauth');
        $view = new View('oauth-error', $errors);
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

        $template = new View('oauth');
        $view = new View('oauth-signin');
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

} // END Controller Consumer
