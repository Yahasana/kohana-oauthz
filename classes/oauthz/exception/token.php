<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth exception for access token request flow
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2011 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * *
 */
class Oauthz_Exception_Token extends Oauthz_Exception {

	public function __construct($message, array $state = NULL, $code = 0)
	{
        $error_description = I18n::get('Token Errors Response');

        $this->error_description = $error_description[$message];

		// Pass the message to the parent
		parent::__construct($message, $state, $code);
	}

} // END Oauthz_Exception_Token
