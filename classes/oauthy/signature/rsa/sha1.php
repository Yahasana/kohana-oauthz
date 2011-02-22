<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth signature for RSA-SHA1
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthy
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauthy_Signature
 * *
 */
class Oauthy_Signature_Rsa_Sha1 extends Oauthy_Signature {

    public static $algorithm = 'RSA-SHA1';

    public function build(Oauthy_Token $token)
    {
        // Pull the private key ID from the certificate
        $private_key = openssl_get_privatekey($token->private_cert);

        // Sign using the key
        if(openssl_sign(parent::$identifier, $signature, $private_key) !== TRUE)
        {
            throw new Oauthy_Exception('');
        }

        // Release the key resource
        openssl_free_key($private_key);

        return base64_encode($signature);
    }

    public function check(Oauthy_Token $token, $signature)
    {
        // Pull the public key ID from the certificate
        $public_key = openssl_get_publickey($token->public_cert);

        // Check the computed signature against the one passed in the query
        $ok = openssl_verify(parent::$identifier, base64_decode($signature), $public_key);

        // Release the key resource
        openssl_free_key($public_key);

        return $ok === 1;
    }
    
} // END Oauthy_Signature_Rsa_Sha1
