<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauthz helper class
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * *
 */
abstract class Oauthz_Core {

    /**
     * OAuth server configuration group name
     *
     * @access	protected
     * @var		string	$_type
     */
    public static $type = 'default';

    public static function config($key = NULL, $type = NULL)
    {
        static $config;

        isset($type) OR $type = Oauthz::$type;

        if( ! isset($config[$type]))
        {
            if( ! $config[$type] = Kohana::config('oauth-server')->get($type))
            {
                throw new Kohana_Exception('There is no ":group" in your config file oauth-server.php'
                    , array(':group' => $type));
            }
        }

        return isset($key) ? isset($config[$type][$key]) ? $config[$type][$key] : NULL : $config[$type];
    }

    /**
     * Oauthz_Signature::factory alias
     *
     * @access  public
     * @param   string	$method
     * @param   string	$identifier
     * @return  object
     * @see     Oauthz_Signature::factory
     */
    public static function signature($method, $identifier)
    {
        return Oauthz_Signature::factory($method, $identifier);
    }

    /**
     * This function takes a query like a=b&a=c&d=e and returns the parsed
     *
     * @access    public
     * @param     string    $query
     * @return    array
     */
    public static function parse_query($query = NULL, $args = NULL)
    {
        $params = array();

        if($query === NULL) $query = ltrim(URL::query(), '?');

        if( ! empty($query))
        {
            $query = explode('&', $query);

            foreach ($query as $param)
            {
                list($key, $value) = explode('=', $param, 2);
                $params[Oauthz::urldecode($key)] = $value !== NULL ? Oauthz::urldecode($value) : '';
            }
        }

        return $args === NULL ? $params : (isset($params[$args]) ? $params[$args] : NULL);
    }

    /**
     * Build HTTP Query
     *
     * @access  public
     * @param   arra    $params
     * @return  string  HTTP query
     */
    public static function build_query(array $params)
    {
        if (empty($params)) return '';

        $query = '';
        foreach ($params as $key => $value)
        {
            $query .= Oauthz::urlencode($key).'='.Oauthz::urlencode($value).'&';
        }

        return rtrim($query, '&');
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
            return array_map(array('Oauthz', 'urldecode'), $item);
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
        static $search  = array('+', '%7E');
        static $replace = array(' ', '~');

        if (is_array($item))
        {
            return array_map(array('Oauthz', 'urlencode'), $item);
        }

        if (is_scalar($item) === FALSE)
        {
            return $item;
        }

        return str_replace($search, $replace, rawurlencode($item));
    }

    public static function grant_access_uri($redirect)
    {
        return $redirect.URL::query();
    }

    public static function access_denied_uri($redirect = NULL)
    {
        $params = Oauthz::parse_query();
        if( ! $redirect) $redirect = Arr::get($params, 'redirect_uri');
        if( $state = Arr::get($params, 'state')) $state = '&state='.$state;
        return $redirect.'?error=access_denied'.$state;
    }

} // END Oauthz Core
