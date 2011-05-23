<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth authentication bearer method
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2011 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.com
 * *
 */
abstract class Oauthz_Authorization_Bearer extends Oauthz_Authorization {

    public function authenticate($client_id, $client_secret)
    {
        if($data = Oauthz_Authorization_Bearer::parse())
        {
            // TODO
        }

        return $data;
    }

    public static function parse($digest = NULL)
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION']) 
            AND strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']), 'bearer') === 0)
        {
            $params = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 7)), 2);
            
            // TODO
        }

        return empty($data) ? FALSE : $data;
    }

} // END Oauthz_Authorization_Bearer
