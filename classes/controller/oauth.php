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
        $error = array();

        $errors['code_errors'] = I18n::get('Authorization Errors Response');
        $errors['token_errors'] = I18n::get('Token Errors Response');
        $errors['access_errors'] = I18n::get('Access Errors Response');

        if(isset($errors['code_errors'][$error_code]))
        {
            $error['code_errors'][$error_code] = $errors['code_errors'][$error_code];
        }

        if(isset($errors['token_errors'][$error_code]))
        {
            $error['token_errors'][$error_code] = $errors['code_errors'][$error_code];
        }

        if(isset($errors['access_errors'][$error_code]))
        {
            $error['access_errors'][$error_code] = $errors['code_errors'][$error_code];
        }

        if($error)
        {
            $error['error_code'] = $error_code;
            $errors = $error;
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
