<?php
$server = URL::base(FALSE, TRUE);
echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
?><XRD xmlns="http://docs.oasis-open.org/ns/xri/xrd-1.0"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <Expires>2011-05-10T00:00:00Z</Expires>
  <Subject>http://<?php echo $server; ?>oauth</Subject>
  <Property type="http://<?php echo $server; ?>type/oauth" xsi:nil="true" />
  <Link href="http://<?php echo $server; ?>oauth/authorize" />
  <Link href="http://<?php echo $server; ?>oauth/token" />
  <Link href="http://<?php echo $server; ?>api" />
</XRD>