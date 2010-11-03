<?php defined('SYSPATH') or die('No direct script access.');
    if($user = Session::instance()->get('user'))
    {
        $usermail = $user['mail'];
    }
    else
    {
        $usermail = '';
    }
?><form action="" method="post" name="form1" id="form1">
<input type='hidden' name='__v_state__' value='<?php echo md5(microtime()); ?>'/>
<h3>Login with a valid email address</h3>
<label for="usermail">User Email</label><input type="text" name="usermail" id="usermail" value="<?php echo $usermail; ?>" />
<input type="submit" value="Submit" />
</form>