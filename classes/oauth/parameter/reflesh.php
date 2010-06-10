<?php

class Oauth_Parameter_Reflesh extends Oauth_Parameter {

    /**
     * type
     *      REQUIRED.  The parameter value MUST be set to "refresh".
     */
    public $type;

    /**
     * client_id
     *      REQUIRED.  The client identifier as described in Section 2.1.
     */
    public $client_id;

    /**
     * client_secret
     *      REQUIRED if the client was issued a secret.  The client secret.
     */
    public $client_secret;

    /**
     * refresh_token
     *      REQUIRED.  The refresh token associated with the access token to be refreshed.
     */
    public $refresh_token;

    /**
     * format
     *      OPTIONAL.  The response format requested by the client.  Value
     *      MUST be one of "json", "xml", or "form".  Alternatively, the
     *      client MAY use the HTTP "Accept" header field with the desired
     *      media type.  Defaults to "json" if omitted and no "Accept"
     *      header field is present.
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
            'reflesh_token'  => $this->post('code'),
            'secret_type' => '',
            'format'    => 'json' // OPTIONAL. "json", "xml", or "form"
        );
        return TRUE;
    }
}
