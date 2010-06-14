<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth helper class
 *
 * @author     sumh <oalite@gmail.com>
 * @package    Oauth
 * @copyright  (c) 2009 OALite team
 * @license    http://www.oalite.cn/license.txt
 * @version    $id$
 * @link       http://www.oalite.cn
 * @since      Available since Release 1.0
 * *
 */
abstract class Oauth extends Oauth_Core{

    public static function get($key = NULL, $default = NULL)
    {
        if ($key === NULL)
        {
            $default = Request::$method === 'POST' ? $_POST : $_GET;
        }
        else
        {
            $data = Request::$method === 'POST' ? $_POST : $_GET;
            if(isset($data[$key])) $default = $data[$key];
        }
        return $default;
    }
}
