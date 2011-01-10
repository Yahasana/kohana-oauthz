<?php
/**
 * OAuth request parameter handler
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * *
 */
abstract class Oauth_Parameter {

    /**
     * Factory for create different request types' parameter object
     *
     * @access  public
     * @param   string    $type
     * @return  Oauth_Parameter
     */
    public static function factory($type, array $args)
    {
        $class = 'Oauth_Parameter_'.$type;
        return new $class($args);
    }

    public static function headers()
    {
        if(isset($_SERVER['HTTP_AUTHORIZATION']) OR $_SERVER['HTTP_AUTHORIZATION'] = getenv('HTTP_AUTHORIZATION'))
        {
            $params = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
            $data['user'] = $params[0];
            unset($params[0]);
            $data['pass'] = implode('', $params);

            return $data;
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

    // function to parse the http auth header
    public static function parse_digest($txt)
    {
        // protect against missing data
        $needed_parts = array('nonce' => TRUE, 'nc' => TRUE, 'cnonce' => TRUE, 'qop'=> TRUE, 'username' => TRUE, 'uri' => TRUE, 'response' => TRUE);
        $data = array();

        preg_match_all('@(\w+)=(?:(?:\'([^\']+)\'|"([^"]+)")|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

        foreach($matches as $m) 
        {
            $data[$m[1]] = $m[2] ? $m[2] : ($m[3] ? $m[3] : $m[4]);
            unset($needed_parts[$m[1]]);
        }

        return $needed_parts ? FALSE : $data;
    }

    /**
     * Authorization check and populate $client into token
     *
     * @access	public
     * @param	string	$client
     * @return	Oauth_Token
     */
    public function oauth_token($client)
    {
        throw new Oauth_Exception_Token('invalid_request');
    }

    /**
     * Verify the request parameter and populate $client into token
     *
     * @access	public
     * @param	string	$client
     * @return	Oauth_Token
     */
    public function access_token($client)
    {
        throw new Oauth_Exception_Access('invalid_request');
    }

} // END Oauth_Parameter
