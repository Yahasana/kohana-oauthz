<?php

class Oauth_Parameter_Username extends Oauth_Parameter {

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
            'username'  => $this->post('username'),
            'password'  => '',
            'scope'     => '', // OPTIONAL.  The scope of the access token as a list of space-delimited strings.
            'secret_type' => '',
            'format'    => 'json' // OPTIONAL. "json", "xml", or "form"
        );
        return TRUE;
    }
}
