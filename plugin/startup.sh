#!/usr/bin/php
<?php
include 'DBWrapper.php';
include 'Tomcat.php';
$dbWrapper = new DBWrapper();
$tomcat = new Tomcat();
$instances = $dbWrapper->getAllInstance();
while($row = mysql_fetch_array($instances)){
$userName = $row['user_name'];
$domainName = $row['domain_name'];
$tomcatVersion = $row['tomcat_version'];
exec("sh service-files/" . $userName . "-" . $domainName . "-tomcat-" . $tomcatVersion . ".sh start");
}
?>