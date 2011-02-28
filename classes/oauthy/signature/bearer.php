<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth signature for plaintext
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthy
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauthy_Signature
 * *
 */
class Oauthy_Signature_Bearer extends Oauthy_Signature {

    public static $algorithm = 'BEARER';

    public function build(Oauthy_Token $token)
    {
        $signature = Oauthy::urlencode($token->client_secret);

        if( ! empty($token->token_secret))
        {
            $signature .= '&'.Oauthy::urlencode($token->token_secret);
        }

        return $signature;
    }
    
} // END Oauthy_Signature_Bearer
