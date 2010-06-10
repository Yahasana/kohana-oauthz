<?php

class Oauth_Parameter_Device extends Oauth_Parameter {

    public function __construct(Model_Oauth $oauth)
    {
        $this->oauth = $oauth;
        $this->client_id = $this->get('client_id');
        $this->redirect_uri = $this->get('redirect_uri');
    }

    public function authorization_check($client)
    {
        $params = array(
            'type'      => 'web_server',
            'client_id' => 'get_from_query',
            'scope'     => '', // OPTIONAL.  The scope of the access token as a list of space-delimited strings.
            'format'    => 'json' // OPTIONAL. "json", "xml", or "form"
        );
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
            'type'      => 'device_token',
            'client_id' => 'get_from_query',
            'code' => $this->post('client_secret'),
            'secret_type' => '',
            'format'    => 'json' // OPTIONAL. "json", "xml", or "form"
        );
        return TRUE;
    }
}
