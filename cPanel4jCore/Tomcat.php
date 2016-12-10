<?php

/**
 * Author: VIVEK SONI (contact@viveksoni.net)
 * Tomcat Class
 * Plugin Directory: /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j
 * Cron Command: * * * * * php /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j/cron.php > cpanel4j_Cron_log.txt
 *
 */

namespace cPanel4jCore;

require_once "Config.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * This Class deals with tomcat installation
 */
class Tomcat extends Config
{

    private $DBWrapper;

    public function __construct ()
    {
        exec("export JAVA_HOME=".$this->javaHome);
        $this->DBWrapper = new DBWrapper();
    }

    public function generateRandomPortNumber ($reservedArray)
    {
        $random = true;
        while ($random) {
            $temp = rand(2000, 18000);
            if (array_search($temp, $reservedArray)) {
                continue;
            }
            else {
                return $temp;
            }
        }
    }

    /**
     * Read all the used ports from database & config file and returns array
     * @return array
     */
    public function getReservedPorts ()
    {
        $reservedPorts = $this->reservedPorts;
        $userPorts = $this->DBWrapper->getAllPorts();
        if ($userPorts == null)
            $userPorts = array();
        $result = array_merge($reservedPorts, $userPorts);
        return $result;
    }

    public function createInstance ($domainName, $userName, $tomcatVersion)
    {
        $result = "";
        $reservedArray = $this->getReservedPorts();
        //check if  domain already exists exists in instances
        if ($this->DBWrapper->getTomcatInstancesCountByDomain($domainName) <= 0) {
            exec("export JAVA_HOME=" . $this->javaHome);
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
            // $catalinaHome = "/cPanel4jCore/tomcat-" . $tomcatVersion . "-engine";
            $userTomcatDir = "/home/" . $userName . "/" . $domainName . "/tomcat-" . $tomcatVersion . "/";

            //Step 1st Creating User Tomcat Directory
            if (!file_exists($userTomcatDir)) {
                exec("mkdir -p " . $userTomcatDir);
            }
            else {
                $result .="User Tomcat Directory Already Exists";
            }

            //step 2nd Moving tomcat installation files to user tomcat directory

            $result.= exec("cp -r /cPanel4jCore/tomcat-" . $tomcatVersion . "-template/logs /cPanel4jCore/tomcat-" . $tomcatVersion . "-template/conf /cPanel4jCore/tomcat-" . $tomcatVersion . "-template/temp /cPanel4jCore/tomcat-" . $tomcatVersion . "-template/webapps " . $userTomcatDir);

            //step 3rd Writing Server.XML File
            $serverXMLFileName = $userTomcatDir . "conf/server.xml";
            exec("rm -f $serverXMLFileName");
            $additionString = "";
            if ($tomcatVersion == "7.0.59") {
                // if version is 7 then we have to add this line in server.xml not requires in tomcat 8
                $additionString = '<Listener className="org.apache.catalina.core.JasperListener" />';
            }
            // Content of server.xml file
            $serverXMLFileContent = <<<EOT
<?xml version = "1.0" encoding = "utf-8"?>
<Server port="$shutdown_port" shutdown="SHUTDOWN">
    <Listener className="org.apache.catalina.startup.VersionLoggerListener" />
    <Listener className="org.apache.catalina.core.AprLifecycleListener" SSLEngine="on" />
                    $additionString
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
        <Connector port="$http_port" protocol="HTTP/1.1"
                   connectionTimeout="20000"
                   redirectPort="8443" />
        <Connector port="$ajp_port" enableLookups="false"  protocol="AJP/1.3" redirectPort="8443" />
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
</Server>
EOT;
            // Writing Server.xml file
            $configFile = fopen($serverXMLFileName, "w");
            fwrite($configFile, $serverXMLFileContent);
            fclose($configFile);

            //TODO: verifying installation
            // $isInstalled = $this->verifyInstallation($userTomcatDir,$serviceFile);
            //Adding HTTP (ONLY HTTP) Port in iptables allow list
            $result.= exec("iptables -A INPUT -p tcp --dport " . $http_port . " -j ACCEPT");
            $result.= exec("/etc/init.d/iptables restart");

            $this->DBWrapper->insertTomcatInstance($userName, $domainName, $http_port, $ajp_port, $shutdown_port, $tomcatVersion);
            // TODO: $result if it have some contain in it then it mean it is a error
            //cool now write this installation back to xml file
            return array("status" => 'success', 'message' => 'Instance Created Successfully');
        }
        else {
            return array('status' => 'fail', 'message' => "Domain Is already there");
        }
    }

    /**
     * Performs Action on tomcat instnace (like start stop delete)
     * @param int $id
     * @param string $userName
     * @param string $action
     */
    public function tomcatInstanceAction ($id, $userName, $action)
    {
        $i = $this->DBWrapper->getInstance($id);
        if ($i['user_name'] == $userName) {
            $this->DBWrapper->setCronFlag($id, 0);
            $this->DBWrapper->setStatus($id, $action);
        }
    }

    /**
     * Deletes the instance
     * Initiates a cron to delete files & from database
     * @param int $instanceId
     * @param string $userName
     * @return boolean
     */
    public function deleteInstance ($instanceId, $userName)
    {
        if ($this->DBWrapper->getUserNameByInstanceId($instanceId) == $userName) {
            $this->DBWrapper->setCronFlag($instanceId, 0);
            $this->DBWrapper->setDeleteFlag($instanceId);

            return true;
        }
        return false;
    }

}
