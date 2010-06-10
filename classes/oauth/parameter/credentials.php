<?php

class Oauth_Parameter_Credentials extends Oauth_Parameter {

    /**
     * type
     *     REQUIRED.  The parameter value MUST be set to "client_credentials".
     */
    public $type;

    /**
     * client_id
     *     REQUIRED.  The client identifier as described in Section 2.1.
     */
    public $client_id;

    /**
     * client_secret
     *     REQUIRED.  The client secret as described in Section 2.1.
     */
    public $client_secret;

    /**
     * scope
     *     OPTIONAL.  The scope of the access request expressed as a list
     *     of space-delimited strings.  The value of the "scope" parameter
     *     is defined by the authorization server.  If the value contains
     *     multiple space-delimited strings, their order does not matter,
     *     and each string adds an additional access range to the
     *     requested scope.
     * format
     *     OPTIONAL.  The response format requested by the client.  Value
     *     MUST be one of "json", "xml", or "form".  Alternatively, the
     *     client MAY use the HTTP "Accept" header field with the desired
     *     media type.  Defaults to "json" if omitted and no "Accept"
     *     header field is present.
     */

    public function __construct(Model_Oauth $oauth)
    {
        $this->oauth = $oauth;
        $this->client_id = $this->get('client_id');
        $this->redirect_uri = $this->get('redirect_uri');
    }

    public function authorization_check($client)
    {
        if(! $tmp = $this->get('redirect_uri') or $tmp != $client['redirect_uri'])
            return $this->error = 'redirect_uri_mismatch';
        else if($format = $this->get('format') and Kohana::config('oalite_server.default')->format[$format] === TRUE)
        {
            $this->format = $format;
        }

        return TRUE;
    }

    public function access_token_check($client)
    {
        $params = array(
            'type'      => 'web_server',
            'client_id' => 'get_from_query',
            'client_secret' => $this->post('client_secret'),
            'scope'     => '', // OPTIONAL.  The scope of the access token as a list of space-delimited strings.
            'secret_type' => '',
            'format'    => 'json' // OPTIONAL. "json", "xml", or "form"
        );
        return TRUE;
    }
}
