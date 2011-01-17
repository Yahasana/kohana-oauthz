<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Server management
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see        Oauth_Server
 * *
 */
class Controller_Server extends Oauth2_Server {

    public function __construct(Request $request)
    {
        if( ! Session::instance()->get('user'))
        {
            $request->redirect('oauth/signin');
        }

        parent::__construct($request);
    }

    public function action_index()
    {
        $server = new Model_Oauth_Server;

        $data = $server->lists(array('user_id' => $_SESSION['user']['uid']));

        $this->template->content = new View('oauth-server', $data);

        $this->request->response = $this->template->render();
    }

} // END Controller Consumer
