<?php
/**
 * OAuth request parameter handler
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * *
 */
abstract class Oauthz_Type {

    /**
     * Factory for create different request types' parameter object
     *
     * @access  public
     * @param   string  $type
     * @param   array   $args
     * @return  Oauthz_Type
     */
    public static function factory($type, array $args)
    {
        $class = 'Oauthz_Type_'.$type;
        return new $class($args);
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
     * Authorization check and populate $client into authorization token
     *
     * @access	public
     * @param	array	$client
     * @return	Oauthz_Token
     * @throw   Oauthz_Exception_Token    Error Codes: invalid_request
     */
    public function oauth_token($client)
    {
        throw new Oauthz_Exception_Token('invalid_request');
    }

    /**
     * Verify the request parameter and populate $client into access token
     *
     * @access	public
     * @param	array	$client
     * @return	Oauthz_Token
     * @throw   Oauthz_Exception_Access    Error Codes: invalid_request
     */
    public function access_token($client)
    {
        throw new Oauthz_Exception_Access('invalid_request');
    }

} // END Oauthz_Type
