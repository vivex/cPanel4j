#!/usr/bin/php
<?php
namespace cPanel4jCore;
require_once 'DBWrapper.php';
require_once 'Tomcat.php';
require_once 'Config.php';
$config = new Config();
$dbWrapper = new DBWrapper();
$tomcat = new Tomcat();
exec("export JAVA_HOME=".$config->java_home);
$instances = $dbWrapper->getAllInstance();
while($row = mysql_fetch_array($instances)){
$userName = $row['user_name'];
$domainName = $row['domain_name'];
$tomcatVersion = $row['tomcat_version'];
exec("sh service-files/" . $userName . "-" . $domainName . "-tomcat-" . $tomcatVersion . ".sh start");
}
?>