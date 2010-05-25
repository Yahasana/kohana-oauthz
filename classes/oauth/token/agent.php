<?php

class Oauth_Token_ extends Oauth_Token {

    public function load()
    {

    }

    public function check($client)
    {
        if(! $tmp = $this->get('redirect_uri') or $tmp != $client['redirect_uri'])
            return 'redirect_uri_mismatch';
        else
            return TRUE;
    }
}
