<?php

require_once "DBConnection.php";
class DBWrapper extends DBConnection{

private $connection;

public function __construct(){
    $this->connection = mysql_connect($this->host, $this->userName, $this->password);
    mysql_select_db($this->database, $this->connection);
}

public function insertTomcatInstance($userName,$domainName,$httpPort,$ajpPort,$shutDownPort,$tomcatVersion){
	$now = new DateTime();
$createDate = $now->format('Y-m-d H:i:s');    
	$query= "insert into `tomcat-instances` (user_name,domain_name,tomcat_version,shutdown_port,
		http_port,ajp_port,create_date) values('$userName','$domainName','$tomcatVersion','$shutDownPort',
		'$httpPort','$ajpPort','$createDate')";
 	$q = mysql_query($query,$this->connection);
 	//echo mysql_error();
 	return ($q) ? true : false ;
}

public function getAllPorts(){
	$query = "select shutdown_port,ajp_port,http_port from 'tomcat-instances' where delete_flag=0";
 	$q = mysql_query($query,$this->connection);
 	$ports = array();
 	if($q){
 		while($row = mysql_fetch_array($q)){
 		array_push($ports, $row['shutdown_port']);
 		array_push($ports, $row['ajp_port']);
 		array_push($ports, $row['http_port']);
 	}}
 	else $ports=null;
 	return $ports;
}

public function getTomcatInstancesByUser($userName){
	$query = "select * from `tomcat-instances` where user_name = '$userName'";
	return mysql_query($query,$this->connection);
}

public function getTomcatInstancesCountByDomain($domainName){
	$query = "select * from `tomcat-instances` where domain_name = '$domainName' and delete_flag=0";
	$q = mysql_query($query,$this->connection);
	if($q)
	$count =  mysql_num_rows($q);
	else $count = 0;

	//echo mysql_error();
	return $count;

}

public function getInstance($id){
	$query = "select * from `tomcat-instances` where id = '$id'  delete_flag=0 ";
	$result =  mysql_query($query,$this->connection);
	return mysql_fetch_array($result);
}

public function getRecordForCron(){
	$query = "select * from `tomcat-instances` where  cron_flag=0";
	return mysql_query($query,$this->connection);

}

public function setCronFlag($id){
	$id = mysql_real_escape_string($id);
	$query = "update `tomcat-instances` set cron_flag=1 where  delete_flag=0 and id='$id'";
	return mysql_query($query,$this->connection);
}

public function hardDeleteTCInstance($id,$userName){
	$id = mysql_real_escape_string($id);
	$userName = mysql_real_escape_string($userName);
	$query = "delete from `tomcat-instances` where id='$id' and user_name='$userName'";
	return mysql_query($query,$this->connection);
}
public function setCronDeleteFlag($id,$userName){
	$id = mysql_real_escape_string($id);
	$userName = mysql_real_escape_string($userName);
	$query = "update `tomcat-instances` set cron_flag=0,delete_flag=1 where  id='$id' and user_name='$userName'";
	return mysql_query($query,$this->connection);
}
public function __destruct() {
	mysql_close($this->connection);
}

}

?>