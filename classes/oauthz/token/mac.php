<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth authentication mac method
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2011 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * @see         Oauthz_Authorization
 * *
 */
class Oauthz_Token_Mac extends Oauthz_Authorization {

    public function authenticate($client)
    {
        // TODO
    }

    // function to parse the http auth header
    public static function parse($digest = NULL)
    {
        if ($digest === NULL AND isset($_SERVER['HTTP_AUTHORIZATION']) AND strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']), 'mac') === 0)
        {
            $digest = substr($_SERVER['HTTP_AUTHORIZATION'], 4);
        }

        $info = FALSE;

        if($digest)
        {
            // protect against missing data
            $fields = array('id' => 1, 'nonce' => 1, 'bodyhash' => 1, 'ext'=> 1, 'mac' => 1);

            preg_match_all('@('.implode('|', array_keys($fields)).')=[\'"]?([^\'",]+)@', $digest, $matches, PREG_SET_ORDER);

            $data = array();

            foreach($matches as $m)
            {
                $data[$m[1]] = $m[2] ? $m[2] : ($m[3] ? $m[3] : $m[4]);
                unset($fields[$m[1]]);
            }

            unset($fields['bodyhash'], $fields['ext']);

            empty($fields) AND $info = $data;
        }

        return $info;
    }

    // Issue "mac" OAuth Access Token Type
    public function access_token()
    {
        // TODO
    }

} // END Oauthz_Token_Mac
