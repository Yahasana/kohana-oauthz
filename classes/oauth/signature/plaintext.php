<?php defined('SYSPATH') or die('No direct script access.');

class Oauth_Signature_Plaintext extends Oauth_Signature {

    public static $algorithm = 'PLAINTEXT';

    public function build(Oauth_Token $token)
    {
        $signature = Oauth::urlencode($token->client_secret);

        if( ! empty($token->token_secret))
        {
            $signature .= '&'.Oauth::urlencode($token->token_secret);
        }

        return $signature;
    }
}
