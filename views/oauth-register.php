<form action="/server/register" method="post" name="form1" id="form1">
<input type='hidden' name="__v_state__" value='<?php echo md5(microtime()); ?>'/>

<input type='hidden' name="client_id" value='<?php if(isset($client_id)) echo $client_id; ?>'/>
<label for="password">Password for secret request *:</label><input type="text" name="password" id="password" value="" /><sup>&#9786;</sup><?php
    if(isset($errors['password'])) echo $errors['password']; ?><br />

<label for="confirm">Password confirm *:</label><input type="text" name="confirm" id="confirm" value="" /><?php
    if(isset($errors['confirm'])) echo $errors['confirm']; ?><br />

<label for="redirect_uri">Redirect URI *:</label><input type="text" name="redirect_uri" id="redirect_uri" value="<?php if(isset($redirect_uri)) echo $redirect_uri; ?>" /><?php
    if(isset($errors['redirect_uri'])) echo $errors['redirect_uri']; ?><br />

<label for="appname">Application Name *:</label><input id="appname" name="appname" value="" type="text"><br />

<label for="profile">Application Profile *:</label><select id="profile" name="profile">
<option value="webserver">Web Server Application</option>
<option value="native">Native Application</option>
<option value="useragent">Browser Application</option>
</select><br />

<label for="description">Application Description:</label><textarea rows="3" cols="20" id="description" name="description"></textarea><br />

<label for="scope">Scope</label><select id="scope" name="scope" multiple>
<option value="get">Get my</option>
<option value="create">Create me</option>
<option value="update">Update you</option>
<option value="delete">Delete it</option>
</select><br />

<label for="public_cert">SSH Public Key:</label><textarea name="public_cert" id="public_cert"><?php if(isset($public_cert)) echo $public_cert; ?></textarea><?php
    if(isset($errors['public_cert'])) echo $errors['public_cert']; ?><br />

<br />
<label for="Submit"></label><input type="submit" value="Submit" />
</form>

Note: <i>&#9786;, This should be generated from system and updated regularly in some days</i>