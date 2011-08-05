<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth authorization method handlers
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2011 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * *
 */
interface Oauthz_Authorization {

    public function authenticate($client_id, $client_secret);

} // END Oauthz_Authorization
