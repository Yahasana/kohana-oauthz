#Kohana OAuth 2 Module ![project status](http://stillmaintained.com/Yahasana/kohana-oauthy.png)#

#OAuth protocal v2 [draft 22](http://tools.ietf.org/wg/oauth/)

### Requirement ###
 1) php v5.2+, curl extension to run the demo

 2) MySQL, SQLite, MSSQL or NOSQL database

 3) Kohana [v3.0.x](http://dev.kohanaframework.org/attachments/download/1649/kohana-3.0.10.zip), I have no plan to support v3.1+ ATM

 4) Apache, Nignx, a web server

## Server ##

### Install and configuration ###

 1) Create the oauth data tabe by execute `oauth.sql` in doc directory

 2) Configurate the server parameters in `config/oauthz-server.php`

### Modify interface show to users ###

 1) Client have to register an account before they access the protected resources. they can also manage their account

 2) Resource owners personal information management.
 
```php
class Controller_Oauth extends Oauthz_Server {

}
```
 See [more](/Yahasana/kohana-Oauthy/blob/master/guide/oauthz/server.md)

### Develop grant type or response type handler extensions ###

 1) Simpler way `myextension.php`
 
```php
class Oauthz_Extension_MyExtension extends Oauthz_Extension {

    public function execute()
    {
        // todo
    }
}
```
  then drop this file into the right place

 2) Complex one, e.g. [assersion](/Yahasana/kohana-Oauthy/blob/master/classes/oauthz/extension/assersion.php)

### Develop web service API protected by OAuth2 ###

```php
class Controller_Api extends Oauthz_Api {

}
```
 See [more](/Yahasana/kohana-Oauthy/blob/master/guide/oauthz/api.md)

### Overlay standard grant type or response type handler ###

 Not recommend but you can, here is
 
```php
class Oauthz_MyController extends Oauthz_Controller {

    protected function code()
    {
        // todo
    }
}
```
## Client ##

### Install and configuration ###

 1) Configurate the client parameters in `config/oauthz-client.php`

### Develop resources request handler ###
 
```php
class Controller_Client extends Oauthz_Client {

}
```
 See [more](/Yahasana/kohana-Oauthy/blob/master/guide/oauthz/client.md)

### Demo explaination ###

[Demo at oalite.com](http://oalite.com/oauth)

[User guide](/Yahasana/kohana-Oauthy/blob/master/guide/oauthz/demo.md)

## ISC License (ISCL) ##

http://www.opensource.org/licenses/isc-license.txt

## Want to donate ? ##

To support this project, via [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=42424861@qq.com&item_name=Support Oauthz further development). Thanks!

## Want to be helped and buy me some coffee ? ##

Welcome
