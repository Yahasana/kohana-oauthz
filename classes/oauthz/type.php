<?php
/**
 * OAuth request parameter handler
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.com
 * *
 */
abstract class Oauthz_Type {

    /**
     * Factory for create different request types' parameter object
     *
     * @access  public
     * @param   string    $type
     * @return  Oauthz_Type
     */
    public static function factory($type, array $args)
    {
        $class = 'Oauthz_Type_'.$type;
        return new $class($args);
    }

    public static function headers()
    {
        if(isset($_SERVER['HTTP_AUTHORIZATION']) OR $_SERVER['HTTP_AUTHORIZATION'] = getenv('HTTP_AUTHORIZATION'))
        {
            $params = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)), 2);

            return array('user' => $params[0], 'pass' => $params[1]);
        }
    }

    public static function post()
    {
        // CONTENT_TYPE for application/x-www-form-encoded or multipart/form-data
        if((isset($_SERVER['CONTENT_TYPE']) OR $_SERVER['CONTENT_TYPE'] = getenv('CONTENT_TYPE'))
            AND stripos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') !== FALSE)
        {
            //
        }
    }

    /**
     * Authorization check and populate $client into token
     *
     * @access	public
     * @param	string	$client
     * @return	Oauthz_Token
     */
    public function oauth_token($client)
    {
        throw new Oauthz_Exception_Token('invalid_request');
    }

    /**
     * Verify the request parameter and populate $client into token
     *
     * @access	public
     * @param	string	$client
     * @return	Oauthz_Token
     */
    public function access_token($client)
    {
        throw new Oauthz_Exception_Access('invalid_request');
    }

} // END Oauthz_Type
