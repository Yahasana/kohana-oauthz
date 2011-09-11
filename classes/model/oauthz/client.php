<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Handle OAuth data storage
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * @see         Model_Oauthz
 * *
 */
class Model_Oauthz_Client extends Model_Oauthz {

    public function get($server_id, $user_id)
    {
        return DB::select('server_id','client_id','client_secret','redirect_uri','scope','secret_type','ssh_key','app_name','app_desc','app_profile','app_purpose','user_id','user_level','enabled','created','modified')
                ->from('t_oauth_clients')
                ->where('server_id', '=', $server_id)
                ->where('user_id','=', $user_id)
                ->execute($this->_db)
                ->current();
    }
    
    public function lookup($client_id)
    {
        return DB::select('*')
            ->from('t_oauth_clients')
            ->where('client_id', '=', $client_id)
            ->execute($this->_db)
            ->current();
    }
    
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
	 *      app_profile: Application Profile: Web Client Application, Native Application, Browser Application, Autonomous clients
	 *      user_id: Ref# from users table
	 *      user_level: diferent client levels have different max request times
	 *      enabled: 0: waiting for system administrator audit; 1: acceptable; 2: ban
	 *      created: create datetime
	 *      modified: modified datetime
	 *
     * @return	mix     array(insert_id, affect_rows) or validate object
     */
    public function append(array $params, $prefix = 'OAL_')
    {
        $params['client_id'] = $prefix.strtoupper(uniqid());

        $valid = Validate::factory($params)
            ->filter(TRUE, 'trim')
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
            'modified'      => array ('range'       => array (0,2147483647)),
        ), $params);

        foreach($rules as $field => $rule)
            foreach($rule as $r => $p)
                $valid->rule($field, $r, $p);
                
        // TODO: redirect_uri MUST NOT include a fragment component.
        // The client MUST NOT include any untrusted third-party scripts in the redirection endpoint
        // response (e.g. third-party analytics, social plug-ins, ad networks) 
        // The client SHOULD NOT include any third-party scripts in the redirection endpoint response.
        
        if($valid->check())
        {
            $valid = $valid->as_array();

            if($this->unique_client($valid['redirect_uri'], $params['user_id']))
            {
                return $valid->error('redirect_uri', 'not unique');
            }

            foreach($valid as $key => $val)
            {
                if($val === '') $valid[$key] = NULL;
            }

            $valid['user_id']       = $params['user_id'];
            $valid['created']       = $_SERVER['REQUEST_TIME'];
            $valid['client_secret'] = sha1($params['client_secret']);

            $query = DB::insert('t_oauth_clients', array_keys($valid))
                ->values(array_values($valid))
                ->execute($this->_db);

            $valid['server_id']     = $query[0];
            $valid['affected_rows'] = $query[1];

            $valid += $params;
        }

        // Validation data, or collection of the errors
        return $valid;
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
	 *      app_profile: Application Profile: Web Client Application, Native Application, Browser Application, Autonomous clients
	 *      user_id: Ref# from users table
	 *      user_level: diferent client levels have different max request times
	 *      enabled: 0: waiting for system administrator audit; 1: acceptable; 2: ban
	 *      created: create datetime
	 *      modified: modified datetime
	 *
     * @return	mix     update rows affect or validate object
     */
    public function update($server_id, array $params)
    {

        if(empty($params['client_secret'])) unset($params['client_secret']);

        $valid = Validate::factory($params)
            ->filter(TRUE, 'trim');

        $rules = array_intersect_key(array (
            'client_id'     => array ('not_empty'   => NULL, 'max_length' => array (128)),
            'client_secret' => array ('max_length' => array (128)),
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
        ), $params);

        foreach($rules as $field => $rule)
            foreach($rule as $r => $p)
                $valid->rule($field, $r, $p);

        if($valid->check())
        {
            $valid = $valid->as_array();

            if($this->unique_client($valid['redirect_uri'], $params['user_id']) === 1)
            {
                unset($valid['redirect_uri']);
            }

            foreach($valid as $key => $val)
            {
                if($val === '') $valid[$key] = NULL;
            }

            $valid['modified']      = $_SERVER['REQUEST_TIME'];
            $valid['affected_rows'] = DB::update('t_oauth_clients')
                ->set($valid)
                ->where('server_id', '=', $server_id)
                ->where('user_id', '=', $params['user_id'])
                ->execute($this->_db);

            $valid += $params;
        }

        // Validation data, or collection of the errors
        return $valid;
    }

    public function delete($server_id, $user_id)
    {
        return ctype_digit($server_id)
            ? DB::delete('t_oauth_clients')
                ->where('server_id', '=', $server_id)
                ->where('user_id','=', $user_id)
                ->execute($this->_db)
            : NULL;
    }

    /**
     * List servers
     *
     * @access	public
     * @param	array	    $params
     * @param	Pagination	$pagination	default [ NULL ] passed by reference
     * @param	boolean	    $calc_total	default [ TRUE ] is needed to caculate the total records for pagination
     * @return	array       array('servers' => data, 'orderby' => $params['orderby'], 'pagination' => $pagination)
     */
    public function lists(array $params, $pagination = NULL, $calc_total = TRUE)
    {
        $pagination instanceOf Pagination OR $pagination = new Pagination;

        $sql = 'FROM `t_oauth_clients` ';

        // Customize where from params
        $sql .= 'WHERE user_id='.$this->_db->quote($params['user_id']);

        // caculte the total rows
        if($calc_total === TRUE)
        {
            $pagination->total_items = $this->_db->query(
                Database::SELECT, 'SELECT COUNT(`server_id`) num_rows '.$sql, FALSE
            )->get('num_rows');

            $data['pagination'] = $pagination;

            if($pagination->total_items === 0)
            {
                $data['servers'] = array();
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
                case 'level':
                    $sql .= ' ORDER BY user_level DESC';
                    break;
                case 'appname':
                    $sql .= ' ORDER BY app_name DESC';
                    break;
                default:
                    $params['orderby'] = 'client_id';
                    $sql .= ' ORDER BY client_id DESC';
                    break;
            }
            $data['orderby'] = $params['orderby'];
        }

        $sql .= " LIMIT {$pagination->offset}, {$pagination->items_per_page}";

        $data['servers'] = $this->_db->query(Database::SELECT, 'SELECT * '.$sql, FALSE);

        return $data;
    }

    protected function unique_client($redirect_uri, $user_id)
    {
        // Check if the username already exists in the database
        return DB::select(array(DB::expr('COUNT(1)'), 'total'))
            ->from('t_oauth_clients')
            ->where('redirect_uri', '=', $redirect_uri)
            ->where('user_id', '=', $user_id)
            ->execute($this->_db)
            ->get('total');
    }

} // END Model_Oauthz_Client
