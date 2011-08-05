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
abstract class Oauthz_Authorization {

    protected $_client_id;
    
    protected $_token;

    public static function initialize(array $params)
    {
        switch(Request::$method)
        {
            case 'POST':
                $token_type = Arr::get($_POST, 'token_type');
                break;
            case 'GET':
                $token_type = Arr::get($_GET, 'token_type');
                break;
        }

        if(isset($token_type))
        {
            $token_type = 'Oauthz_Token_'.$token_type;

            if(class_exists($token_type))
            {
                return new $token_type($params);
            }
        }

        throw new Oauthz_Exception_Token('invalid_token');
    }
    
    public function client_id()
    {
        return $this->_client_id;
    }
    
    public function token()
    {
        return $this->_token;
    }

    abstract public function authenticate($client);

} // END Oauthz_Authorization
