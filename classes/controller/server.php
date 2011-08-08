<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Server management
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * @see         Oauthz_Server
 * *
 */
class Controller_Server extends Oauthz_Server {

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
        $server = new Model_Oauthz_Client;

        $data = $server->lists(array('user_id' => $_SESSION['user']['uid']));

        $this->template->content = new View('oauthz-server', $data);

        $this->request->response = $this->template->render();
    }

} // END Controller Consumer
