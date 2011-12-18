<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth authentication digest method
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2011 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * @see         Oauthz_Authentication
 * *
 */
class Oauthz_Token_Digest extends Oauthz_Authentication {

    public function verfiy($token)
    {
        if($data = static::parse() AND $data['username'] === $token['client_id'])
        {
            // generate the valid response
            $realm = md5("$token['client_id']:$data['realm']:$token['client_secret']");
            $method = md5("$_SERVER['REQUEST_METHOD']:$data['uri']");

            $data = $data['response'] === md5("$realm:$data['nonce']:$data['nc']:$data['cnonce']:$data['qop']:$method");
        }

        return $data;
    }

    // function to parse the http auth header
    public static function parse($digest = NULL)
    {
        if($digest === NULL)
        {
            // mod_php
            if (isset($_SERVER['PHP_AUTH_DIGEST']))
            {
                $digest = $_SERVER['PHP_AUTH_DIGEST'];
            }
            // most other servers
            elseif (isset($_SERVER['HTTP_AUTHORIZATION'])
                AND strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']), 'digest') === 0)
            {
                $digest = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
            }
        }

        $info = FALSE;

        if($digest)
        {
            // protect against missing data
            $fields = array(
                'nonce'     => 1,
                'nc'        => 1,
                'cnonce'    => 1,
                'qop'       => 1,
                'username'  => 1,
                'uri'       => 1,
                'response'  => 1,
                'realm'     => 1
            );

            preg_match_all('@('.implode('|', array_keys($fields)).')=[\'"]?([^\'",]+)@', $digest, $matches, PREG_SET_ORDER);

            $data = array();

            foreach($matches as $match)
            {
                $data[$match[1]] = $match[2] ? $match[2] : ($match[3] ?: $match[4]);
                unset($fields[$match[1]]);
            }

            empty($fields) AND $info = $data;
        }

        return $info;
    }

} // END Oauthz_Token_Digest
