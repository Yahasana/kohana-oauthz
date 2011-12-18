<h1>OAuth Test Client</h1>
<p>Note: we don't store any of the information you type in.</p>
<?php 
if($authorized) 
{
    ?>do you want to let the <strong><?php echo $_GET['redirect_uri']; ?></strong> to access your information?
    <br /><br /><a href="<?php echo Oauthz::grant_access_uri('http://docs/oauth/authorize'); ?>" title="">Approve access</a>
    <a href="<?php echo Oauthz::access_denied_uri(); ?>" title="">Deny access</a><?php 
} 
else 
{ 
    ?><form method="POST" name="oauth_client">
    <input type="hidden" name="oauth_consumer_key" value="<?php echo $oauth_consumer_key; ?>" />
    <input type="hidden" name="oauth_token" value="<?php echo $oauth_token; ?>" />
    <input type="hidden" name="oauth_token_secret" value="<?php echo $oauth_token_secret; ?>" />
    <input type="hidden" name="oauth_signature_method" value="<?php echo $oauth_signature_method; ?>" />
    <input type="hidden" name="oauth_nonce" value="<?php echo $oauth_nonce; ?>" />
    <label for="userid"></label><input type="text" name="username" id="userid" value="" />
    <label for="passwd"></label><input type="password" name="passwd" id="passwd" value="" />
    <input type="submit" value="Submit" />
    </form><?php
}