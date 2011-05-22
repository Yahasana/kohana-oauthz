<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth authentication digest method
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2011 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.com
 * *
 */
abstract class Oauthz_Authentication_Digest extends Oauthz_Authentication {

    public function headers()
    {

    }

    public function authenticate($client_id, $client_secret)
    {
        if($data = Oauthz_Authentication_Digest::parse() AND $data['username'] === $client_id)
        {
            // generate the valid response
            $A1 = md5($client_id.':'.$realm.':'.$client_secret);
            $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);

            $data = $data['response'] === md5("$A1:$data['nonce']:$data['nc']:$data['cnonce']:$data['qop']:$A2");
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
            elseif ((isset($_SERVER['HTTP_AUTHENTICATION']) OR $_SERVER['HTTP_AUTHORIZATION'] = getenv('HTTP_AUTHORIZATION'))
                AND strpos(strtolower($_SERVER['HTTP_AUTHENTICATION']), 'digest') === 0)
            {
                $digest = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
            }
        }

        $info = FALSE;

        if($digest)
        {
            // protect against missing data
            $fields = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop'=> 1, 'username' => 1, 'uri' => 1, 'response' => 1);

            preg_match_all('@(\w+)=(?:(?:\'([^\']+)\'|"([^"]+)")|([^\s,]+))@', $digest, $matches, PREG_SET_ORDER);

            $data = array();

            foreach($matches as $m)
            {
                $data[$m[1]] = $m[2] ? $m[2] : ($m[3] ? $m[3] : $m[4]);
                unset($fields[$m[1]]);
            }

            empty($fields) AND $info = $data;
        }

        return $info;
    }

} // END Oauthz_Authentication_Digest
