<?php
/**
 * OAuth Client class
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * *
 */
class Oauth_Client {

    public $type = 'webserver';

    public $redirect_uri;

    public $state;

    public $scope;

    public $immediate;

    public $secret_type;

    public $client_id;

    public $client_secret;

    public function __construct($client_id, $client_secret = NULL)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }
    
} // END Oauth_Client
