<?php
/**
 * Tomcat Installation Form Handler
 */
error_reporting(E_ALL);
require_once "/usr/local/cpanel/php/cpanel.php";
require_once "Tomcat.php";
$cpanel = new CPANEL();
$cpanel->set_debug(1);
echo $cpanel->header('cPanel4J');
$domainName = $_POST['domainName'];
$tomCatVersion = $_POST['tomcat-version'];
echo $tomCatVersion;
echo $domainName;
if($tomCatVersion!='7.0.57' || $tomCatVersion!='8.0.15' || $domaineName == ""){
    echo "Error: Error In Form Data";
}
else{
    $domainListApiCall = $cpanel->api2('DomainLookup','getdocroot', array() );
    $domainList = $domainListApiCall['cpanelresult']['data'];
    $docRoot = $domain['docroot'];
    $roots = explode("/",$docRoot);
    $userName = $roots['2'];
    $tomcat = new Tomcat();
    $result = $tomcat->createInstance($domainName, $userName, $tomcatVersion);
    
}




