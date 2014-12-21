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
        $userPorts = array('8080', '80','25565','3306','2638','2086','2087','2095','2096','2083','2082'); //this array will hold all reserved ports in 1d array
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
    
    public function writeToXML($instances,$domainName,$userName,$tomcatVersion,$http_port,$ajp_port,$shutdown_port){
        $newInstance['shutdown_port'] = $shutdown_port;
        $newInstance['http_port'] = $http_port;
        $newInstance['ajp_port'] = $ajp_port;
        $newInstance['username'] = $userName;
        $newInstance['domain_name'] = $domainName;
        $newInstance['tomcat_version'] = $tomcatVersion;
        array_push($instances, $newInstance);
        $xml = new SimpleXMLElement('<root/>');
        array_walk_recursive($instances, array ($xml, 'addChild'));
        $content = $xml->asXML();
        $myfile = fopen(c, "w") or die("Unable to open file for write!");
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
            $command = "sh ".dirname(__FILE__) ."/setup-instance.sh $domainName $userName $tomcatVersion $http_port $ajp_port $shutdown_port";
            $command = escapeshellarg($command);
            echo "About to".$command;
            // setup-instance.sh domain.com username version connectorPort ajpport shutdownport
            $result = exec($command,$op,$rt);
            var_dump($result);
            echo "<hr>"; var_dump($op);echo "<hr>"; var_dump($rt);
            echo "Command Executed";
            echo $result;
            if ($result == 'DONE') {
                //cool now write this installation back to xml file
                $this->writeToXML($domainName,$userName,$tomcatVersion,$http_port,$ajp_port,$shutdown_port);
                return array("status"=>'success','message'=>'Instance Created Successfully');
            } else {
                return array('status'=>'fail','message'=>$result);  
            }
        } else {
            return array('status'=>'fail','message'=>"Domain Is already there");  
        }
    }

}
