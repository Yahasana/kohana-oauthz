<html>
<head>
<title>OAuth Test Client</title>
</head>
<body>
<div><a href="/server">server</a> | <a href="/client">client</a></div>
<h1>OAuth Test Client</h1>
<h2>Instructions for Use</h2>
<p>Note: we don't store any of the information you type in.</p>
<?php if($authorized) {
    $redirect_uri = Oauth::parse_query($query, 'redirect_uri');
?>do you want to let the <?php echo $redirect_uri; ?> to access your information?
<br /><a href="http://docs/oauth/access<?php echo $query; ?>" title="">Approve access</a>
<a href="<?php echo $redirect_uri; ?>?error=user_denied" title="">Deny access</a>
<?php } else { ?>
<form method="POST" name="oauth_client">
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
?></body>
</html>