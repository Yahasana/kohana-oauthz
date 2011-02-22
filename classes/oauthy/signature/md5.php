<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth signature for MD5
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthy
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauthy_Signature
 * *
 */
class Oauthy_Signature_Md5 extends Oauthy_Signature {

    public static $algorithm = 'MD5';

    public function build(Oauthy_Token $token)
    {
        // TODO
    }

    public function check(Oauthy_Token $token, $signature)
    {
        // TODO
    }
    
} // END Oauthy_Signature_Md5
