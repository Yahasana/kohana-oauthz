<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth signature for HMAC-SHA1
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauth_Signature
 * *
 */
class Oauth2_Signature_Hmac_Sha1 extends Oauth2_Signature {

    public static $algorithm = 'HMAC-SHA1';

    public function build(Oauth2_Token $token)
    {
        $key = Oauth2::urlencode($token->client_secret);

        if( ! empty($token->token_secret))
        {
            $key .= '&'.Oauth2::urlencode($token->token_secret);
        }

        return base64_encode(hash_hmac('sha1', parent::$identifier, $key, TRUE));
    }
    
} // END Oauth_Signature_Hmac_Sha1
