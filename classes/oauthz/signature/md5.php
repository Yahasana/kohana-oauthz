<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth signature for MD5
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.com
 * @see         Oauthz_Signature
 * *
 */
class Oauthz_Signature_Md5 extends Oauthz_Signature {

    public static $algorithm = 'MD5';

    public function build(Oauthz_Token $token)
    {
        // TODO
    }

    public function check(Oauthz_Token $token, $signature)
    {
        // TODO
    }
    
} // END Oauthz_Signature_Md5
