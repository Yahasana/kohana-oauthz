<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Handle OAuth data storage
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Kohana_Model
 * *
 */
class Model_Oauth extends Kohana_Model {

    protected $_db = 'default';

    /**
     * Insert server
     *
     * @access	public
     * @param	array	$params
	 *      client_id: AKA. API key
	 *      client_secret: AKA. API secret
	 *      redirect_uri: AKA. Callback URI
	 *      scope: May be create, read, update or delete. so on so for
	 *      secret_type: Secret signature encrypt type. e.g
	 *      ssh_key: SSH public keys
	 *      app_name: Application Name
	 *      app_desc: Application Description, When users authenticate via your app, this is what they'll see.
	 *      app_profile: Application Profile: Web Server Application, Native Application, Browser Application, Autonomous clients
	 *      user_id: Ref# from users table
	 *      user_level: diferent client levels have different max request times
	 *      enabled: 0: waiting for system administrator audit; 1: acceptable; 2: ban
	 *      created: create datetime
	 *      modified: modified datetime
	 *
     * @return	mix     insert_id or validate object
     */
    public function reg_server($params, $prefix = 'OAL_')
    {
        $params['client_id'] = $prefix.strtoupper(uniqid());

        $valid = Validate::factory($params)
			->rule('client_id', 'not_empty')
			->rule('client_secret', 'not_empty')
			->rule('redirect_uri', 'not_empty')
			->rule('app_name', 'not_empty');

        $rules = array_intersect_key(array (
            'client_id'     => array ('max_length'  => array (128)),
            'client_secret' => array ('max_length'  => array (128)),
            'redirect_uri'  => array ('max_length'  => array (512)),
            'scope'         => array ('max_length'  => array (256)),
            'secret_type'   => array ('in_array'    => array (array ('plaintext','md5','rsa-sha1','hmac-sha1')),),
            'ssh_key'       => array ('max_length'  => array (512)),
            'app_name'      => array ('max_length'  => array (128)),
            'app_desc'      => array ('max_length'  => array (65535)),
            'app_profile'   => array ('in_array'    => array (array ('webserver','native','useragent','autonomous')),),
            'app_purpose'   => array ('max_length'  => array (512)),
            'user_level'    => array ('range'       => array (0,255)),
            'enabled'       => array ('range'       => array (0,255)),
            'created'       => array ('range'       => array (0,4294967295)),
            'modified'      => array ('range'       => array (0,2147483647)),
        ), $params);

        foreach($rules as $field => $rule)
            foreach($rule as $r => $p)
                $valid->rule($field, $r, $p);

        if($valid->check())
        {
            $valid = $valid->as_array();

            foreach($valid as $key => $val)
            {
                if($val === '') $valid[$key] = NULL;
            }

            $valid['created'] = $_SERVER['REQUEST_TIME'];
            $valid['client_secret'] = sha1($valid['client_secret']);
            $server = DB::insert('t_oauth_servers', array_keys($valid))
                ->values(array_values($valid))
                ->execute($this->_db);

            return $server[0];
        }
        else
        {
            // Validation failed, collect the errors
            return $valid;
        }
    }

