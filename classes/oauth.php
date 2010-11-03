<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth helper class
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
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

} // END Oauth
