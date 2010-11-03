<?php defined('SYSPATH') or die('No direct script access.');
    $GI18N = I18n::load(I18n::$lang);
    ?><table><caption>Applications list you have registered</caption><thead>
    <tr><th>API Key</th><th>API Secret</th><th>Redirect URI</th><th>Scope</th><th>SSH Key</th><th>OP</th></tr>
    </thead><tbody><?php
    foreach($servers as $row)
    {
    ?><tr><td><a href="/server/register/<?php echo $row['client_id']; ?>"><?php echo $row['client_id']; ?></a></td>
    <td><?php echo $row['client_secret']; ?></td><td><?php echo $row['redirect_uri']; ?></td>
    <td><?php echo $row['scope']; ?></td><td><?php echo $row['public_cert']; ?></td><th>DEL</th></tr><?php
    }
?></tbody></table><hr />

<a href="/server/register">Register another request indentifier</a>
