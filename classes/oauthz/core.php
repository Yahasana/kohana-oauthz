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

    public static function grant_access_uri($redirect)
    {
        return $redirect.URL::query();
    }

    public static function access_denied_uri($redirect = NULL)
    {
        if( ! $redirect) $redirect = Arr::get($_GET, 'redirect_uri');
        if( $state = Arr::get($_GET, 'state')) $state = '&state='.$state;
        return $redirect.'?error=access_denied'.$state;
    }

} // END Oauthz Core
