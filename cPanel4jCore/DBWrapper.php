<?php

/**
 * cPanel4J
 * DBWrapper.php
 * Author: Vivek Soni (contact@viveksoni.net)
 * Instructions & More Info: www.cpanel4j.com
 * Released under the GNU General Public License
 */

namespace cPanel4jCore;

require_once "Config.php";

/**
 * DBWrapper Class
 * Deals with datbase methods
 */
class DBWrapper extends Config
{

    private $connection;

    public function __construct ()
    {
        $this->connection = mysqli_connect($this->host, $this->userName, $this->password);
        mysqli_select_db($this->connection, $this->database);
    }

    public function insertTomcatInstance ($userName, $domainName, $httpPort, $ajpPort, $shutDownPort, $tomcatVersion)
    {
        $now = new \DateTime();
        $createDate = $now->format('Y-m-d H:i:s');
        $query = "insert into `tomcat-instances` (user_name,domain_name,tomcat_version,shutdown_port,
        http_port,ajp_port,create_date) values('$userName','$domainName','$tomcatVersion','$shutDownPort',
        '$httpPort','$ajpPort','$createDate')";
       $q = mysqli_query($this->connection, $query);
// 	echo mysql_error();
        return ($q) ? true : false;
    }

    public function getAllPorts ()
    {
        $query = "select shutdown_port,ajp_port,http_port from 'tomcat-instances' where delete_flag=0";
        $q = mysqli_query( $this->connection,$query);
        $ports = array();
        if ($q) {
            while ($row = mysqli_fetch_array($q)) {
                array_push($ports, $row['shutdown_port']);
                array_push($ports, $row['ajp_port']);
                array_push($ports, $row['http_port']);
            }
        }
        else
            $ports = null;
        return $ports;
    }

    public function getTomcatInstancesByUser ($userName)
    {
        $query = "select * from `tomcat-instances` where user_name = '$userName'";
        return mysqli_query($this->connection, $query);
    }

    public function getTomcatInstancesCountByDomain ($domainName)
    {
        $query = "select * from `tomcat-instances` where domain_name = '$domainName' and delete_flag=0";
        $q = mysqli_query($this->connection, $query);
        if ($q)
            $count = mysqli_num_rows($q);
        else
            $count = 0;

        //echo mysql_error();
        return $count;
    }

    public function getInstance ($id)
    {
        $query = "select * from `tomcat-instances` where id = '$id'  and delete_flag=0 ";
        $result = mysqli_query($this->connection,$query );
        return mysqli_fetch_array($result);
    }

    public function getAllInstance ()
    {
        $query = "select * from `tomcat-instances` where  delete_flag=0 ";
        $result = mysqli_query($this->connection, $query);
        return $result;
    }

    public function getRecordForCron ()
    {
        $query = "select * from `tomcat-instances` where  cron_flag=0";
        return mysqli_query($this->connection, $query);
    }

    public function setCronFlag ($id, $value)
    {
        $id = mysqli_real_escape_string($this->connection, $id);
        $value = mysqli_real_escape_string($this->connection, $value);
        if ($value == 0 or $value == 1) {
            $query = "update `tomcat-instances` set cron_flag='$value'  where  delete_flag=0 and id='$id'";
            return mysqli_query($this->connection ,$query );
        }
        else
            return false;
    }

    public function setStatus ($id, $status)
    {
        $id = mysqli_real_escape_string($this->connection, $id);
        $query = "update `tomcat-instances` set status='$status' where  id='$id'";
        return mysqli_query($this->connection, $query);
    }

    public function setDeleteFlag ($id)
    {
        $id = mysqli_real_escape_string($this->connection, $id);
        $query = "update `tomcat-instances` set delete_flag='1' where  id='$id'";
        return mysqli_query($this->connection, $query);
    }

    public function setInstalledFlag ($id)
    {
        $id = mysqli_real_escape_string($this->connection, $id);
        $query = "update `tomcat-instances` set installed='1'  where  id='$id'";
        return mysqli_query($this->connection, $query);
    }

    public function hardDeleteTCInstance ($id, $userName)
    {
        $id = mysqli_real_escape_string($this->connection, $id);
        $userName = mysqli_real_escape_string($this->connection, $userName);
        $query = "delete from `tomcat-instances` where id='$id' and user_name='$userName'";
        return mysqli_query($this->connection, $query);
    }

    public function getUserNameByInstanceId ($instanceId)
    {
        $query = "select user_name from `tomcat-instances` where id='$instanceId'";
        $r = mysqli_query($this->connection, $query);
        $row = mysqli_fetch_array($r);
        return $row['user_name'];
    }

    public function __destruct ()
    {
        mysqli_close($this->connection);
    }

}

?>
