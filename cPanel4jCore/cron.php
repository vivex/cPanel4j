<?php

/**
 * cPanel4J
 * sql_install.php
 * Author: Vivek Soni (contact@viveksoni.net)
 * Instructions & More Info: www.cpanel4j.com
 * Released under the GNU General Public License
 */

namespace cPanel4jCore;

//CRON JOBS TO BE RUN AS ROOT

require_once "DBWrapper.php";
require_once "Tomcat.php";
$dbWrapper = new DBWrapper();
$tomCat = new Tomcat();

$records = $dbWrapper->getRecordForCron();
while ($row = mysql_fetch_array($records)) {
    if ($row['delete_flag'] == 0) {
        $userName = $row['user_name'];
        $domainName = $row['domain_name'];
        $ajp_port = $row['ajp_port'];
        $tomcatVersion = $row['tomcat_version'];
        if ($row['installed'] == 0) {
            //install instnac
            $catalinaHome = "/cPanel4jCore/tomcat-" . $tomcatVersion . "-engine";
            $userTomcatDir = "/home/" . $userName . "/" . $domainName . "/tomcat-" . $tomcatVersion . "/";
            // Step 4 creating service startup sh file

            $fileName = "/cPanel4jCore/service-files/" . $userName . "-" . $domainName . "-tomcat-" . $tomcatVersion . ".sh";
            exec("rm -f $fileName");
            $serviceFileContent = "#!/bin/bash \n#description: Tomcat-" . $domainName . " start stop restart \n#processname: tomcat-" . $userName . "-" . $domainName . " \n
#chkconfig: 234 20 80 \n CATALINA_HOME=" . $catalinaHome . " \n export CATALINA_BASE=" . $userTomcatDir . " \n
case $1 in \n start) \n sh \$CATALINA_HOME/bin/startup.sh \n ;; \n stop) \n sh \$CATALINA_HOME/bin/shutdown.sh \n ;; \n
restart) \n sh \$CATALINA_HOME/bin/shutdown.sh \n sh \$CATALINA_HOME/binstartup.sh \n;; \n esac \n exit 0";
            $serviceFile = fopen($fileName, "w");
            fwrite($serviceFile, $serviceFileContent);
            fclose($serviceFile);

            exec("chown $userName $fileName");
            //Now have to add vhosts entry

            $vhostFileDir = "/usr/local/apache/conf/userdata/std/2/" . $userName . "/" . $domainName;
            exec("mkdir -p " . $vhostFileDir);
            $vhostFileName = $vhostFileDir . "/cpanel4j-ajp-vhost.conf";
            $vHost = "ProxyPass / ajp://localhost:" . $ajp_port . "/ \nProxyPassReverse / ajp://localhost:" . $ajp_port;
            $vHostFile = fopen($vhostFileName, "w");
            fwrite($vHostFile, $vHost);
            fclose($vHostFile);

            //create symlinks
            exec("mkdir -p /usr/local/apache/conf/userdata/std/2_2/" . $userName . "/" . $domainName . "/");
            exec("mkdir -p /usr/local/apache/conf/userdata/std/2_4/" . $userName . "/" . $domainName . "/");
            //create dir  for ssl too
            exec("mkdir -p /usr/local/apache/conf/userdata/ssl/2/" . $userName . "/" . $domainName . "/");
            exec("mkdir -p /usr/local/apache/conf/userdata/ssl/2_2/" . $userName . "/" . $domainName . "/");
            exec("mkdir -p /usr/local/apache/conf/userdata/ssl/2_4/" . $userName . "/" . $domainName . "/");


            $vhostFileName2_2 = "/usr/local/apache/conf/userdata/std/2_2/" . $userName . "/" . $domainName . "/cpanel4j-ajp-vhost.conf";
            $vhostFileName2_4 = "/usr/local/apache/conf/userdata/std/2_4/" . $userName . "/" . $domainName . "/cpanel4j-ajp-vhost.conf";

            $vhostFileName_ssl_2 = "/usr/local/apache/conf/userdata/ssl/2/" . $userName . "/" . $domainName . "/cpanel4j-ajp-vhost.conf";
            $vhostFileName_ssl_2_2 = "/usr/local/apache/conf/userdata/ssl/2_2/" . $userName . "/" . $domainName . "/cpanel4j-ajp-vhost.conf";
            $vhostFileName_ssl_2_4 = "/usr/local/apache/conf/userdata/ssl/2_4/" . $userName . "/" . $domainName . "/cpanel4j-ajp-vhost.conf";

            exec("ln -s " . $vhostFileName . " " . $vhostFileName2_2);
            exec("ln -s " . $vhostFileName . " " . $vhostFileName2_4);
            exec("ln -s " . $vhostFileName . " " . $vhostFileName_ssl_2);
            exec("ln -s " . $vhostFileName . " " . $vhostFileName_ssl_2_2);
            exec("ln -s " . $vhostFileName . " " . $vhostFileName_ssl_2_4);

            $dbWrapper->setCronFlag($row['id'], 1);
            $dbWrapper->setStatus($row['id'], 'start');
            $dbWrapper->setInstalledFlag($row['id']);
            exec("/usr/local/cpanel/scripts/rebuildhttpdconf");
            exec("/etc/init.d/httpd restart");
            echo exec("sh service-files/" . $userName . "-" . $domainName . "-tomcat-" . $tomcatVersion . ".sh start");
        }
        else if ($row['installed'] == 1) {
            //mean instance is installed we need to check wheather user want to start or stop

            if ($row['status'] == "pending_start") {
                echo exec("sh service-files/" . $userName . "-" . $domainName . "-tomcat-" . $tomcatVersion . ".sh start");
                $dbWrapper->setCronFlag($row['id'], 1);
                $dbWrapper->setStatus($row['id'], 'start');
            }
            else if ($row['status'] == "pending_stop") {
                echo exec("sh service-files/" . $userName . "-" . $domainName . "-tomcat-" . $tomcatVersion . ".sh stop");
                $dbWrapper->setCronFlag($row['id'], 1);
                $dbWrapper->setStatus($row['id'], 'stop');
            }
        }
    }
    else if ($row['delete_flag'] == 1) {
        //delete the instanmlce
        $id = $row['id'];
        $userName = $row['user_name'];
        $domainName = $row['domain_name'];
        $tomcatVersion = $row['tomcat_version'];

        echo exec("rm -rf /usr/local/apache/conf/userdata/std/2/" . $userName . "/" . $domainName);
        echo exec("rm -rf /usr/local/apache/conf/userdata/std/2_2/" . $userName . "/" . $domainName);
        echo exec("rm -rf /usr/local/apache/conf/userdata/std/2_4/" . $userName . "/" . $domainName);

        echo exec("rm -rf /usr/local/apache/conf/userdata/ssl/2/" . $userName . "/" . $domainName);
        echo exec("rm -rf /usr/local/apache/conf/userdata/ssl/2_2/" . $userName . "/" . $domainName);
        echo exec("rm -rf /usr/local/apache/conf/userdata/ssl/2_4/" . $userName . "/" . $domainName);

        echo exec("rm -rf /home/" . $userName . "/" . $domainName);
        echo exec("rm service-files/" . $userName . "-" . $domainName . "-tomcat-" . $tomcatVersion . ".sh");

        $dbWrapper->hardDeleteTCInstance($id, $userName);
        exec("/usr/local/cpanel/scripts/rebuildhttpdconf");
        exec("/etc/init.d/httpd restart");
    }
}