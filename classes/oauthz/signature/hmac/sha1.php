<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth signature for HMAC-SHA1
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.com
 * @see         Oauthz_Signature
 * *
 */
class Oauthz_Signature_Hmac_Sha1 extends Oauthz_Signature {

    public static $algorithm = 'HMAC-SHA1';

    public function build(Oauthz_Token $token)
    {
        $key = Oauthz::urlencode($token->client_secret);

        if( ! empty($token->token_secret))
        {
            $key .= '&'.Oauthz::urlencode($token->token_secret);
        }

        return base64_encode(hash_hmac('sha1', parent::$identifier, $key, TRUE));
    }
    
} // END Oauthz_Signature_Hmac_Sha1
