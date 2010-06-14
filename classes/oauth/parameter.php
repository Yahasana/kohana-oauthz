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
     * Factory for create different request types' parameter object
     *
     * @access  public
     * @param   string    $type
     * @return  Oauth_Parameter
     */
    public static function factory($type, $flag = FALSE)
    {
        $class = 'Oauth_Parameter_'.$type;
        return class_exists($class) ? new $class($flag) : new stdClass;
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
    abstract public function access_token($client);

    public static $headers = NULL;

    /**
     * This function takes a query like a=b&a=c&d=e and returns the parsed
     *
     * @access    public
     * @param     string    $query
     * @return    array
     */
    public static function parse_query($query = NULL)
    {
        $params = array();

        if($query === NULL) $query = URL::query();

        if( ! empty($query))
        {
            $query = explode('&', $query);

            foreach ($query as $param)
            {
                list($key, $value) = explode('=', $param, 2);
                $params[Oauth::urldecode($key)] = $value !== NULL ? Oauth::urldecode($value) : '';
            }
        }

        return $params;
    }

    /**
     * Build HTTP Query
     *
     * @access  public
     * @param   arra    $params
     * @return  string  HTTP query
     */
    public static function build_query(array $params)
    {
        if (empty($params)) return '';

        $query = '';
        foreach ($params as $key => $value)
        {
            $query .= Oauth::urlencode($key).'='.Oauth::urlencode($value).'&';
        }

        return rtrim($query, '&');
    }

    /**
     * Explode the oauth parameter from $_POST and returns the parsed
     *
     * @access  public
     * @param   string  $query
     * @return  array
     */
    public static function parse_post($post)
    {
        $params = array();

        if (! empty($post))
        {
            if(isset(self::$headers['Content-Type'])
                AND stripos(self::$headers['Content-Type'], 'application/x-www-form-urlencoded') !== FALSE)
            {
                //
            }
        }

        return $params;
    }

    /**
     * helper to try to sort out headers for people who aren't running apache
     *
     * @access  public
     * @return  void
     */
    public static function request_headers()
    {
        if(self::$headers !== NULL) return self::$headers;

        $headers = array();
        if (function_exists('apache_request_headers'))
        {
            foreach(apache_request_headers() as $key => $value)
            {
                $headers[ucwords(strtolower($key))] = $value;
            }
        }

        foreach ($_SERVER as $key => $value)
        {
            if (substr($key, 0, 5) === 'HTTP_')
            {
                $key = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[ucwords($key)] = $value;
            }
        }

        return self::$headers = $headers;
    }

    /**
     * Utility function for turning the Authorization: header into parameters
     * has to do some unescaping
     * Can filter out any non-oauth parameters if needed (default behaviour)
     *
     * @access  public
     * @param   string    $headers
     * @param   string    $oauth_only    default [ TRUE ]
     * @return  array
     */
    public static function parse_header($headers)
    {
        $pattern = '/(([-_a-z]*)=("([^"]*)"|([^,]*)),?)/';
        $offset = 0;
        $params = array();
        if (isset($headers['Authorization']) && substr($headers['Authorization'], 0, 12) === 'Token token=')
        {
            $this->_params = Oauth::parse_header($headers['Authorization']) + $this->_params;
        }
        while (preg_match($pattern, $headers, $matches, PREG_OFFSET_CAPTURE, $offset) > 0)
        {
            $match = $matches[0];
            $header_name = $matches[2][0];
            $header_content = (isset($matches[5])) ? $matches[5][0] : $matches[4][0];
            $params[$header_name] = Oauth::urldecode($header_content);
            $offset = $match[1] + strlen($match[0]);
        }

        if (isset($params['realm']))
        {
            unset($params['realm']);
        }

        return $params;
    }

    public static function build_header(array $params, $realm = '')
    {
        $header ='Authorization: Token token="'.$realm.'"';
        foreach ($params as $key => $value)
        {
            if (is_array($value))
            {
                throw new OAuth_Exception('Arrays not supported in headers');
            }
            $header .= ','.Oauth::urlencode($key).'="'.Oauth::urlencode($value).'"';
        }
        return $header;
    }
}
