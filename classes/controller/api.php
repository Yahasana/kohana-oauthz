<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth controller
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthy
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link		http://www.oalite.cn
 * @see         Oauthy_Api
 * *
 */
class Controller_Api extends Oauthy_Api {

    protected $_exclude = array('xrds', 'index');

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
        $this->request->response = json_encode(array('Hello', 'OAuth', '2.0'));
    }

    public function action_create()
    {
        //
    }

    public function action_update()
    {
        //
    }

    public function action_delete()
    {
        //
    }

} // END Controller_Api
