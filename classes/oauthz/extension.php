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

    /**
     * OPTION if the "state" parameter was present in the client authorization request.
     *
     * @access	protected
     * @var		string	$state
     */
    protected $state;

    /**
     * Create grant_type or token_type object
     *
     * @access	public
     * @param	string	$type
     * @param	array	$args
     * @return	mix
     */
    public static function factory($type, array $args)
    {
        $type = 'Oauthz_Extension_'.$type;

        if(class_exists($type))
        {
            return new $type($args);
        }

        return FALSE;
    }

    /**
     * Obtain token
     *
     * @access	public
     * @return	mix
     */
    abstract public function execute();

} // END Oauthz_Extension
