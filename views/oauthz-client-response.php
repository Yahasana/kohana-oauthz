<?php defined('SYSPATH') or die('No direct script access.');
    // $GI18N = I18n::load(I18n::$lang);
?>
<h2>The first request and response</h2><table>
<tr><th>URI</th><td><a href="<?= $first['uri'] ?>" target="_blank"><?= $first['uri'] ?></a> &#9756; <i>Click! to access it directly</i></td></tr>
<tr><th>access_token</th><td><?= $first['token'] ?></td></tr>
<tr><th>response <i style="font-weight:normal">[json]</i></th><td><?= $first['info'] ?></td></tr>
</table>

<h2>The second request and response</h2><table>
<tr><th>URI</th><td><a href="<?= $second['uri'] ?>" target="_blank"><?= $second['uri'] ?></a> &#9756; <i>Click! to access it directly</i></td></tr>
<tr><th>access_token</th><td><?= $second['token'] ?></td></tr>
<tr><th>response <i style="font-weight:normal">[json]</i></th><td><?= $second['info'] ?></td></tr>
</table>
<br />
<br />
Look cool, hum<br />
But uham, do not try to refresh this page to refetch these info! <br />
if you do, yup please have a try.