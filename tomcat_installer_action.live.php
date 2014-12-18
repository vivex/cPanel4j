<?php
/**
 * Tomcat Installation Form Handler
 */

$domainName = $_POST['domainName'];
$tomCatVersion = $_POST['tomcat-version'];
error_reporting(E_ALL);
require_once "/usr/local/cpanel/php/cpanel.php";
$cpanel = new CPANEL();
$cpanel->set_debug(1);
$docRoot = null;
//echo $cpanel->header('cPanel4J');
$domainListApiCall = $cpanel->api2('DomainLookup','getdocroots', array() );
$domainList = $domainListApiCall['cpanelresult']['data'];
foreach($domainList as $domain){
    if($domain['domain'] == $domainName){
        $docRoot = $domain['docroot'];
        break;
    }
}