    /**
     * Update server
     *
     * @access	public
     * @param	int	    $server_id
     * @param	array	$params
	 *      client_id: AKA. API key
	 *      client_secret: AKA. API secret
	 *      redirect_uri: AKA. Callback URI
	 *      scope: May be create, read, update or delete. so on so for
	 *      secret_type: Secret signature encrypt type. e.g
	 *      ssh_key: SSH public keys
	 *      app_name: Application Name
	 *      app_desc: Application Description, When users authenticate via your app, this is what they'll see.
	 *      app_profile: Application Profile: Web Server Application, Native Application, Browser Application, Autonomous clients
	 *      user_id: Ref# from users table
	 *      user_level: diferent client levels have different max request times
	 *      enabled: 0: waiting for system administrator audit; 1: acceptable; 2: ban
	 *      created: create datetime
	 *      modified: modified datetime
	 *
     * @return	mix     update rows affect or validate object
     */
    public function update_server($server_id, array $params)
    {
        $valid = Validate::factory($params);

        $rules = array_intersect_key(array (
            'client_id'     => array ('not_empty'   => NULL, 'max_length' => array (128)),
            'client_secret' => array ('not_empty'   => NULL, 'max_length' => array (128)),
            'redirect_uri'  => array ('not_empty'   => NULL, 'max_length' => array (512)),
            'scope'         => array ('max_length'  => array (256)),
            'secret_type'   => array ('in_array'    => array (array ('plaintext','md5','rsa-sha1','hmac-sha1')),),
            'ssh_key'       => array ('max_length'  => array (512)),
            'app_name'      => array ('not_empty'   => NULL, 'max_length' => array (128)),
            'app_desc'      => array ('max_length'  => array (65535)),
            'app_profile'   => array ('in_array'    => array (array ('webserver','native','useragent','autonomous')),),
            'app_purpose'   => array ('max_length'  => array (512)),
            'user_level'    => array ('range'       => array (0,255)),
            'enabled'       => array ('range'       => array (0,255)),
            'created'       => array ('not_empty'   => NULL, 'range' => array (0,4294967295)),
            'modified'      => array ('range'       => array (0,2147483647)),
        ), $params);

        foreach($rules as $field => $rule)
            foreach($rule as $r => $p)
                $valid->rule($field, $r, $p);

        if($valid->check())
        {
            $valid = $valid->as_array();

            foreach($valid as $key => $val)
            {
                if($val === '') $valid[$key] = NULL;
            }

            $valid['modified'] = $_SERVER['REQUEST_TIME'];
            isset($valid['client_secret']) AND $valid['client_secret'] = sha1($valid['client_secret']);
            return DB::update('t_oauth_servers')
                ->set($valid)
                ->where('server_id', '=', $server_id)
                ->where('user_id','=', $valid['user_id'])
                ->execute($this->_db);
        }
        else
        {
            // Validation failed, collect the errors
            return $valid;
        }
    }

    /**
     * Insert client
     *
     * @access	public
     * @param	array	$params
	 *      confirm_type: Request confirm, 0: every time; 1: only once; 2: with expired period; 3: once and banned
	 *      client_level: diferent client levels have different max request times
	 *      expired_date: date time
	 *
     * @return	mix     insert_id or validate object
     */
    public function reg_client(array $params)
    {
        if(isset($params['expired_date']) AND $expired_date = strtotime($params['expired_date']))
            $params['expired_date'] = $expired_date;
        else
            unset($params['expired_date']);

        $valid = Validate::factory($params)
			->rule('client_id', 'not_empty')
			->rule('redirect_uri', 'not_empty');

        $rules = array_intersect_key(array (
            'client_id'     => array ('max_length'  => array (128)),
            'redirect_uri'  => array ('max_length'  => array (512)),
            'confirm_type'  => array ('range'       => array (0,255)),
            'client_level'  => array ('range'       => array (0,255)),
            'modified'      => array ('range'       => array (0,4294967295)),
            'created'       => array ('range'       => array (0,4294967295)),
            'scope'         => array ('max_length'  => array (512)),
            'expired_date'  => array ('range'       => array (0,4294967295)),
            'client_desc'   => array ('max_length'  => array (65535)),
        ), $params);

        foreach($rules as $field => $rule)
            foreach($rule as $r => $p)
                $valid->rule($field, $r, $p);

        if($valid->check())
        {
            $valid = $valid->as_array();

            foreach($valid as $key => $val)
            {
                if($val === '') $valid[$key] = NULL;
            }

            $valid['created'] = $_SERVER['REQUEST_TIME'];
            return DB::insert('t_oauth_clients', array_keys($valid))
                ->values(array_values($valid))
                ->execute($this->_db);
        }
        else
        {
            // Validation failed, collect the errors
            return $valid;
        }
    }

    /**
     * Update client
     *
     * @access	public
     * @param	int	    $user_id
     * @param	array	$params
	 *      confirm_type: Request confirm, 0: every time; 1: only once; 2: with expired period; 3: once and banned
	 *      client_level: diferent client levels have different max request times
	 *      expired_date: date time
	 *
     * @return	mix     update rows affect or validate object
     */
    public function update_client()
    {
        if(isset($params['expired_date']) AND $expired_date = strtotime($params['expired_date']))
            $params['expired_date'] = $expired_date;
        else
            unset($params['expired_date']);

        $valid = Validate::factory($params);

        $rules = array_intersect_key(array (
            'client_id'     => array ('not_empty'   => NULL, 'max_length' => array (128)),
            'redirect_uri'  => array ('not_empty'   => NULL, 'max_length' => array (512)),
            'confirm_type'  => array ('range'       => array (0,255)),
            'client_level'  => array ('range'       => array (0,255)),
            'modified'      => array ('range'       => array (0,4294967295)),
            'created'       => array ('range'       => array (0,4294967295)),
            'scope'         => array ('max_length'  => array (512)),
            'expired_date'  => array ('range'       => array (0,4294967295)),
            'client_desc'   => array ('max_length'  => array (65535)),
        ), $params);

        foreach($rules as $field => $rule)
            foreach($rule as $r => $p)
                $valid->rule($field, $r, $p);

        if($valid->check())
        {
            $valid = $valid->as_array();

            foreach($valid as $key => $val)
            {
                if($val === '') $valid[$key] = NULL;
            }

            $valid['modified'] = $_SERVER['REQUEST_TIME'];
            return DB::update('t_oauth_clients')
                ->set($valid)
                ->where('user_id', '=', $user_id)
                ->execute($this->_db);
        }
        else
        {
            // Validation failed, collect the errors
            return $valid;
        }
    }

