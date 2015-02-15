<?php

/**
 * Author: VIVEK SONI (contact@viveksoni.net)
 * Tomcat Class
 * Plugin Directory: /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j
 * Cron Command: * * * * * php /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j/cron.php > cpanel4j_Cron_log.txt
 *
 */
require_once "Config.php";
class Tomcat extends Config{

    private $DBWrapper;

    public function __construct(){
         $this->DBWrapper= new DBWrapper();
    }

    public function generateRandomPortNumber($reservedArray) {
        $random = true;
       while ($random) {
            $temp = rand(2000, 18000);
            if (array_search($temp,$reservedArray)) {
                continue;
            } else {
                return $temp;
            }
        }
    
}

    public function getReservedPorts(){
        $reservedPorts = array('8080', '80', '25565', '3306', '2638', '2086', '2087', '2095', '2096', '2083', '2082'); 
        $userPorts = $this->DBWrapper->getAllPorts();
        if($userPorts==null)$userPorts=array();
        $result = array_merge($reservedPorts,$userPorts);
        return $result;
    }


    public function createInstance($domainName, $userName, $tomcatVersion) {
        $result="";
        $reservedArray = $this->getReservedPorts();
        //check if  domain already exists exists in instances
        if ($this->DBWrapper->getTomcatInstancesCountByDomain($domainName)<=0) {
             exec("export JAVA_HOME=".$this->java_home);
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
            $catalinaHome = "/usr/local/cpanel/base/frontend/paper_lantern/cpanel4j/tomcat-" . $tomcatVersion."-engine";
            $userTomcatDir = "/home/" . $userName . "/" . $domainName . "/tomcat-" . $tomcatVersion . "/";

            //Step 1st Creating User Tomcat Directory
            if (!file_exists($userTomcatDir)) {
                exec("mkdir -p " . $userTomcatDir);
            } else {
                $result .="User Tomcat Directory Already Exists";
            }

            //step 2nd Moving tomcat installation files to user tomcat directory

            $result.= exec("cp -r tomcat-".$tomcatVersion."-template/logs tomcat-" . $tomcatVersion . "-template/conf tomcat-" . $tomcatVersion . "-template/temp tomcat-" . $tomcatVersion . "-template/webapps " . $userTomcatDir);

            //step 3rd Writing Server.XML File
            $serverXMLFileName = $userTomcatDir . "/conf/server.xml";
            exec("rm -f $serverXMLFileName");
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
      </Host>
    </Engine>
  </Service>
</Server>';
            $configFile = fopen($serverXMLFileName, "w");
            fwrite($configFile, $serverXMLFileContent);
            fclose($configFile);


            // Step 4 creating service startup sh file
           
            $fileName = "service-files/" . $userName . "-" . $domainName . "-tomcat-" . $tomcatVersion . ".sh";
            exec("rm -f $fileName");
            $serviceFileContent = "#!/bin/bash \n#description: Tomcat-" . $domainName . " start stop restart \n#processname: tomcat-" . $userName . "-" . $domainName . " \n
#chkconfig: 234 20 80 \n CATALINA_HOME=" . $catalinaHome . " \n export CATALINA_BASE=" . $userTomcatDir . " \n
case $1 in \n start) \n sh \$CATALINA_HOME/bin/startup.sh \n ;; \n stop) \n sh \$CATALINA_HOME/bin/shutdown.sh \n ;; \n
restart) \n sh \$CATALINA_HOME/bin/shutdown.sh \n sh \$CATALINA_HOME/binstartup.sh \n;; \n esac \n exit 0";
            $serviceFile = fopen($fileName, "w");
            fwrite($serviceFile, $serviceFileContent);
            fclose($serviceFile);
        

            //TODO: verifying installation 
            // $isInstalled = $this->verifyInstallation($userTomcatDir,$serviceFile);


            //Adding HTTP (ONLY HTTP) Port in iptables allow list
            $result.= exec("iptables -A INPUT -p tcp --dport " . $http_port . " -j ACCEPT");
            $result.= exec("/etc/init.d/iptables restart");

            $this->DBWrapper->insertTomcatInstance($userName,$domainName,$http_port,$ajp_port,$shutdown_port,$tomcatVersion);
            echo $result;
            if ($result == 'DONE') {
                //cool now write this installation back to xml file
                
                return array("status" => 'success', 'message' => 'Instance Created Successfully');
            } else {
                return array('status' => 'fail', 'message' => $result);
            }
        } else {
            return array('status' => 'fail', 'message' => "Domain Is already there");
        }
    }

    public function tomcatInstanceAction($id,$userName,$action){
      $i = $this->DBWrapper->getInstance($id);
      if($i['user_name']==$userName){
      echo exec("sh service-files/" . $i['user_name'] . "-" . $i['domain_name'] . "-tomcat-" . $i['tomcat_version'] . ".sh ".$action);
      $this->DBWrapper->setStatus($status,$id,$userName);
      }
    }


    public function deleteInstance($instanceId){
        $instance =  $this->DBWrapper->setCronDeleteFlag($instanceId);
        return true;
    }

}
