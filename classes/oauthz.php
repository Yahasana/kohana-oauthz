<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth helper class
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * *
 */
abstract class Oauthz extends Oauthz_Core {

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

} // END Oauthz
