<?php
//Page.php
require_once "/usr/local/cpanel/php/cpanel.php";
require_once "DBWrapper.php";
require_once "Tomcat.php";
$cpanel = new CPANEL();
$cpanel->set_debug(1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
	$domainListApiCall = $cpanel->api2('DomainLookup','getdocroot', array() );
    $domainList = $domainListApiCall['cpanelresult']['data'];
    $domainList = $domainList['0'];
    $docRoot = $domainList['docroot'];
    $roots = explode("/",$docRoot);
    $userName = $roots['2'];
$action = $_GET['action'];
if($action=="list"){
	echo $cpanel->header('View Tomcat Instances- cPanel4J');
 	

	$DBWrapper= new DBWrapper();
	$count = 1;
	$instanceResult = $DBWrapper->getTomcatInstancesByUser($userName);
	echo "<table class='table'><tr><th>#</th><th>Domain Name</th><th>TomcatVersion</th><th>Status</th><th>Create Date</th><th>Ports</th><th>Action</th></tr>";

	while($row = mysql_fetch_array($instanceResult)){
		if($row['cron_flag']==0){
			if($row['delete_flag']==0)
			$status="<font color=yellow>Pending Installation</font>";
			else if($row['delete_flag']==1)
			$status="<font color=red>Pending Delete</font>";
		} else if($row['cron_flag']==1)
		$status="<font color=green>Running</font>";
		
		echo "<tr><td>$count</td><td>".$row['domain_name']."</td>"."<td>".$row['tomcat_version']."</td><td>$status</td><td>".$row['create_date']."</td><td>ShutDown Port:".$row['shutdown_port']."<br/>HTTP Port:".$row['http_port']."<br/>AJP Port:".$row['ajp_port']."</td><td><a href=page.live.php?action=delete_instance&id=".$row['id'].">Delete</a></td></tr>";
	$count++;
	}

	echo "</table>";
	echo $cpanel->footer();
}else if($action =="delete_instance"){
	$id= $_GET['id'];
	$DBWrapper= new DBWrapper();
	$DBWrapper->setCronDeleteFlag($id,$userName);
	header("Location:page.php?action=list");

}else if($action =="create_instance"){
	echo $cpanel->header('cPanel4J');
$domainListApiCall = $cpanel->api2('DomainLookup','getbasedomains', array() );
$domainList = $domainListApiCall['cpanelresult']['data'];
//print_r($domainListApiCall);
echo '<p class="lead">cPanel4J allows you to install Apache Tomcat on your domain name.Tomcat is an application server that executes Java Servlets and renders web pages that include JSP Coding.It will work on default 80 port using ajp proxy (httpd as proxy server).</p>';
echo '<form class="form-horizontal" action = "page.live.php?action=create_instance_action" method = "POST" role="form">';
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

}else if($action =="create_instance_action"){
	echo $cpanel->header('cPanel4J');
$domainName = $_POST['domainName'];
$tomCatVersion = $_POST['tomcat-version'];
if(($tomCatVersion=='7.0.59' || $tomCatVersion=='8.0.15') & $domainName != ""){

    $domainListApiCall = $cpanel->api2('DomainLookup','getdocroot', array() );
    $domainList = $domainListApiCall['cpanelresult']['data'];
    $domainList = $domainList['0'];
    $docRoot = $domainList['docroot'];
    $roots = explode("/",$docRoot);
    $userName = $roots['2'];
    $tomcat = new Tomcat();
    $result = $tomcat->createInstance($domainName, $userName, $tomCatVersion);
    if($result['status']=="success"){
        
        header("Location:page.php?action=list");
    }else if($result['status']=="fail"){
        echo $result['message'];
    }else{
        echo "Something wrong happend";
    }
    
}else{
    echo "Form Data Error";
}

echo $cpanel->footer();
}


?>