<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth controller
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2009 OALite team
 * @license     http://www.oalite.com/license.txt
 * @version     $id$
 * @link        http://www.oalite.com
 * @see         Oauth_Server_Controller
 * @since       Available since Release 1.0
 * *
 */
class Controller_Api extends Oauth_Controller {

    /**
     * Accessing Protected Resources
     * The request MUST be signed per Signing Requests (Signing Requests),
     * and contains the following parameters:
     * oauth_consumer_key:
     *     The Consumer Key.
     * oauth_token:
     *     The Access Token.
     * oauth_signature_method:
     *     The signature method the Consumer used to sign the request.
     * oauth_signature:
     *     The signature as defined in Signing Requests (Signing Requests).
     * oauth_timestamp:
     *     As defined in Nonce and Timestamp (Nonce and Timestamp).
     * oauth_nonce:
     *     As defined in Nonce and Timestamp (Nonce and Timestamp).
     * oauth_version:
     *     OPTIONAL. If present, value MUST be 1.0. Service Providers MUST assume the protocol version to be 1.0 if this parameter is not present. Service Providers’ response to non-1.0 value is left undefined.
     * Additional parameters:
     *     Any additional parameters, as defined by the Service Provider.
     *
     * @access    public
     * @return    void
     */
    public function action_api()
    {
        try
        {
            print_r(Oauth::request_headers());
        }
        catch (Oauth_Exception $e)
        {
            print($e->getMessage() . "\n<hr />\n");
        }
    }

    public function action_get()
    {
        //
    }

    public function action_post()
    {
        //
    }

    public function action_put()
    {
        //
    }

    public function action_delete()
    {
        //
    }

    public function action_register()
    {
        $data = array(
            'usa_id_ref'    => $this->post('UsaIdRef'),
            'consumer_key'    => $this->post('ConsumerKey'),
            'consumer_secret'    => $this->post('ConsumerSecret'),
            'enabled'    => $this->post('Enabled'),
            'status'    => $this->post('Status'),
            'requester_name'    => $this->post('RequesterName'),
            'requester_email'    => $this->post('RequesterEmail'),
            'callback_uri'    => $this->post('CallbackUri'),
            'app_uri'    => $this->post('AppUri'),
            'app_title'    => $this->post('AppTitle'),
            'app_desc'    => $this->post('AppDesc'),
            'app_notes'    => $this->post('AppNotes'),
            'app_type'    => $this->post('AppType'),
            'app_licence'    => $this->post('AppLicence'),
            'issue_date'    => $this->post('IssueDate'),
            'timestamp'    => $this->post('Timestamp'),
            'signature_method'    => $this->oauth_params['oauth_signature_method'],
        );
        if(!empty($_POST))
        {
            $this->oauth_model->register_server($data);
        }
        $this->render('v_oauth_register', $data);
    }

} //END Controller Oauth
