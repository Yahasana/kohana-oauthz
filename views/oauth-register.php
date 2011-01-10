<?php if(isset($errors)) print_r($errors); ?>
<form action="/server/register" method="post" name="form1" id="form1">
<input type='hidden' name="__v_state__" value="<?php echo md5(microtime()); ?>"/>
<input type='hidden' name="server_id" value="<?php if(isset($server_id)) echo $server_id; ?>" />

<?php if(isset($client_id)) echo '<label for="password">Client ID *:</label><strong>'.$client_id.'</strong><br />'; ?>

<label for="password">Password for secret request *:</label><input type="text" name="client_secret" id="password" value="" /><sup>&#9786;</sup><?php
    if(isset($errors['client_secret'])) echo $errors['client_secret']; ?><br />

<label for="confirm">Password confirm *:</label><input type="text" name="confirm" id="confirm" value="" /><?php
    if(isset($errors['confirm'])) echo $errors['confirm']; ?><br />

<label for="redirect_uri">Redirect URI *:</label><input type="text" name="redirect_uri" id="redirect_uri" value="<?php if(isset($redirect_uri)) echo $redirect_uri; ?>" /><?php
    if(isset($errors['redirect_uri'])) echo $errors['redirect_uri']; ?><br />

<label for="appname">Application Name *:</label><input id="appname" name="app_name" value="<?php if(isset($app_name)) echo $app_name; ?>" type="text">
<?php if(isset($errors['app_name'])) echo $errors['app_name']; ?><br />

<label for="profile">Application Profile *:</label><select id="profile" name="app_profile">
<option value="webserver">Web Server Application</option>
<option value="native">Native Application</option>
<option value="useragent">Browser Application</option>
</select><br />
<label for="purpose">Application Purpose:</label><textarea name="app_purpose" id="purpose"><?php if(isset($app_purpose)) echo $app_purpose; ?></textarea>
<?php if(isset($errors['app_purpose'])) echo $errors['app_purpose']; ?><br />

<br />

<label for="description">Application Description:</label><textarea rows="3" cols="20" id="description" name="app_desc"><?php if(isset($app_desc)) echo $app_desc; ?></textarea>
<?php if(isset($errors['app_desc'])) echo $errors['app_desc']; ?><br />

<label for="scope">Scope</label><select id="scope" name="scope" multiple>
<option value="get">Get my</option>
<option value="create">Create me</option>
<option value="update">Update you</option>
<option value="delete">Delete it</option>
</select>
<?php if(isset($errors['scope'])) echo $errors['scope']; ?><br />

<label for="ssh_key">SSH Public Key:</label><textarea name="ssh_key" id="ssh_key"><?php if(isset($ssh_key)) echo $ssh_key; ?></textarea><?php
    if(isset($errors['ssh_key'])) echo $errors['ssh_key']; ?><br />

<br />
<label for="Submit"></label><input type="submit" value="Submit" />
</form>

Note: <i>&#9786;, This should be generated from system and updated regularly in some days</i>