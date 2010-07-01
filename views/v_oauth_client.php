<?php defined('SYSPATH') or die('No direct script access.');
    $GI18N = I18n::load(I18n::$lang);
    if(isset($errors)) print_r($errors);
    ?><table><caption>Service list you have register</caption><thead>
    <tr><th>client_id</th><th>redirect_uri</th><th>confirm_type</th><th>client_desc</th><th>OP</th></tr>
    </thead><tbody><?php
    foreach($clients as $row)
    {
    ?><tr><td><?php echo $row['client_id']; ?></td><td><?php echo $row['redirect_uri']; ?></td><td><?php echo $row['confirm_type']; ?></td><td><?php echo $row['client_desc']; ?></td><th>DEL</th></tr>
<?php
    }
?></tbody></table><?php
    if( ! empty($client_id))
    {
?><form action="" method="post" name="form1" id="form1">
<input type='hidden' name='resubmit' value='<?php echo md5(microtime()); ?>'/>
<label for="password">Password for secret request:</label><input type="text" name="password" id="password" value="<?php echo ''; ?>" /><br />
<label for="confirm">Password confirm:</label><input type="text" name="confirm" id="confirm" value="<?php echo ''; ?>" /><br />
    <label for="redirect_uri">Redirect URI:</label><input type="text" name="redirect_uri" id="redirect_uri" value="<?php echo ''; ?>" />
<input type="submit" value="Submit" />
</form><?php
    }
