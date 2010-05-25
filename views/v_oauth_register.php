<form action="/oauth/register" method="post" enctype="multipart/form-data" name="form1" id="form1">
<input type='hidden' name='resubmit' value='<?php echo md5(microtime()); ?>'/><fieldset>
<legend><h3>t_customers</h3></legend>
<table class="action"><tr>
<th><label for="usaIdRef">UsaIdRef</label></th>
<td><input id="usaIdRef" name="UsaIdRef" value="<?php echo $usa_id_ref; ?>" type="text" /></td>

<th><label for="consumerKey">ConsumerKey</label></th>
<td><input id="consumerKey" name="ConsumerKey" value="<?php echo $consumer_key; ?>" type="text" /></td>
</tr><tr>
<th><label for="consumerSecret">ConsumerSecret</label></th>
<td><input id="consumerSecret" name="ConsumerSecret" value="<?php echo $consumer_secret; ?>" type="text" /></td>

<th><label for="enabled">Enabled</label></th>
<td><input id="enabled" name="Enabled" value="<?php echo $enabled; ?>" type="text" /></td>
</tr><tr>
<th><label for="status">Status</label></th>
<td><input id="status" name="Status" value="<?php echo $status; ?>" type="text" /></td>

<th><label for="requesterName">RequesterName</label></th>
<td><input id="requesterName" name="RequesterName" value="<?php echo $requester_name; ?>" type="text" /></td>
</tr><tr>
<th><label for="requesterEmail">RequesterEmail</label></th>
<td><input id="requesterEmail" name="RequesterEmail" value="<?php echo $requester_email; ?>" type="text" /></td>

<th><label for="callbackUri">CallbackUri</label></th>
<td><input id="callbackUri" name="CallbackUri" value="<?php echo $callback_uri; ?>" type="text" /></td>
</tr><tr>
<th><label for="appUri">AppUri</label></th>
<td><input id="appUri" name="AppUri" value="<?php echo $app_uri; ?>" type="text" /></td>

<th><label for="appTitle">AppTitle</label></th>
<td><input id="appTitle" name="AppTitle" value="<?php echo $app_title; ?>" type="text" /></td>
</tr><tr>
<th><label for="appDesc">AppDesc</label></th>
<td><input id="appDesc" name="AppDesc" value="<?php echo $app_desc; ?>" type="text" /></td>

<th><label for="appNotes">AppNotes</label></th>
<td><input id="appNotes" name="AppNotes" value="<?php echo $app_notes; ?>" type="text" /></td>
</tr><tr>
<th><label for="appType">AppType</label></th>
<td><input id="appType" name="AppType" value="<?php echo $app_type; ?>" type="text" /></td>

<th><label for="appLicence">AppLicence</label></th>
<td><input id="appLicence" name="AppLicence" value="<?php echo $app_licence; ?>" type="text" /></td>
</tr><tr>
<th><label for="issueDate">IssueDate</label></th>
<td><input id="issueDate" name="IssueDate" value="<?php echo $issue_date; ?>" type="text" /></td>

<th><label for="timestamp">Timestamp</label></th>
<td><input id="timestamp" name="Timestamp" value="<?php echo $timestamp; ?>" type="text" /></td>
</tr>
  </table>
</fieldset>
<input type="submit" id="" value="Submit" />
</form>
