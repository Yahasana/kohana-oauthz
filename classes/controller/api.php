<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth controller
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2009 OALite team
 * @license     http://www.oalite.com/license.txt
 * @version     $id$
 * @link        http://www.oalite.com
 * @see         Oauth_Server_Controller
 * @since       Available since Release 1.0
 * *
 */
class Controller_Api extends Oauth_Controller {

    /**
     * Accessing Protected Resources
     *
     * @access    public
     * @return    void
     */
    public function action_index()
    {
        $methods = array();
        foreach(get_class_methods($this) as $method)
        {
            if(substr($method, 0, 7) === 'action_')
            {
                $method = ltrim($method, 'action_');
                $methods[$method] = '/api/'.$method;
            }
        }
        $this->request->response = json_encode($methods);
    }

    public function action_get()
    {
        //
    }

    public function action_post()
    {
        //
    }

    public function action_put()
    {
        //
    }

    public function action_delete()
    {
        //
    }

} // END Controller Oauth
