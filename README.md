#OAuth protocal v2 [draft](http://tools.ietf.org/wg/oauth/)

## Server ##

### Install and configuration ###

 1) Create the oauth data tabe by execute `oauth.sql` in doc directory

 2) Configurate the server parameters in `config/Oauthz-server.php`

### Modify interface show to users ###

 1) Client have to register an account before they access the protected resources. they can also manage their account

 2) Resource owners personal information management.

    class Controller_Oauth extends Oauthz_Server {}

 See [more](/Yahasana/kohana-Oauthy/blob/master/guide/server.md)

### Develop grant type or response type handler extensions ###

 1) Simpler way `myextension.php`

    class Oauthz_Extension_MyExtension extends Oauthz_Extension {

        public function execute()
        {
            // todo
        }
    }

  then drop this file into the right place

 2) Complex one, e.g. [assersion](/Yahasana/kohana-Oauthy/blob/master/classes/extension/assersion.php)

### Develop web service API protected by OAuth2 ###

    class Controller_Api extends Oauthz_Api {}

 See [more](/Yahasana/kohana-Oauthy/blob/master/guide/api.md)

### Overlay standard grant type or response type handler ###

 Not recommend but you can, here is

    class Oauthz_MyController extends Oauthz_Controller {
        protected function code()
        {
            // todo
        }
    }

## Client ##

### Install and configuration ###

 1) Configurate the client parameters in `config/Oauthz-client.php`

### Develop resources request handler ###

    class Controller_Client extends Oauthz_Client {}

 See [more](/Yahasana/kohana-Oauthy/blob/master/guide/client.md)

### Demo explaination ###

[Demo guide](/Yahasana/kohana-Oauthy/blob/master/guide/demo.md)

## ISC License (ISCL) ##

http://www.opensource.org/licenses/isc-license.txt

## Want to donate ? ##

To support this project, via [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=42424861@qq.com&item_name=Support Oauthz further development). Thanks!

## Want to be helped and buy me some coffee ? ##

Welcome
