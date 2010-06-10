<?php

class Oauth_Parameter_Username extends Oauth_Parameter {

    /**
     * type
     *      REQUIRED.  The parameter value MUST be set to "username".
     */
    public $type;

    /**
     * client_id
     *      REQUIRED.  The client identifier as described in Section 2.1.
     */
    public $client_id;

    /**
     * client_secret
     *      REQUIRED.  The client secret as described in Section 2.1.
     *      OPTIONAL if no client secret was issued.
     */
    public $client_secret;

    /**
     * scope
     *      OPTIONAL.  The scope of the access request expressed as a list
     *      of space-delimited strings.  The value of the "scope" parameter
     *      is defined by the authorization server.  If the value contains
     *      multiple space-delimited strings, their order does not matter,
     *      and each string adds an additional access range to the
     *      requested scope.
     * format
     *      OPTIONAL.  The response format requested by the client.  Value
     *      MUST be one of "json", "xml", or "form".  Alternatively, the
     *      client MAY use the HTTP "Accept" header field with the desired
     *      media type.  Defaults to "json" if omitted and no "Accept"
     *      header field is present.
     */

    public function __construct($flag = FALSE)
    {
        $this->type = $this->get('type');
        $this->client_id = $this->get('client_id');
        $this->client_secret = $this->get('client_secret');
        if($flag === FALSE)
        {
            // REQUIRED.  The end-user’s username.
            $this->username = $this->get('username');

            // REQUIRED.  The end-user’s password.
            $this->password = $this->get('password');
        }
        $this->scope = $this->get('scope');
        $this->format = $this->get('format');
    }

    public function oauth_token($client)
    {
        $token = new Oauth_Token;

        if($this->format)
        {
            $token->format = $this->format;
        }

        if($client['redirect_uri'] !== $this->redirect_uri)
        {
            $token->error = 'redirect_uri_mismatch';
            return $token;
        }

        if( ! empty($client['scope']) AND ! isset($client['scope'][$this->scope]))
        {
            $token->error = 'unauthorized_client';
            return $token;
        }

        if($this->immediate)
        {
            // TODO
        }

        // Grants Authorization
        $token->code = $client['code'];

        return $token;
    }

    public function access_token($client)
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
