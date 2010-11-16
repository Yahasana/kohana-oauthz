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
abstract class Oauth_Core {

    public static $headers = NULL;

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
     * @access  public
     * @param   string	$method
     * @param   string	$identifier
     * @return  object
     * @see     Oauth_Signature::factory
     */
    public static function signature($method, $identifier)
    {
        return Oauth_Signature::factory($method, $identifier);
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
                $params[Oauth::urldecode($key)] = $value !== NULL ? Oauth::urldecode($value) : '';
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
            $query .= Oauth::urlencode($key).'='.Oauth::urlencode($value).'&';
        }

        return rtrim($query, '&');
    }

    /**
     * Explode the oauth parameter from $_POST and returns the parsed
     *
     * @access  public
     * @param   string  $query
     * @return  array
     */
    public static function parse_post($post = NULL)
    {
        $params = array();

        if($post === NULL) $post = $_POST;

        if ( ! empty($post))
        {
            //
        }

        return $params;
    }

    /**
     * Utility function for turning the Authorization: header into parameters
     * has to do some unescaping
     * Can filter out any non-oauth parameters if needed (default behaviour)
     *
     * @access  public
     * @param   string    $headers
     * @param   string    $oauth_only    default [ TRUE ]
     * @return  array
     */
    public static function parse_header()
    {
        $offset = 0;
        $params = array();
        $pattern = '/(([-_a-z]*)=("([^"]*)"|([^,]*)),?)/';

        if (isset($_SERVER['HTTP_AUTHORIZATION']) && substr($_SERVER['HTTP_AUTHORIZATION'], 0, 12) === 'Token token=')
        {
            while(preg_match($pattern, $_SERVER['HTTP_AUTHORIZATION'], $matches, PREG_OFFSET_CAPTURE, $offset) > 0)
            {
                $match = $matches[0];
                $name = $matches[2][0];
                $content = (isset($matches[5])) ? $matches[5][0] : $matches[4][0];
                $params[$name] = Oauth::urldecode($content);
                $offset = $match[1] + strlen($match[0]);
            }
        }

        unset($params['realm']);

        return $params;
    }

    public static function build_header(array $params, $realm = '')
    {
        $header ='Authorization: Token token="'.$realm.'"';
        foreach ($params as $key => $value)
        {
            if (is_array($value))
            {
                throw new OAuth_Exception('Arrays not supported in headers');
            }
            $header .= ','.Oauth::urlencode($key).'="'.Oauth::urlencode($value).'"';
        }
        return $header;
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

    public static function grant_access_uri($redirect)
    {
        return $redirect.URL::query();
    }

    public static function access_denied_uri($redirect = NULL)
    {
        $params = Oauth::parse_query();
        if( ! $redirect) $redirect = Arr::get($params, 'redirect_uri');
        if( $state = Arr::get($params, 'state')) $state = '&state='.$state;
        return $redirect.'?error=access_denied'.$state;
    }

} // END Oauth Core
