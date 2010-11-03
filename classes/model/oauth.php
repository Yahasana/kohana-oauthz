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

    public function reg_server($server, $prefix = 'OAL_')
    {
        $valid = Validate::factory($server)
            ->rule('client_id', 'not_empty')
            ->rule('user_id', 'not_empty');
        if($valid->check())
        {
            if(DB::select('server_id')
                ->from('t_oauth_servers')
                ->where('client_id','=', $server['client_id'])
                ->where('user_id','=', $server['user_id'])
                ->execute($this->_db)
                ->count())
            {
                DB::update('t_oauth_servers')
                    ->set($server)
                    ->where('client_id','=', $server['client_id'])
                    ->where('user_id','=', $server['user_id'])
                    ->execute($this->_db);
            }
        }
        elseif($valid->rule('password', 'not_empty')
            ->rule('redirect_uri', 'not_empty')
            ->rule('secret_type', 'not_empty')
            ->rule('public_cert', 'not_empty')
            ->check())
        {
            $server['client_id'] = $prefix.strtoupper(uniqid());
            $server['client_secret'] = $server['password'];
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

    public function update_server($server)
    {
        $data = array(
            //'user_id'       => $server['user_id'],
            //'client_id'     => $server['client_id'],
            //'redirect_uri'  => $server['redirect_uri'],
            'scope'         => $server['scope'],
            'public_cert'   => $server['public_cert']
        );
        if( ! empty($server['client_secret'])) 
        {
            $data['client_secret'] = sha1($server['client_secret']);
        }
        
        return DB::update('t_oauth_servers')
            ->set($data)
            ->where('client_id','=', $server['client_id'])
            ->where('user_id','=', $server['user_id'])
            ->execute($this->_db);
    }

    public function lookup_server($client_id, $user_id)
    {
        return DB::select('*')
            ->from('t_oauth_servers')
            ->where('client_id', '=', $client_id)
            ->where('user_id','=', $user_id)
            ->execute($this->_db)
            ->current();
    }

    public function unique_server($redirect_uri, $user_id = NULL)
    {
        // Check if the username already exists in the database
        return DB::select(array(DB::expr('COUNT(1)'), 'total'))
            ->from('t_oauth_servers')
            ->where('redirect_uri', '=', $redirect_uri)
            //->where('user_id', '=', $user_id)
            ->execute($this->_db)
            ->get('total');
    }

    public function list_server($user_id)
    {
        return DB::select('*')
            ->from('t_oauth_servers')
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

    public function lookup_client($client_id, $expired_in = 3600)
    {
        if($client_id AND $client = DB::select('client_secret','server_id','redirect_uri','user_id')
            ->from('t_oauth_servers')
            ->where('client_id' , '=', $client_id)
            ->execute($this->_db)
            ->current())
        {
            $client['code'] = uniqid();
            $access_token = sha1(md5(time()));
            $refresh_token = sha1(sha1(mt_rand()));
            DB::insert('t_oauth_tokens', array('client_id','code','user_id','access_token','timestamp','expire_in','refresh_token'))
                ->values(array($client_id, $client['code'], $client['user_id'], $access_token, time(), $expired_in, $refresh_token))
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
                
            if($token['timestamp'] > time() - 60)
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

    public function refresh_token(Oauth_Token $token)
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

} // END Model Oauth
