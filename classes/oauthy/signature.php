<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Oauth signature base class
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthy
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * *
 */
abstract class Oauthy_Signature {

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
     * @var     string    $identifier
     */
    protected static $identifier  = NULL;

    /**
     * Initialize the signature method's driver
     *
     * @access  public
     * @param   string    $method
     * @param   string    $identifier
     * @throw   Oauthy_Exception
     * @return  object    instance of the method's driver
     */
    public static function factory($method, $identifier)
    {
        static $instance;

        if ( ! isset($instance[$method]))
        {
            $class = 'Oauthy_Signature_'.str_replace('-', '_', $method);
            if(class_exists($class))
            {
                $instance[$method] = new $class;
            }
            else
            {
                throw new Oauthy_Exception('invalid_algorithm');
            }
        }

        self::$identifier = $identifier;

        return $instance[$method];
    }

    /**
     * Build a signature from oauth token
     *
     * @access	public
     * @param	Oauthy_Token    $token	default [ NULL ]
     * @return	string
     */
    abstract public function build(Oauthy_Token $token);

    /**
     * Check if the request signature corresponds to the one calculated for the request.
     *
     * @access	public
     * @param	Oauthy_Token    $token
     * @param	string	        $signature
     * @return	boolean
     */
    public function check(Oauthy_Token $token, $signature)
    {
        return $signature === $this->build($token);
    }

} // END Oauthy_Signature
