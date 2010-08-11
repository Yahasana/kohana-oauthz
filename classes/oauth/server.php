<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth servers register management
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link		http://www.oalite.cn
 * @see			Kohana_Controller
 * *
 */
class Oauth_Server extends Kohana_Controller {

    protected $template = 'v_oauth';

    public function before()
    {
        $this->oauth = new Model_Oauth;
        $this->template = new View($this->template);
    }

    public function action_register($client_id = NULL)
    {
        $data = array();
        if($client_id)
        {
            $data = $this->oauth->lookup_server($client_id, $_SESSION['user']['uid']);
        }
        elseif(isset($_POST['password']))
        {
            $post = new Validate($_POST);
            $post->filter(TRUE, 'trim')
                ->rule('redirect_uri', 'not_empty');

            if(empty($_POST['client_id']))
            {
                $post->rule('password', 'not_empty')
                    ->rule('password', 'min_length', array(6))
                    ->rule('confirm',  'matches', array('password'));
            }
            elseif( ! empty($_POST['password']) OR ! empty($_POST['confirm']))
            {
                $post->rule('password', 'not_empty')
                    ->rule('password', 'min_length', array(6))
                    ->rule('confirm',  'matches', array('password'));
            }

            if($post->check())
            {
                $_POST['user_id'] = $_SESSION['user']['uid'];

                if(empty($_POST['client_id']))
                {
                    if($this->oauth->unique_server($_POST['redirect_uri']))
                    {
                        $post->error('redirect_uri', 'not unique');
                        $data['errors'] = $post->errors('validate');
                    }
                    else
                    {
                        $this->oauth->reg_server($_POST);
                    }
                }
                else
                {
                    $this->oauth->update_server($_POST);
                }
            }
            else
            {
                $data['errors'] = $post->errors('validate');
            }
        }

        $this->template->content = new View('v_oauth_register', $data);

        $this->request->response = $this->template->render();
    }

    public function action_client()
    {
        $data['clients'] = $this->oauth->list_client('0');

        $this->template->content = new View('v_oauth_client', $data);

        $this->request->response = $this->template->render();
    }

    public function action_access_deny()
    {
        $this->request->status = 302; #HTTP/1.1 302 Found
        $this->request->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $this->request->headers['Location'] = 'http://example.com/rd#error=user_denied';
    }

} // END Oauth_Server
