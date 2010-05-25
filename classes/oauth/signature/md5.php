<?php defined('SYSPATH') or die('No direct script access.');

class Oauth_Signature_Md5 extends Oauth_Signature {

    public static $algorithm = 'MD5';

    public function build(Oauth_Token $token)
    {
        //
    }

    public function check(Oauth_Token $token, $signature)
    {
        //
    }
}
