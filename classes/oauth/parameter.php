<?php

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

    public function headers()
    {
        // CONTENT_TYPE for application/x-www-form-encoded or multipart/form-data
        // $_SERVER['HTTP_AUTHORIZATION']
        // $auth_params = explode(":" , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
        if(isset($_SERVER['HTTP_AUTHORIZATION']) OR $_SERVER['HTTP_AUTHORIZATION'] = getenv('HTTP_AUTHORIZATION'))
        {
            $params = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
            $data['user'] = $params[0];
            unset($params[0]);
            $data['pass'] = implode('', $params);

            return $data;
        }
    }

    function post()
    {
        if((isset($_SERVER['CONTENT_TYPE']) OR $_SERVER['CONTENT_TYPE'] = getenv('CONTENT_TYPE'))
            AND stripos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') !== FALSE)
        {
            //
        }
    }

    // function to parse the http auth header
    function parse_digest($txt)
    {
        // protect against missing data
        $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
        $data = array();

        preg_match_all('@(\w+)=(?:(?:\'([^\']+)\'|"([^"]+)")|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

        foreach($matches as $m) {
            $data[$m[1]] = $m[2] ? $m[2] : ($m[3] ? $m[3] : $m[4]);
            unset($needed_parts[$m[1]]);
        }

        return $needed_parts ? false : $data;
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
