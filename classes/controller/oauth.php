<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth server controller
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link		http://oalite.com
 * @see         Oauthz_Controller
 * *
 */
class Controller_Oauth extends Oauthz_Controller {

    public function action_index()
    {
        $template = new View('oauthz-template');
        $template->content = '<h3>Hello guest.</h3>';
        $this->request->response = $template;
    }

    public function action_code()
    {
        $query = URL::query();
        $template = new View('oauthz-template');
        $view = new View('oauthz-server-authorize', array('authorized' => TRUE, 'query' => $query));
        $template->content = $view->render();
        $this->request->response = $template;
    }

    public function action_error($error_code = NULL)
    {
        $errors = array();

        $config = $this->_configs;

        if(isset($config['code_errors'][$error_code]))
        {
            $errors['code_errors'][$error_code] = $config['code_errors'][$error_code];
        }

        if(isset($config['token_errors'][$error_code]))
        {
            $errors['token_errors'][$error_code] = $config['code_errors'][$error_code];
        }

        if(isset($config['access_errors'][$error_code]))
        {
            $errors['access_errors'][$error_code] = $config['code_errors'][$error_code];
        }

        if($errors)
        {
            $errors['error_code'] = $error_code;
        }
        else
        {
            $errors['code_errors'] = $config['code_errors'];
            $errors['token_errors'] = $config['code_errors'];
            $errors['access_errors'] = $config['code_errors'];
        }

        $template = new View('oauthz-template');
        $view = new View('oauthz-server-error', $errors);
        $template->content = $view->render();
        $this->request->response = $template;
    }

    public function action_signin()
    {
        if( ! empty($_POST['usermail']) AND Validate::email($_POST['usermail']))
        {
            $user = array(
                'uid'   => $_SERVER['REQUEST_TIME'],
                'mail'  => $_POST['usermail']
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

        $template = new View('oauthz-template');
        $view = new View('oauthz-server-signin');
        $template->content = $view->render();
        $this->request->response = $template;
    }

    public function action_logout()
    {
        Session::instance()->delete('user');
        Cookie::delete('user');
        $this->request->redirect('oauth/index');
    }

    public function action_okay()
    {
        echo $this->request->referrer;
    }

} // END Controller Consumer
