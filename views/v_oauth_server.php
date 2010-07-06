<?php defined('SYSPATH') or die('No direct script access.');
    $GI18N = I18n::load(I18n::$lang);
    ?><table><caption>Service list you have register</caption><thead>
    <tr><th>client_id</th><th>client_secret</th><th>redirect_uri</th><th>scope</th><th>Public Cert</th><th>OP</th></tr>
    </thead><tbody><?php
    foreach($servers as $row)
    {
    ?><tr><td><a href="/server/register/<?php echo $row['client_id']; ?>"><?php echo $row['client_id']; ?></a></td>
    <td><?php echo $row['client_secret']; ?></td><td><?php echo $row['redirect_uri']; ?></td>
    <td><?php echo $row['scope']; ?></td><td><?php echo $row['public_cert']; ?></td><th>DEL</th></tr>
<?php
    }
?></tbody></table><hr />

<a href="/server/register">Register another request indentifier</a>
