<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Handle OAuth data storage
 *
 * @author     sumh <oalite@gmail.com>
 * @package    Oauth
 * @copyright  (c) 2009 OALite team
 * @license    http://www.oalite.com/license.txt
 * @version    $id$
 * @link       http://www.oalite.com
 * @see        Kohana_Model
 * @since      Available since Release 1.0
 * *
 */
class Model_Oauth extends Kohana_Model {

    protected $_db = 'default';

    public function reg_server($server, $prefix = 'OAL_')
    {
        $server['user_id'] = 3;

        if(isset($server['client_id']) AND DB::select('server_id')
            ->from('t_oauth_servers')
            ->where('client_id','=', $server['client_id'])
            ->where('user_id','=', $server['user_id'])
            ->execute($this->_db)
            ->current())
        {
            DB::update('t_oauth_servers')
                ->set($server)
                ->where('client_id','=', $server['client_id'])
                ->where('user_id','=', $server['user_id'])
                ->execute($this->_db);
        }
        else
        {
            $server['client_id'] = $prefix.uniqid();
            $server['client_secret'] = $server['pass'];
            $server['secret_type'] = 'plaintext';
            DB::insert('t_oauth_servers', array(
                    'user_id',
                    'client_id',
                    'client_secret',
                    'redirect_uri',
                    'secret_type',
                    'scope',
                    'public_cert'
                ))->values(array(
                    $server['user_id'],
                    $server['client_id'],
                    sha1($server['client_secret']),
                    $server['redirect_uri'],
                    $server['secret_type'],
                    $server['scope'],
                    $server['public_cert']
                ))->execute($this->_db);
        }
        return $server['client_id'];
    }

    public function update_server()
    {
        //
    }

    public function lookup_server($client_id)
    {
        //
    }

    public function unique_server($redirect_uri, $user_id = NULL)
    {
        // Check if the username already exists in the database
        return ! DB::select(array(DB::expr('COUNT(1)'), 'total'))
            ->from('t_oauth_servers')
            ->where('redirect_uri', '=', $redirect_uri)
            //->where('user_id', '=', $user_id)
            ->execute($this->_db)
            ->get('total');
    }

    public function list_server($user_id)
    {
        return DB::select('*')->from('t_oauth_servers')
            ->where('user_id', '=', $user_id)
            ->execute($this->_db);
    }

    public function reg_client()
    {
        //
    }

    public function update_client()
    {
    }

    public function lookup_client($client_id)
    {
        if($client_id  AND $client = DB::select('client_secret','server_id','redirect_uri','user_id')
            ->from('t_oauth_servers')
            ->where('client_id' , '=', $client_id)
            ->execute($this->_db)
            ->current())
        {
            $client['code'] = uniqid();
            $access_token = sha1(md5(time()));
            $token_secret = md5(sha1(time()));
            $refresh_token = sha1(sha1(mt_rand()));
            DB::insert('t_oauth_tokens', array('client_id','code','user_id','access_token','token_secret','timestamp','expire_in','refresh_token'))
                ->values(array($client_id, $client['code'], $client['user_id'], $access_token, $token_secret, date('Y-m-d H:i:s'), 3600, $refresh_token))
                ->execute($this->_db);

            return $client;
        }
        return NULL;
    }

    public function list_client($user_id)
    {
        return DB::select('*')->from('t_oauth_clients')
            ->where('user_id', '=', $user_id)
            ->execute($this->_db);
    }

    public function lookup_code($code)
    {
        if($code  AND $token = DB::select('token_id', 'client_id', 'code', 'nonce',
            'access_token','token_secret','timestamp','refresh_token','expire_in')
            ->from('t_oauth_tokens')
            ->where('code' , '=', $code)
            ->execute($this->_db)
            ->current())
        {
            if(strtotime($token['timestamp']) > time() - 60)
            {
                DB::update('t_oauth_tokens')
                    ->set(array('code' => uniqid()))
                    ->where('token_id' , '=', $token['token_id'])
                    ->execute($this->_db);
                return $token;
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

    public function lookup_token($oauth_token)
    {
        // implement me
        $token = DB::select('*')
            ->from('t_oauth_tokens')
            ->where('access_token' , '=', $oauth_token)
            ->execute($this->_db)
            ->current();
        if($token)
        {
            return $token;
        }

        return NULL;
    }

    public function reflesh_token(Oauth_Token $token)
    {
        DB::delete('t_oauth_tokens')
            ->where('token', '=', $token->key)
            ->execute($this->_db);
    }

    public function lookup_nonce($client, $token, $nonce, $timestamp)
    {
        // implement me
        $secret = DB::select('*')
            ->from('t_oauth_server_nonces')
            ->where('token' , '=', serialize($token))
            ->where('nonce' , '=', $nonce)
            ->where('timestamp' , '=', $timestamp)
            ->execute($this->_db)
            ->current();
        if($secret)
        {
            return TRUE;
        }
        return FALSE;
    }

    public function new_request_token(Oauth_Client $client)
    {
        if($server_id = DB::select('server_id')
            ->from('t_oauth_servers')
            ->where('client_id' , '=', $client->client_id)
            ->execute($this->_db)
            ->get('server_id'))
        {
            // return a new token attached to this client
            $key = md5(time());
            $secret = time() + time();
            $token = new OAuth_Token($key, md5(md5($secret)));

            DB::insert('t_oauth_tokens')
                ->columns(array('token','token_type','token_secret','server_id'))
                ->values(array($key, 'request', $secret, $server_id))
                ->execute($this->_db);

            return $token;
        }
        return NULL;
    }

    public function new_access_token(Oauth_Token $token)
    {
        $key = md5(time());
        $secret = time() + time();
        $new_token = new OAuth_Token($key, md5(md5($secret)));

        if(DB::update('t_oauth_tokens')
            ->set(array('token' => $key, 'token_type' => 'access', 'token_secret' => $secret))
            ->where('token', '=', $token->key)
            ->where('token_type', '=', 'request')
            ->execute($this->_db))
            return $new_token;

        return NULL;
    }

} //END Model Oauth
