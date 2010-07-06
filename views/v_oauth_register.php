<form action="/server/register" method="post" name="form1" id="form1">
<input type='hidden' name="__v_state__" value='<?php echo md5(microtime()); ?>'/>
<input type='hidden' name="client_id" value='<?php if(isset($client_id)) echo $client_id; ?>'/>
<label for="password">Password for secret request:</label><input type="text" name="password" id="password" value="" /><?php 
    if(isset($errors['password'])) echo $errors['password']; ?><br />
<label for="confirm">Password confirm:</label><input type="text" name="confirm" id="confirm" value="" /><?php 
    if(isset($errors['confirm'])) echo $errors['confirm']; ?><br />
<label for="redirect_uri">Redirect URI:</label><input type="text" name="redirect_uri" id="redirect_uri" value="<?php if(isset($redirect_uri)) echo $redirect_uri; ?>" /><?php 
    if(isset($errors['redirect_uri'])) echo $errors['redirect_uri']; ?><br />
<label for="scope">Scope</label><input type="text" name="scope" id="scope" value="<?php if(isset($scope)) echo $scope; ?>" /><br />
<label for="public_cert">Public Cert Key:</label><textarea name="public_cert" id="public_cert"><?php if(isset($public_cert)) echo $public_cert; ?></textarea><?php 
    if(isset($errors['public_cert'])) echo $errors['public_cert']; ?><br />
<input type="submit" value="Submit" />
</form>