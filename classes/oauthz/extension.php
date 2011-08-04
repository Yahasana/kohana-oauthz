<?php
/**
 * Oauth Extension
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * *
 */
abstract class Oauthz_Extension {

    public static function factory($type, array $args)
    {
        $type = 'Oauthz_Extension_'.$type;

        return class_exists($type) ? new $type($args) : NULL;
    }

    abstract public function execute();

} // END Oauthz_Extension
