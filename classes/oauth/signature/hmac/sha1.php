<?php defined('SYSPATH') or die('No direct script access.');

class Oauth_Signature_Hmac_Sha1 extends Oauth_Signature {

    public static $algorithm = 'HMAC-SHA1';

    public function build(Oauth_Token $token)
    {
        $key = Oauth::urlencode($token->client_secret);

        if( ! empty($token->token_secret))
        {
            $key .= '&'.Oauth::urlencode($token->token_secret);
        }

        return base64_encode(hash_hmac('sha1', parent::$base_string, $key, TRUE));
    }
}
