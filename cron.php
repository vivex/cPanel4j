<?php
//CRON JOBS TO BE RUN AS ROOT

require_once "DBWrapper.php";

$dbWrapper= new DBWrapper();

$records = $dbWrapper->getRecordForCron();
while($row=mysql_fetch_array($records)){
 //Now have to add vhosts entry
	$userName = $row['user_name'];
	$domainName = $row['domain_name'];
	$ajp_port =  $row['ajp_port'];

            $vhostFileDir = "/usr/local/apache/conf/userdata/std/2/".$userName."/".$domainName;
            exec("mkdir -p " . $vhostFileDir);
            $vhostFileName = $vhostFileDir."/cpanel4j-ajp-vhost.conf";
            $vHost = "ProxyPass / ajp://localhost:" . $ajp_port . "/ \n ProxyPassReverse / ajp://localhost:" . $ajp_port;
            $vHostFile = fopen($vhostFileName, "w");
            fwrite($vHostFile, $vHost);
            fclose($vHostFile);

            //create symlinks
            exec("mkdir -p /usr/local/apache/conf/userdata/std/2_2/" . $userName . "/" . $domainName . "/");
            exec("mkdir -p /usr/local/apache/conf/userdata/std/2_4/" . $userName . "/" . $domainName . "/");
            $vhostFileName2_2 = "/usr/local/apache/conf/userdata/std/2_2/" . $userName . "/" . $domainName . "/cpanel4j_ajp.conf";
            $vhostFileName2_4 = "/usr/local/apache/conf/userdata/std/2_4/" . $userName . "/" . $domainName . "/cpanel4j_ajp.conf";
            exec("ln -s " . $vhostFileName . " " . $vhostFileName2_2);
            exec("ln -s " . $vhostFileName . " " . $vhostFileName2_4);
            $dbWrapper->setCronFlag($row['id']);
            exec("/usr/local/cpanel/scripts/rebuildhttpdconf");

}