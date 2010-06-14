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

    protected $_id = 'default';

    public function reg_server($server)
    {
        $server['user_id'] = 3;

        if(isset($server['client_id']) and DB::select('server_id')
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
            $server['client_id'] = 'OA_'.uniqid();
            $server['client_secret'] = $server['pass'];
            $server['secret_type'] = 'plaintext';
            DB::insert('t_oauth_servers')
                ->columns(array(
                    'user_id',
                    'client_id',
                    'client_secret',
                    'redirect_uri',
                    'secret_type'
                ))->values(array(
                    $server['user_id'],
                    $server['client_id'],
                    $server['client_secret'],
                    $server['redirect_uri'],
                    $server['secret_type']
                ))->execute($this->_db);
        }
        return $server['client_id'];
    }

    public function update_server()
    {
        //
    }

    public function lookup_server($client_id, $redirect_uri = NULL)
    {
        //
    }

    public function unique_server($redirect_uri)
    {
        // Check if the username already exists in the database
        return ! DB::select(array(DB::expr('COUNT(server_id)'), 'total'))
            ->from('t_oauth_servers')
            ->where('redirect_uri', '=', $redirect_uri)
            ->where('user_id', '=', $user_id)
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
        // implement me
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
            DB::insert('t_oauth_tokens', array('server_id','code','user_id','access_token','token_secret','timestamp','expire_in','refresh_token'))
                ->values(array($client['server_id'], $client['code'], $client['user_id'], $access_token, $token_secret, date('Y-m-d H:i:s'), 3600, $refresh_token))
                ->execute($this->_db);

            return $client;
        }
        return NULL;
    }

    public function new_code(Oauth_Client $client)
    {
        if($server_id = DB::select('server_id')->from('t_oauth_servers')
            ->where('client_id' , '=', $client->client_id)
            ->where('redirect_uri' , '=', $client->redirect_uri)
            ->execute($this->_db)
            ->get('server_id'))
        {
            $code = Text::random(); // 8 character random string
            $secret = time() + time();
            $token = new OAuth_Token($code, md5(md5($secret)));

            DB::insert('t_oauth_tokens')
                ->columns(array('access_token','token_type','token_secret','server_id'))
                ->values(array($key, 'request', $secret, $server_id))
                ->execute($this->_db);

            // return a new token attached to this client
            return $token;
        }
        return NULL;
    }

    public function lookup_code()
    {
        //
    }

    public function new_token()
    {
        //
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
            return new Oauth_Token($token);
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