    /**
     * List clients
     *
     * @access	public
     * @param	array	    $params
     * @param	Pagination	$pagination	default [ NULL ] passed by reference
     * @param	boolean	    $calc_total	default [ TRUE ] is needed to caculate the total records for pagination
     * @return	array       array('clients' => data, 'orderby' => $params['orderby'], 'pagination' => $pagination)
     */
    public function list_client(array $params, & $pagination = NULL, $calc_total = TRUE)
    {
        if( ! $pagination instanceOf Pagination) $pagination = new Pagination;

        $sql = 'FROM `t_oauth_clients` ';

        // Customize where from params
        //$sql .= 'WHERE ... '

        // caculte the total rows
        if($calc_total === TRUE)
        {
            $pagination->total_items = $this->_db->query(Database::SELECT,
                'SELECT COUNT(`user_id`) num_rows '.$sql, FALSE
            )->get('num_rows');

            $data['pagination'] = $pagination;

            if($pagination->total_items == 0)
            {
                $data['clients'] = array();
                isset($params['orderby']) AND $data['orderby'] = $params['orderby'];
                return $data;
            }
        }

        // Customize order by from params
        if(isset($params['orderby']))
        {
            switch($params['orderby'])
            {
                case 'uri':
                    $sql .= ' ORDER BY redirect_uri DESC';
                    break;
                case 'expire':
                    $sql .= ' ORDER BY expired_date DESC';
                    break;
                case 'update':
                    $sql .= ' ORDER BY modified DESC';
                    break;
                case 'level':
                    $sql .= ' ORDER BY user_level DESC';
                    break;
                default:
                    $params['orderby'] = 'redirect_uri';
                    $sql .= ' ORDER BY redirect_uri DESC';
                    break;
            }
            $data['orderby'] = $params['orderby'];
        }

        $sql .= " LIMIT {$pagination->offset}, {$pagination->items_per_page}";

        $data['clients'] = $this->_db->query(Database::SELECT, 'SELECT * '.$sql, FALSE);

        return $data;
    }

    public function lookup_code($code)
    {
        if($code AND $token = DB::select('token_id','client_id','code','nonce','access_token','timestamp','refresh_token','expire_in')
            ->from('t_oauth_tokens')
            ->where('code' , '=', $code)
            ->execute($this->_db)
            ->current())
        {
            $client = DB::select('client_id','redirect_uri','client_secret','secret_type')
                ->from('t_oauth_servers')
                ->where('client_id' , '=', $token['client_id'])
                ->where('enabled' , '=', TRUE)
                ->execute($this->_db)
                ->current();

            if($token['timestamp'] > $_SERVER['REQUEST_TIME'] - 60)
            {
                DB::update('t_oauth_tokens')
                    ->set(array('code' => uniqid()))
                    ->where('token_id' , '=', $token['token_id'])
                    ->execute($this->_db);

                return $token + $client;
            }
        }
        return NULL;
    }

    public function audit_token($token)
    {
        DB::insert('t_oauth_audits', array('access_token'))
            ->values(array($token->access_token))
            ->execute($this->_db);
    }

    public function refresh_token(Oauth_Token $token)
    {
        DB::delete('t_oauth_tokens')
            ->where('token', '=', $token->key)
            ->execute($this->_db);
    }

    public function lookup_nonce($client, $token, $nonce, $timestamp)
    {
        // implement me
        if($secret = DB::select('*')
            ->from('t_oauth_server_nonces')
            ->where('token' , '=', serialize($token))
            ->where('nonce' , '=', $nonce)
            ->where('timestamp' , '=', $timestamp)
            ->execute($this->_db)
            ->current())
        {
            return TRUE;
        }
        return FALSE;
    }

} // END Model Oauth
