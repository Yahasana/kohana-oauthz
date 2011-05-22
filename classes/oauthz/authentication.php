<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth authentication method handlers
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2011 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.com
 * *
 */
abstract class Oauthz_Authentication {
    
    abstract public function headers();
    
    abstract public function authenticate();

} // END Oauthz_Authentication
