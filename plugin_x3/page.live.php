<?php
require_once "/usr/local/cpanel/php/cpanel.php";

$cpanel = new CPANEL();

$header =$cpanel->api1('Branding','include','stdheader.html');

echo $header['cpanelresult']['data']['result'];
?>
<center>
<h1>cPanel4J Wont Support X3 Theme Please Switch To Paper Lantern Theme</h1>
<br/>
And cPanel is also going to remove support for this theme <a href="http://blog.cpanel.com/upgrade-to-paper-lantern/#more-25682">Click Here To No More</a>.

</center>

<?php
echo "<br/><center><a href='https://www.cpanel4j.com'>Powered By cPanel4j</a></center>";
$footer = $cpanel->api1('Branding','include','stdfooter.html');
echo $footer['cpanelresult']['data']['result'];
?>