<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth signature base class
 *
 * @author     sumh <oalite@gmail.com>
 * @package    Oauth
 * @copyright  (c) 2009 OALite team
 * @license    http://www.oalite.com/license.txt
 * @version    $id$
 * @link       http://www.oalite.com
 * @since      Available since Release 1.0
 * *
 */
abstract class Oauth_Signature {

    /**
     * The name of the signature method
     *
     * @access  public
     * @var     string    $algorithm
     */
    public static $algorithm;

    /**
     * Base string to build the signature for some method
     *
     * @access  protected
     * @var     string    $base_string
     */
    protected static $base_string  = NULL;

    /**
     * Initialize the signature method's driver
     *
     * @access  public
     * @param   string    $method
     * @param   string    $base_string
     * @throw   Oauth_Exception
     * @return  object    instance of the method's driver
     */
    public static function factory($method, $base_string)
    {
        static $instance;
        if (! isset($instance[$method]))
        {
            $class = 'Oauth_Signature_'.str_replace('-', '_', $method);
            if(class_exists($class))
            {
                $instance[$method] = new $class;
            }
            else
            {
                throw new Oauth_Exception('');
            }
        }
        self::$base_string = $base_string;

        return $instance[$method];
    }

    /**
     * Build a signature from oauth token
     *
     * @access	public
     * @param	Oauth_Token	$token	default [ NULL) ]
     * @return	void
     */
    abstract public function build(Oauth_Token $token);

    /**
     * Check if the request signature corresponds to the one calculated for the request.
     *
     * @access	public
     * @param	Oauth_Token	$token
     * @param	string	    $signature
     * @return	boolean
     */
    public function check(Oauth_Token $token, $signature)
    {
        return $signature === $this->build($token);
    }

    private function __construct()
    {
        // This is a static class
    }

} //END Oauth Signature
