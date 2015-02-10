<?php

/**
 * Author: VIVEK SONI (contact@viveksoni.net)
 * Tomcat Class
 * 
 */
error_reporting(E_ALL);

class Tomcat {

    public $instanceFileName = "tomcat-instances.xml";

    public function getXMLArray() {
        $myfile = fopen($this->instanceFileName, "r") or die("Unable to open file in read mode!");
        $xmlstring = fread($myfile, filesize($this->instanceFileName));
        $xml = simplexml_load_string($xmlstring);
        $json = json_encode($xml);
        $instances = json_decode($json, TRUE);
        fclose($myfile);
        return $instances['tomcat-instance'];
    }

    public function reservedArray($instances) {
        $userPorts = array('8080', '80', '25565', '3306', '2638', '2086', '2087', '2095', '2096', '2083', '2082'); //this array will hold all reserved ports in 1d array
        foreach ($instances as $instance) {
            array_push($userPorts, $instance['shutdown_port']);
            array_push($userPorts, $instance['http_port']);
            array_push($userPorts, $instance['ajp_port']);
        }
        return $userPorts;
    }

    public function generateRandomPortNumber($reservedArray) {
        $random = true;
        while ($random) {
            $temp = rand(2000, 18000);
            if (array_search($reservedArray, $temp)) {
                continue;
            } else {
                return $temp;
            }
        }
    }

    public function checkDomain($instancesArray, $domainName) {
        foreach ($instancesArray as $instances) {
            if ($instances['domain_name'] == $domainName) {
                return false;
            }
        }
        return true;
    }

    public function writeToXML($instances, $domainName, $userName, $tomcatVersion, $http_port, $ajp_port, $shutdown_port) {
        $newInstance['shutdown_port'] = $shutdown_port;
        $newInstance['http_port'] = $http_port;
        $newInstance['ajp_port'] = $ajp_port;
        $newInstance['username'] = $userName;
        $newInstance['domain_name'] = $domainName;
        $newInstance['tomcat_version'] = $tomcatVersion;
        if (count($instances) > 0)
            array_push($instances, $newInstance);
        else
            $instances = $newInstance;
        $xml = new SimpleXMLElement('<root/>');
        foreach ($instances as $i) {
            $node = $xml->addChild("tomcat-instance");
            foreach ($i as $k => $v) {
                $node->addChild($k, $v);
            }
        }
        $content = $xml->asXML();
        $myfile = fopen($this->instanceFileName, "w") or die("Unable to open file for write!");
        fwrite($myfile, $content);
        fclose($myfile);
    }

