<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth signature for plaintext
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.com
 * @see         Oauthz_Signature
 * *
 */
class Oauthz_Signature_Bearer extends Oauthz_Signature {

    public static $algorithm = 'BEARER';

    public function build(Oauthz_Token $token)
    {
        $signature = Oauthz::urlencode($token->client_secret);

        if( ! empty($token->token_secret))
        {
            $signature .= '&'.Oauthz::urlencode($token->token_secret);
        }

        return $signature;
    }
    
} // END Oauthz_Signature_Bearer
