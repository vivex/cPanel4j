#!/usr/bin/php
###############################################################
##                                                           ##
##                   cPanel4j Startup File                   ##
##               Author contact@viveksoni.net                ##
##             Copyright (C) 2015  Vivek Soni                ##
##        Instructions & More Info -> www.cpnel4j.com        ##
##       Released under the GNU General Public License       ##
##                                                           ##
###############################################################
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