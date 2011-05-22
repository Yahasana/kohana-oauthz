<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Handle OAuth data storage
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.com
 * @see         Oauthz_Model
 * *
 */
class Model_Oauthz_Token extends Oauthz_Model {

    public function get($token_id)
    {
        return ctype_digit($token_id)
            ? DB::select('token_id','client_id','code','access_token','refresh_token','expires_in','timestamp','nonce','user_id')
                ->from('t_oauth_tokens')
                ->where('token_id', '=', $token_id)
                ->execute($this->_db)
                ->current()
            : NULL;
    }

    /**
     * Insert token
     *
     * @access	public
     * @param	array	$params
	 *      user_id: Ref# from users table
	 *
     * @return	array     token
     */
    public function code($client_id, $expires_in = 3600)
    {
        if($client_id AND $client = DB::select('client_secret','server_id','redirect_uri','user_id')
            ->from('t_oauth_servers')
            ->where('client_id' , '=', $client_id)
            ->where('enabled' , '=', 1)
            ->execute($this->_db)
            ->current())
        {
            $client['code'] = uniqid();
            $access_token   = sha1(md5($_SERVER['REQUEST_TIME']));
            $refresh_token  = sha1(sha1(mt_rand()));

            DB::insert('t_oauth_tokens', array('client_id','code','user_id','access_token','timestamp','expires_in','refresh_token'))
                ->values(array($client_id, $client['code'], $client['user_id'], $access_token, $_SERVER['REQUEST_TIME'], $expires_in, $refresh_token))
                ->execute($this->_db);

            return $client;
        }

        return NULL;
    }

    public function oauth_token($client_id, $code, $expires_in = 3600)
    {        
        if($client = DB::select('server_id','client_secret','redirect_uri','user_id')
            ->from('t_oauth_servers')
            ->where('client_id' , '=', $client_id)
            ->execute($this->_db)
            ->current())
        {
            $client += (array) DB::select('*')
                ->from('t_oauth_tokens')
                ->where('client_id' , '=', $client_id)
                ->where('code' , '=', $code)
                //->where('timestamp' , '>=', $_SERVER['REQUEST_TIME'] - $expires_in)
                ->execute($this->_db)
                ->current();
        }
        return $client;
    }

    public function access_token($client_id, $code, $expires_in = 3600)
    {
        return DB::select('*')
            ->from('t_oauth_tokens')
            ->where('client_id' , '=', $client_id)
            ->where('code' , '=', $code)
            //->where('timestamp' , '>=', $_SERVER['REQUEST_TIME'] - $expires_in)
            ->execute($this->_db)
            ->current();
    }

    // TODO
    public function refresh_token(Oauthz_Token $token)
    {
        DB::delete('t_oauth_tokens')
            ->where('token', '=', $token->key)
            ->execute($this->_db);
    }

    public function assertion($client_id)
    {
        // TODO
    }

    /**
     * Update token
     *
     * @access	public
     * @param	int	    $token_id
     * @param	array	$params
	 *      user_id: Ref# from users table
	 *
     * @return	mix     update rows affect or validate object
     */
    public function update($token_id, array $params)
    {
        $valid = Validate::factory($params);

        $rules = array_intersect_key(array (
            'client_id'     => array ('not_empty'   => NULL, 'max_length' => array (128)),
            'code'          => array ('not_empty'   => NULL, 'max_length' => array (128)),
            'access_token'  => array ('not_empty'   => NULL, 'max_length' => array (64)),
            'refresh_token' => array ('max_length'  => array (64)),
            'expires_in'    => array ('range'       => array (0,4294967295)),
            'timestamp'     => array ('not_empty'   => NULL, 'range' => array (0,4294967295)),
            'nonce'         => array ('max_length'  => array (64)),
            'user_id'       => array ('not_empty'   => NULL,),
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

            return DB::update('t_oauth_tokens')
                ->set($valid)
                ->where('token_id', '=', $token_id)
                ->execute($this->_db);
        }
        else
        {
            // Validation failed, collect the errors
            return $valid;
        }
    }

    public function delete($token_id, $client_id)
    {
        return ctype_digit($token_id)
            ? DB::delete('t_oauth_tokens')
                ->where('token_id', '=', $token_id)
                ->where('client_id', '=', $client_id)
                ->execute($this->_db)
            : NULL;
    }

    /**
     * List tokens
     *
     * @access	public
     * @param	array	    $params
     * @param	Pagination	$pagination	default [ NULL ] passed by reference
     * @param	boolean	    $calc_total	default [ TRUE ] is needed to caculate the total records for pagination
     * @return	array       array('tokens' => data, 'orderby' => $params['orderby'], 'pagination' => $pagination)
     */
    public function lists(array $params, $pagination = NULL, $calc_total = TRUE)
    {
        $pagination instanceOf Pagination OR $pagination = new Pagination;

        $sql = 'FROM `t_oauth_tokens` ';

        // Customize where from params
        //$sql .= 'WHERE ... '

        // caculte the total rows
        if($calc_total === TRUE)
        {
            $pagination->total_items = $this->_db->query(
                Database::SELECT, 'SELECT COUNT(`token_id`) num_rows '.$sql, FALSE
            )->get('num_rows');

            $data['pagination'] = $pagination;

            if($pagination->total_items === 0)
            {
                $data['tokens'] = array();
                isset($params['orderby']) AND $data['orderby'] = $params['orderby'];
                return $data;
            }
        }

        // Customize order by from params
        if(isset($params['orderby']))
        {
            switch($params['orderby'])
            {
                case 'client':
                    $sql .= ' ORDER BY client_id DESC';
                    break;
                case 'user':
                    $sql .= ' ORDER BY user_id DESC';
                    break;
                default:
                    $params['orderby'] = 'timestamp';
                    $sql .= ' ORDER BY timestamp DESC';
                    break;
            }
            $data['orderby'] = $params['orderby'];
        }

        $sql .= " LIMIT {$pagination->offset}, {$pagination->items_per_page}";

        $data['tokens'] = $this->_db->query(Database::SELECT, 'SELECT * '.$sql, FALSE);

        return $data;
    }

} // END Model_Oauthz_Token
