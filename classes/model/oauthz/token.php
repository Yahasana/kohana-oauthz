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
class Model_Oauthz_Token extends Model_Oauthz {

    public function get($token_id)
    {
        return ctype_digit((string) $token_id)
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
    public function code($client_id, $token_type, $expires_in = 120, array $option = NULL)
    {
        if($client_id AND $client = DB::select('client_secret','server_id','redirect_uri','user_id')
            ->from('t_oauth_clients')
            ->where('client_id' , '=', $client_id)
            ->where('enabled' , '=', 1)
            ->execute($this->_db)
            ->current())
        {
            $client['code'] = uniqid();
            $access_token   = sha1(md5($_SERVER['REQUEST_TIME']));
            $refresh_token  = sha1(sha1(mt_rand()));
            $expires_in     = $_SERVER['REQUEST_TIME'] + $expires_in;

            isset($option) AND $option = json_encode($option);

            DB::insert('t_oauth_tokens', array(
                    'client_id',
                    'code',
                    'user_id',
                    'access_token',
                    'timestamp',
                    'refresh_token',
                    'expire_code',
                    'token_type',
                    'option'
                ))
                ->values(array(
                    $client_id,
                    $client['code'],
                    0,                     // TODO the user_id should be the resource owner
                    $access_token,
                    $_SERVER['REQUEST_TIME'],
                    $refresh_token,
                    $expires_in,
                    $token_type,
                    $option
                ))
                ->execute($this->_db);

            $client['expires_in'] = $expires_in;

            return $client;
        }

        return NULL;
    }

    public function token($client_id, $code, $expires_in = 3600)
    {
        if($client = DB::select('server_id','client_secret','redirect_uri','user_id')
            ->from('t_oauth_clients')
            ->where('client_id' , '=', $client_id)
            ->execute($this->_db)
            ->current())
        {
            if(DB::update('t_oauth_tokens')
                ->set(array('expire_token' => $_SERVER['REQUEST_TIME'] + $expires_in))
                ->where('code' => $code)
                ->execute($this->_db))
            {
                $client += (array) DB::select('access_token','token_type','refresh_token'
                        ,array('expire_token', 'expires_in'),'option')
                    ->from('t_oauth_tokens')
                    ->where('client_id' , '=', $client_id)
                    ->where('code' , '=', $code)
                    ->execute($this->_db)
                    ->current();
            }
        }
        return $client;
    }

    public function access_token($client_id, $code, $expires_in = 3600)
    {
        if($token = DB::select('access_token','token_type','refresh_token'
                ,array('expire_token', 'expires_in'),'option')
            ->from('t_oauth_tokens')
            ->where('client_id' , '=', $client_id)
            ->where('code' , '=', $code)
            ->execute($this->_db)
            ->current())
        {
            //
        }

        return $token;
    }

    // TODO
    public function refresh_token($client_id, $token)
    {
        if($token = DB::select('access_token','token_type','refresh_token'
                ,array('expire_token', 'expires_in'),'option')
            ->from('t_oauth_tokens')
            ->where('client_id' , '=', $client_id)
            ->where('token' , '=', $token)
            ->execute($this->_db)
            ->current())
        {
            //
        }

        return $token;
    }

    public function assertion($client_id)
    {
        // TODO
    }

    public function delete($token_id, $client_id)
    {
        return ctype_digit((string) $token_id)
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
