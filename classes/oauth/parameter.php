<?php

abstract class Oauth_Parameter {

    /**
     * if the "state" parameter was present in the client authorization request.
     *
     * @access    public
     * @var    string    $state
     */
    public $state;

    /**
     * If the token request is invalid or unauthorized
     *
     * @access    public
     * @var    string    $error
     */
    public $error;

    /**
     * factory for create different request types' parameter object
     *
     * @author  sumh <oalite@gmail.com>
     * @date    2010-05-14 15:03:15
     * @access  public
     * @param   string    $type
     * @return  void
     */
    public static function factory($type)
    {
        $class = 'Oauth_Parameter_'.$type;
        return class_exists($class) ? new $class : NULL;
    }

    abstract public function authorization_check($client);

    abstract public function access_token_check($client);

    public function get($key = NULL, $default = NULL)
    {
        if ($key === NULL)
        {
            $default = Request::$method === 'POST' ? $_POST : $_GET;
        }
        else
        {
            $data = Request::$method === 'POST' ? $_POST : $_GET;
            if(isset($data[$key])) $default = $data[$key];
        }
        return $default;
    }
}
