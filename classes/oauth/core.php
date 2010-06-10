<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth helper class
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2009 OALite team
 * @license     http://www.oalite.com/license.txt
 * @version     $id$
 * @link        http://www.oalite.com
 * @since       Available since Release 1.0
 * *
 */
abstract class Oauth_Core {

    /**
     * Normalized request string for signature verify
     *
     * @access  public
     * @param   string    $method
     * @param   string    $uri
     * @param   array     $params
     * @return  string
     */
    public static function normalize($method, $uri, array $params)
    {
        // ~ The oauth_signature parameter MUST be excluded.
        unset($params['signature']);

        return $method.'&'.Oauth::urlencode($uri).'&'.Oauth::build_query($params);
    }
    
    /**
     * Oauth_Signature::factory alias
     *
     * @see     Oauth_Signature::factory
     * @access  public
     * @param   string	$method
     * @param   string	$base_string
     * @return  object
     */
    public static function signature($method, $base_string)
    {
        return Oauth_Signature::factory($method, $base_string);
    }

    /**
     * URL Decode
     *
     * @param   mixed   $item Item to url decode
     * @return  string  URL decoded string
     */
    public static function urldecode($item)
    {
        if (is_array($item))
        {
            return array_map(array('Oauth', 'urldecode'), $item);
        }

        return rawurldecode($item);
    }

    /**
     * URL Encode
     *
     * @param   mixed $item string or array of items to url encode
     * @return  mixed url encoded string or array of strings
     */
    public static function urlencode($item)
    {
        static $search = array('+', '%7E');
        static $replace = array(' ', '~');

        if (is_array($item))
        {
            return array_map(array('Oauth', 'urlencode'), $item);
        }

        if (is_scalar($item) === FALSE)
        {
            return $item;
        }

        return str_replace($search, $replace, rawurlencode($item));
    }
    
    private function __construct()
    {
        // This is a static class
    }

} //END Oauth
