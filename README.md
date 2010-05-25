#OAuth protocal v2 [draft](http://tools.ietf.org/wg/oauth/)

## Server ##

### Install and configuration ###

 1. create the oauth data tabe by execute `oauth.sql` in doc directory
 2. configurate the server parameters in `config/oauth_server.php`

### Modify interface show to users ###

    class Controller_oauth extends Oauth_Server_Controller {}

### Develop API protected by OAuth ###

    class Controller_Api extends Oauth_Controller {}

## Client ##

### Install and configuration ###

 1. configurate the client parameters in `config/oauth_provider.php`

### Develop resources request handler ###

    class Controller_Client extends Oauth_Client_Controller {}