<?php
error_reporting(E_ALL);
require_once "/usr/local/cpanel/php/cpanel.php";
$cpanel = new CPANEL();
$cpanel->set_debug(1);
echo $cpanel->header('cPanel4J');
$domainListApiCall = $cpanel->api2('DomainLookup','getbasedomains', array() );
$domainList = $domainListApiCall['cpanelresult']['data'];
//print_r($domainListApiCall);
echo '<p class="lead">cPanel4J allows you to install Apache Tomcat on your domain name.Tomcat is an application server that executes Java Servlets and renders web pages that include JSP Coding.It will work on default 80 port using ajp proxy (httpd as proxy server).</p>';
echo '<form class="form-horizontal" action = "tomcat_installer_action.live.php" method = "POST" role="form">';
echo '<h4>Apache Tomcat Installer</h4><div class="form-group"><label for="domain" class="col-sm-4 control-label">Domain Name</label>';
echo "<div class='col-sm-8'><select name='domainName' class='form-control'>";
foreach($domainList as $domain){
echo "<option>".$domain['domain']."</option>";
}
echo "</select></div></div>";

?>
<div class="form-group">
	<label for="version" class="col-sm-4 control-label">Tomcat Version</label>
	<div class="col-sm-8"><select name="tomcat-version" class="form-control">
		<option value="7.0.57">7.0.57 (Recommended)</option>
		<option value="8.0.15">8.0.15</optioni>
	</select></div>
</div>

<div class="form-group text-right">
                <input id="next" class="btn btn-primary" type="submit" value="Next">
                <div id="status"></div>
            </div>
<?php

echo "</div></form>";
?>
<h4>Existing Tomcat Installations</h4>
<table class="table table-striped">
	<thead>
		<tr>
		<th>#</th><th>Domain</th><th>Path</th><th>Ports</th><th>Action</th></tr></thead>

</table>

<?php
echo $cpanel->footer();
?>
