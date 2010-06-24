<?php

abstract class Oauth_Parameter {

    /**
     * Factory for create different request types' parameter object
     *
     * @access  public
     * @param   string    $type
     * @return  Oauth_Parameter
     */
    public static function factory($type, $args = NULL)
    {
        $class = __CLASS__.'_'.$type;
        return class_exists($class) ? new $class($args) : new stdClass;
    }

    /**
     * Authorization check and populate $client into token
     *
     * @access	public
     * @param	string	$client
     * @return	Oauth_Token
     */
    abstract public function oauth_token($client);

    /**
     * Verify the request parameter and populate $client into token
     *
     * @access	public
     * @param	string	$client
     * @return	Oauth_Token
     */
    public function access_token($client)
    {
        return new Oauth_Token;
    }
}
