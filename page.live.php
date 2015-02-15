<?php
//Page.php
require_once "/usr/local/cpanel/php/cpanel.php";
$cpanel = new CPANEL();
$cpanel->set_debug(1);

$action = $_GET['action'];
if($action=="list"){
 	$domainListApiCall = $cpanel->api2('DomainLookup','getdocroot', array() );
    $domainList = $domainListApiCall['cpanelresult']['data'];
    $docRoot = $domainList['docroot'];
    $roots = explode("/",$docRoot);
    $userName = $roots['2'];

	$DBWrapper= new DBWrapper();
	$instanceResult = $DBWrapper->getTomcatInstancesByUser($userName);
	echo "<table class='table'><tr><th>Domain Name</th><th>TomcatVersion</th><th>Create Date</th><th>Ports</th><th>Action</th></tr>";

	while($row = mysql_fetch_array($instanceResult)){
		echo "<tr><td>".$row['domain_name']."</td>"."<td>".$row['tomcat_version']."</td><td>".$row['create_date']."</td><td>ShutDown Port:".$row['shutdown_port']."<br/>HTTP Port:".$row['http_port']."<br/>AJP Port:".$row['ajp_port']."</td><td>Delete</td></tr>";
	}

	echo "</table>";
}else if($action =="delete_instance"){

}else if($action =="create_instance"){
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
		<option value="7.0.59">7.0.59 (Recommended)</option>
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

}else if($aciton =="create_instance_action"){
	echo $cpanel->header('cPanel4J');
$domainName = $_POST['domainName'];
$tomCatVersion = $_POST['tomcat-version'];
if(($tomCatVersion=='7.0.57' || $tomCatVersion=='8.0.15') & $domainName != ""){

    $domainListApiCall = $cpanel->api2('DomainLookup','getdocroot', array() );
    $domainList = $domainListApiCall['cpanelresult']['data'];
    $domainList = $domainList['0'];
    $docRoot = $domainList['docroot'];
    $roots = explode("/",$docRoot);
    $userName = $roots['2'];
    $tomcat = new Tomcat();
    $result = $tomcat->createInstance($domainName, $userName, $tomCatVersion);
    if($result['status']=="success"){
        echo $result['message'];
    }else if($result['status']=="fail"){
        echo $result['message'];
    }else{
        echo "Something wrong happend";
    }
    
}else{
    echo "Form Data Error";
}
}


?>