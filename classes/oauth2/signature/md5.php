<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth signature for MD5
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauth_Signature
 * *
 */
class Oauth2_Signature_Md5 extends Oauth2_Signature {

    public static $algorithm = 'MD5';

    public function build(Oauth2_Token $token)
    {
        // TODO
    }

    public function check(Oauth2_Token $token, $signature)
    {
        // TODO
    }
    
} // END Oauth_Signature_Md5
