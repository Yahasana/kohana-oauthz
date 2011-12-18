<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth authorization method handlers
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2011 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * *
 */
abstract class Oauthz_Authentication {

    protected $_client_id;

    protected $_token;

    public static function factory($token_type, array $config, array $params)
    {
        $token_type = 'Oauthz_Token_'.$token_type;

        $oauth = new $token_type;

        foreach($config as $key => $val)
        {
            if($val === TRUE)
            {
                if(isset($params[$key]) AND $value = trim($params[$key]))
                {
                    $oauth->$key = $value;
                }
                else
                {
                    throw new Oauthz_Exception_Token('invalid_token', self::state($params));
                }
            }
            elseif($val !== FALSE)
            {
                $oauth->$key = $val;
            }
        }

        return $oauth;
    }

    public function client_id()
    {
        return $this->_client_id;
    }

    public function token()
    {
        return $this->access_token;
    }

    public static function state($params)
    {
        // Parse the "state" paramter
        if(isset($params['state']) AND ($state = trim($params['state'])))
        {
            $param = array('state' => $state);
        }
        else
        {
            $param = NULL;
        }

        return $param;
    }

    abstract public function authenticate($client);

} // END Oauthz_Authentication
