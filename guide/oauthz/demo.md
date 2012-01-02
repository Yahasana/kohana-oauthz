# Understand the demo step by step

Note: before runing these steps, you have to setup the OAuth server. see README

### Getting the client ID from OAuth server ###

+ Access `http://example.com/oauth/index`
+ Login by an valid email address. e.g. `demo@example.com`
+ Register a client ID in the server page. e.g. `client_id: OAL_4D2ACB5280EF8, client_secrect: demo`

### Setting client connection settings ###

 1. Request URIs options,
 
        'oauth-uri'     => 'http://example.com/oauth/code',       // the uri for requesting authorization_code
        'token-uri'     => 'http://example.com/oauth/token',      // the uri for requesting access_token
        'api-uri'       => 'http://example.com/api/',             // the uri for requesting protected resource
        'redirect_uri'  => 'http://my-web-server.com/client/do'   // the uri holded by yourself

 2. Config the OAuth client ID and secrect `config/oauth-client.php` with the `client_id` and `client_secrect` above.
 3. Note: Other options is for future, they are still in clound and waiting for implement.

### Go on, thinking you are the smart user and try to experiement the stupid service ###

+ Access `http://my-web-server.com/client/index`
+ the script in your web site now is connecting to `http://example.com/oauth/code` to get an `authorization_code`.
    this works in background and can NOT feel,even you are smart
+ What you see is `http://example.com/oauth/code?response_type=code&client_id=OA_4bfbc43769917&redirect_uri=http%3A%2F%2Fmy-web-server.com%2Fclient%2Fdo`
+ To approve or not to? approve will grant my-web-server.com steal some protected information, which owned by you in example.com.
    No? okay that is it.

### How these work? ###
 Hmm, I have no more time to complete this answer at this moment. help me please.
