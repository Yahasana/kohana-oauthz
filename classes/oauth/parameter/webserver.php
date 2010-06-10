<?php

class Oauth_Parameter_Webserver extends Oauth_Parameter {

    /**
     * type
     *     REQUIRED.  The parameter value MUST be set to "web_server".
     */
    public $type;

    /**
     * client_id
     *     REQUIRED.  The client identifier as described in Section 2.1.
     */
    public $client_id;

    /**
     * client_secret
     *     REQUIRED if the client identifier has a matching secret.  The
     *     client secret as described in Section 2.1.
     */
    public $client_secret;

    /**
     * code
     *     REQUIRED.  The verification code received from the
     *     authorization server.
     */
    public $code;

    /**
     * redirect_uri
     *     REQUIRED.  The redirection URI used in the initial request.
     */
    public $redirect_uri;

    /**
     * format
     *     OPTIONAL.  The response format requested by the client.  Valu
     *     MUST be one of "json", "xml", or "form".  Alternatively, the
     *     client MAY use the HTTP "Accept" header field with the desire
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

        if($tmp = $this->get('state'))
            $this->state = $tmp;

        // OPTIONAL.  The scope of the access token as a list of space-delimited strings.
        if($tmp = $this->get('scope'))
            $this->scope = $tmp;

        if($tmp = $this->get('immediate'))
            $this->immediate = $tmp;

        return TRUE;
    }

    public function access_token_check($client)
    {
        $params = array(
            'type'      => 'web_server',
            'client_id' => 'get_from_query',
            'client_secret' => $this->post('client_secret'),
            'code'  => $this->post('code'),
            'redirect_uri'  => '',
            'secret_type' => '',
            'format'    => 'json' // OPTIONAL. "json", "xml", or "form"
        );
        if($tmp = $this->get('secret_type'))
        {
            $base_string = URL::base(FALSE, TRUE).$this->request->uri;
            $base_string = Oauth::normalize('GET', $base_string, $params);

            if (! Oauth_Signature::factory($tmp, $base_string)->check($token, $signature))
            {
                return $params['redirect_uri'].'#error=bad_verification_code';
            }
            $params['secret_type'] = $tmp;
        }
        return TRUE;
    }
}
