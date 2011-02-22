<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth signature for HMAC-SHA1
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthy
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauthy_Signature
 * *
 */
class Oauthy_Signature_Hmac_Sha1 extends Oauthy_Signature {

    public static $algorithm = 'HMAC-SHA1';

    public function build(Oauthy_Token $token)
    {
        $key = Oauthy::urlencode($token->client_secret);

        if( ! empty($token->token_secret))
        {
            $key .= '&'.Oauthy::urlencode($token->token_secret);
        }

        return base64_encode(hash_hmac('sha1', parent::$identifier, $key, TRUE));
    }
    
} // END Oauthy_Signature_Hmac_Sha1
