<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth signature for plaintext
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauth_Signature
 * *
 */
class Oauth2_Signature_Plaintext extends Oauth2_Signature {

    public static $algorithm = 'PLAINTEXT';

    public function build(Oauth2_Token $token)
    {
        $signature = Oauth2::urlencode($token->client_secret);

        if( ! empty($token->token_secret))
        {
            $signature .= '&'.Oauth2::urlencode($token->token_secret);
        }

        return $signature;
    }
    
} // END Oauth_Signature_Plaintext
