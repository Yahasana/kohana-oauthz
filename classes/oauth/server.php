<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth servers register management
 *
 * @author		sumh <oalite@gmail.com>
 * @package		Oauth_Server
 * @copyright	(c) 2009 OALite team
 * @license		http://www.oalite.com/license.txt
 * @version		$id$
 * @link		http://www.oalite.com
 * @see			Kohana_Controller
 * @since		Available since Release 1.0
 * *
 */
class Oauth_Server extends Kohana_Controller {

    public function before()
    {
        $this->oauth = new Model_Oauth;
    }

    public function action_register()
    {
        if(isset($_POST['pass']))
        {
            $post = new Validate($_POST);
            $post->filter(TRUE, 'trim')
                ->rule('password', 'not_empty')
                ->rule('password', 'min_length', array('6'))
                ->rule('confirm',  'matches', array('password'))
                ->rule('redirect_uri', array($this->oauth, 'unique_server'));
            if($post->check())
            {
                if(empty($_POST['client_id']))
                {
                    $this->oauth->reg_server($_POST);
                }
                else
                {
                    $this->oauth->update_server($_POST);
                }
            }
            else
            {
                $data['errors'] = $post->errors();
            }
        }

        $data['servers'] = $this->oauth->list_server('0');

        $view = new View('template');
        $view->content = new View('v_oauth_server', $data);

        $this->request->response = $view->render();
    }

    public function action_client()
    {
        $data['clients'] = $this->oauth->list_client('0');

        $view = new View('template');
        $view->content = new View('v_oauth_client', $data);

        $this->request->response = $view->render();
    }

    public function action_access_deny()
    {
        $this->request->status = 302; #HTTP/1.1 302 Found
        $this->request->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $this->request->headers['Location'] = 'http://example.com/rd#error=user_denied';
    }
}
