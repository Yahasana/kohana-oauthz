#OAuth protocal v2 [draft](http://tools.ietf.org/wg/oauth/)

## Server ##

### Install and configuration ###

 1) Create the oauth data tabe by execute `oauth.sql` in doc directory

 2) Configurate the server parameters in `config/oauthy-server.php`

### Modify interface show to users ###

 1) Client have to register an account before they access the protected resources. they can also manage their account

 2) Resource owners personal information management.

    class Controller_Oauth extends Oauthy_Server {}

 See [more](/Yahasana/kohana-oauthy/blob/master/guide/server.md)

### Develop grant type or response type handler extensions ###

 1) Simpler way

    class Oauthy_MyController extends Oauthy_Controller {

        protected function new_grant_type()
        {
            // todo
        }
    }

    class Controller_Oauth extends Oauthy_MyController {}

 2) Complex one, e.g. [assersion](/Yahasana/kohana-oauthy/blob/master/classes/extension/assersion.php)

### Develop web service API protected by OAuth2 ###

    class Controller_Api extends Oauthy_Api {}

 See [more](/Yahasana/kohana-oauthy/blob/master/guide/api.md)

### Overlay standard grant type or response type handler ###

 Not recommend but you can, here is

    class Oauthy_MyController extends Oauthy_Controller {
        protected function code()
        {
            // todo
        }
    }

## Client ##

### Install and configuration ###

 1) Configurate the client parameters in `config/oauthy-client.php`

### Develop resources request handler ###

    class Controller_Client extends Oauthy_Client {}

 See [more](/Yahasana/kohana-oauthy/blob/master/guide/client.md)

### Demo explaination ###

[Demo guide](/Yahasana/kohana-oauthy/blob/master/guide/demo.md)

## ISC License (ISCL) ##

http://www.opensource.org/licenses/isc-license.txt

## Want to donate ? ##

If you want to donate, via [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=42424861@qq.com&item_name=Support Oauthz further development). Thanks!

## Want to be helped and buy me some coffee ? ##

Welcome
