<?php
$fileName = "tomcat-instances.xml";
$myfile = fopen($fileName, "r") or die("Unable to open file!");
$xmlstring =  fread($myfile,filesize($fileName));
$xml = simplexml_load_string($xmlstring);
$json = json_encode($xml);
$instances = json_decode($json,TRUE);
$instances = $instances['tomcat-instance'];
foreach($instances as $instance){
    echo $instance['username'];
}

function generateRandomPortNumber(){
    
}
?>
