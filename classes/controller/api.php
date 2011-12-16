<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth controller
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link		http://oalite.com
 * @see         Oauthz_Api
 * *
 */
class Controller_Api extends Oauthz_Api {

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
                $methods[$method] = url::site('/api/'.$method, TRUE);
            }
        }
        $this->request->response = json_encode($methods);
    }

    public function action_get($id = 0)
    {
        $data = array(
            array('Humm', 'Hah'),
            array('Zzz', 'Yaaa'),
            array('Giii', 'Neee')
        );
        $this->request->response = json_encode(isset($data[$id]) ? $data[$id] : array('Hello', 'OAuth', '2.0'));
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