    public function createInstance($domainName, $userName, $tomcatVersion) {
        $instancesArray = $this->getXMLArray();
        $reservedArray = $this->reservedArray($instancesArray);
        //check if  domain already exists exists in instances
        if ($this->checkDomain($instancesArray, $domainName)) {
            //generate three portnumbers
            $shutdown_port = $this->generateRandomPortNumber($reservedArray);
            array_push($reservedArray, $shutdown_port);
            $http_port = $this->generateRandomPortNumber($reservedArray);
            array_push($reservedArray, $http_port);
            $ajp_port = $this->generateRandomPortNumber($reservedArray);
            array_push($reservedArray, $ajp_port);
            /**
             * Setting Up the instance now
             */
            $catalinaHome = "/usr/local/cpanel4j/apache-tomcat-" . $tomcatVersion;
            $userTomcatDir = "/home/" . $userName . "/" . $domainName . "/tomcat-" . $tomcatVersion . "/";

            //Step 1st Creating User Tomcat Directory
            if (!file_exists($userTomcatDir)) {
                exec("mkdir -p " . $userTomcatDir);
            } else {
                $result .="User Tomcat Directory Already Exists";
            }


            //step 2nd Moving tomcat installation files to user tomcat directory

            $result.= exec("cp -r " . $tomcatVersion . "/logs " . $tomcatVersion . "/conf " . $tomcatVersion . "/temp " . $tomcatVersion . "/webapps " . $userTomcatDir);

            //step 3rd Writing Server.XML File
            $serverXMLFileName = $userTomcatDir . "/conf/server.xml";
            $serverXMLFileContent = '<?xml version="1.0" encoding="utf-8"?>
<Server port="' . $shutdown_port . '" shutdown="SHUTDOWN">
  <Listener className="org.apache.catalina.startup.VersionLoggerListener" />
  <Listener className="org.apache.catalina.core.AprLifecycleListener" SSLEngine="on" />
  <Listener className="org.apache.catalina.core.JasperListener" />
  <Listener className="org.apache.catalina.core.JreMemoryLeakPreventionListener" />
  <Listener className="org.apache.catalina.mbeans.GlobalResourcesLifecycleListener" />
  <Listener className="org.apache.catalina.core.ThreadLocalLeakPreventionListener" />
  <GlobalNamingResources>
    <Resource name="UserDatabase" auth="Container"
              type="org.apache.catalina.UserDatabase"
              description="User database that can be updated and saved"
              factory="org.apache.catalina.users.MemoryUserDatabaseFactory"
              pathname="conf/tomcat-users.xml" />
  </GlobalNamingResources>
  <Service name="Catalina">
    <Connector port="' . $http_port . '" protocol="HTTP/1.1"
               connectionTimeout="20000"
               redirectPort="8443" />
    <Connector port="' . $ajp_port . '" enableLookups="false"  protocol="AJP/1.3" redirectPort="8443" />
    <Engine name="Catalina" defaultHost="localhost">
      <Realm className="org.apache.catalina.realm.LockOutRealm">
        <Realm className="org.apache.catalina.realm.UserDatabaseRealm"
               resourceName="UserDatabase"/>
      </Realm>
      <Host name="localhost"  appBase="webapps"
            unpackWARs="true" autoDeploy="true">
        <Valve className="org.apache.catalina.valves.AccessLogValve" directory="logs"
               prefix="localhost_access_log." suffix=".txt"
               pattern="%h %l %u %t &quot;%r&quot; %s %b" />
      </Host>\n
    </Engine>\n
  </Service>
</Server>';
            $configFile = fopen($serverXMLFileName, "w");
            fwrite($configFile, $serverXMLFileContent);
            fclose($configFile);


            // Step 4 creating service startup sh file
            $fileName = "service-files/" . $userName . "-" . $domainName . "-tomcat-" . $tomcatVersion . ".sh";
            $serviceFileContent = "#!/bin/bash \n#description: Tomcat-" . $domainName . " start stop restart \n#processname: tomcat-" . $userName . "-" . $domainName . " \n
#chkconfig: 234 20 80 \n CATALINA_HOME=" . $catalinaHome . " \n export CATALINA_BASE=" . $userTomcatDir . " \n
case $1 in \n start) \n sh \$CATALINA_HOME/bin/startup.sh \n ;; \n stop) \n sh \$CATALINA_HOME/bin/shutdown.sh \n ;; \n
restart) \n sh \$CATALINA_HOME/bin/shutdown.sh \n sh \$CATALINA_HOME/binstartup.sh \n;; \n esac \n exit 0";
            $serviceFile = fopen($fileName, "w");
            fwrite($serviceFile, $serviceFileContent);
            fclose($serviceFile);




            //Now have to add vhosts entry
            $vhostFileDir = "usr/local/apache/conf/userdata/std/2/" . $userName . "/" . $domainName . "";
            exec("mkdir -p " . $vhostFileDir);
            $vhostFileName = $vhostFileDir . "/cpanel4j-ajp-vhost.conf";
            $vHost = "ProxyPass / ajp://localhost:" . $ajp_port . "/ \n ProxyPassReverse / ajp://localhost:" . $ajp_port;
            $vHostFile = fopen($vhostFileName, "w");
            fwrite($vHostFile, $vHost);
            fclose($vHostFile);

            //create symlinks

            $vhostFileName2_2 = "usr/local/apache/conf/userdata/std/2_2/" . $userName . "/" . $domainName . "/cpanel4j_ajp.conf";
            $vhostFileName2_4 = "usr/local/apache/conf/userdata/std/2_4/" . $userName . "/" . $domainName . "/cpanel4j_ajp.conf";
            exec("ln -s " . $vhostFileName . " " . $vhostFileName2_2);
            exec("ln -s " . $vhostFileName . " " . $vhostFileName2_4);

            //TODO: verifying installation 
            // $isInstalled = $this->verifyInstallation($userTomcatDir,$serviceFile);
            //ReBuilding Apache
            exec("sh /usr/local/cpanel/scripts/rebuildhttpdconf");

            //Adding HTTP (ONLY HTTP) Port in iptables allow list
            $result.= exec("iptables -A INPUT -p tcp --dport " . $http_port . " -j ACCEPT");
            $result.= exec("/etc/init.d/iptables restart");
            $result = false;
            if ($result == 'DONE') {
                //cool now write this installation back to xml file
                $this->writeToXML($instancesArray, $domainName, $userName, $tomcatVersion, $http_port, $ajp_port, $shutdown_port);
                return array("status" => 'success', 'message' => 'Instance Created Successfully');
            } else {
                return array('status' => 'fail', 'message' => $result);
            }
        } else {
            return array('status' => 'fail', 'message' => "Domain Is already there");
        }
    }

}
