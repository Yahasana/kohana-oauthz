<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Server management
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
class Controller_Server extends Oauth_Server {

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
        $data['servers'] = $this->oauth->list_server($_SESSION['user']['uid']);
        
        $this->template->content = new View('v_oauth_server', $data);

        $this->request->response = $this->template->render();
    }

} // END Controller Consumer
