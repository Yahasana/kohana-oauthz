<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth authentication mac method
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2011 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * @see         Oauthz_Authentication
 * *
 */
class Oauthz_Token_Mac extends Oauthz_Authentication {

    public function verfiy($token)
    {
        // Pull the public key ID from the certificate
        $public_key = openssl_get_publickey($token->public_cert);

        // Check the computed signature against the one passed in the query
        $data = openssl_verify($this->identifier, base64_decode($this->signature), $public_key);

        // Release the key resource
        openssl_free_key($public_key);

        return $data === 1;
    }

    // function to parse the http auth header
    public static function parse($digest = NULL)
    {
        $info = FALSE;

        return $info;
    }

    public function rsa_sha1($token)
    {
        // Pull the private key ID from the certificate
        $private_key = openssl_get_privatekey($token->private_cert);

        // Sign using the key
        if(openssl_sign($this->identifier, $this->signature, $private_key) !== TRUE)
        {
            throw new Oauthy_Exception('');
        }

        // Release the key resource
        openssl_free_key($private_key);

        return base64_encode($this->signature);
    }

    public function hmac_sha1($token)
    {
        $key = rawurlencode($token->client_secret);

        if( ! empty($token->token_secret))
        {
            $key .= '&'.rawurlencode($token->token_secret);
        }

        return $this->signature === base64_encode(hash_hmac('sha1', $this->identifier, $key, TRUE));
    }

    /**
     * Normalized request string for signature verify
     *
     * @access  public
     * @param   string    $method
     * @param   string    $uri
     * @param   array     $params
     * @return  string
     */
    public static function identifier($method, $uri, array $params)
    {
        // The signature parameter MUST be excluded.
        unset($params['signature']);

        return $method.'&'.rawurlencode($uri).'&'.http_build_query($params, '', '&');
    }

} // END Oauthz_Token_Mac
