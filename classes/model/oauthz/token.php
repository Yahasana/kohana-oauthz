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
    public function code($client_id, $token_type, $expires_in = 120, array $options = NULL)
    {
        if($client_id AND $client = DB::select('client_secret','server_id','redirect_uri','user_id')
            ->from('t_oauth_clients')
            ->where('client_id' , '=', $client_id)
            ->where('enabled' , '=', 1)
            ->execute($this->_db)
            ->current())
        {
            // Initial code, access_token, refresh_token at the same time
            $client['code'] = uniqid();
            $expires_in     = $_SERVER['REQUEST_TIME'] + $expires_in;

            isset($options) AND $options = json_encode($options);

            DB::insert('t_oauth_tokens', array(
                    'client_id',
                    'code',
                    'user_id',
                    'timestamp',
                    'expire_code',
                    'token_type',
                    'options'
                ))
                ->values(array(
                    $client_id,
                    $client['code'],
                    0,                     // TODO the user_id should be the resource owner
                    $_SERVER['REQUEST_TIME'],
                    $expires_in,
                    $token_type,
                    $options
                ))
                ->execute($this->_db);

            $client['token_type'] = $token_type;
            $client['expires_in'] = $expires_in;

            return $client;
        }

        return NULL;
    }

    /**
     * Get the access_token and expire authorization_code
     *
     * @access	public
     * @param	string	$client_id
     * @param	string	$code
     * @param	int 	$expires_in	default [ 3600 ]
     * @return	mix
     */
    public function token($client_id, $code, $expires_in = 3600)
    {
        if($client = DB::select('server_id','client_secret','redirect_uri','user_id')
            ->from('t_oauth_clients')
            ->where('client_id' , '=', $client_id)
            ->execute($this->_db)
            ->current())
        {
            if($token = DB::select('token_id','access_token','token_type','refresh_token'
                    ,array('expire_token', 'expires_in'),'options','expire_code')
                ->from('t_oauth_tokens')
                ->where('client_id' , '=', $client_id)
                ->where('code' , '=', $code)
                ->execute($this->_db)
                ->current())
            {
                if($token['expire_code'] >= $_SERVER['REQUEST_TIME'])
                {
                    // Start access_token expire time counter
                    $token['expires_in']    = $_SERVER['REQUEST_TIME'] + $expires_in;

                    // Generate access_token
                    $token['access_token']  = sha1(md5($_SERVER['REQUEST_TIME']));

                    // Generate refresh_token
                    $token['refresh_token'] = sha1(sha1(mt_rand()));

                    // Update the expire timestamp of access_token to newest AND expire the code
                    DB::update('t_oauth_tokens')
                        ->set(array(
                            'expire_code'   => 0,
                            'expire_token'  => $token['expires_in'],
                            'access_token'  => $token['access_token'],
                            'refresh_token' => $token['refresh_token']
                        ))
                        ->where('token_id', '=', $token['token_id'])
                        ->execute($this->_db);

                    // Don't expose these
                    unset($token['token_id'], $token['expire_code']);

                    $client += $token;
                }
                elseif($token['expire_code'] == 0)
                {
                    // revoke all tokens previously issued based on that authorization code.
                    DB::update('t_oauth_tokens')
                        ->set(array(
                            'expire_code'    => 0,
                            'expire_token'   => 0,
                            'expire_refresh' => 0
                        ))
                        ->where('token_id', '=', $token['token_id'])
                        ->execute($this->_db);
                }
            }
        }

        return isset($client['access_token']) ? $client : NULL;
    }

    public function access_token($token)
    {
        if($token = DB::select('access_token','token_type','refresh_token'
                ,array('expire_token', 'expires_in'),'options')
            ->from('t_oauth_tokens')
            ->where('access_token', '=', $token)
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
                ,array('expire_token', 'expires_in'),'options')
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
