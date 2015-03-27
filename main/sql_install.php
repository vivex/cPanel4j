<?php
/* MYSQL Commands fo cPanel4J */
include 'Config.php';

class DBConnect extends Config {
    private $connection;
    public function __construct(){
        $this->connection = mysql_connect($this->host, $this->userName, $this->password);
        mysql_select_db($this->database, $this->connection);
    }
    
    public function getConnection(){
        return $this->connection;
    }
}


$query1 = "CREATE TABLE `tomcat-instances` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_name` varchar(300) NOT NULL,
 `domain_name` varchar(300) NOT NULL,
 `tomcat_version` varchar(200) NOT NULL,
 `shutdown_port` int(11) NOT NULL,
 `http_port` int(11) NOT NULL,
 `ajp_port` int(11) NOT NULL,
 `create_date` datetime NOT NULL,
 `delete_flag` tinyint(4) NOT NULL,
 `cron_flag` tinyint(4) NOT NULL,
 `status` varchar(100) NOT NULL,
 `installed` int(11) NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`),
 UNIQUE KEY `shutdown_port` (`shutdown_port`),
 UNIQUE KEY `http_port` (`http_port`),
 UNIQUE KEY `ajp_port` (`ajp_port`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1";

$dbConnect = new DBConnect();
$connection = $dbConnect->getConnection();
mysql_query($query1,$connection);

echo "\n".mysql_error();
echo "\nDataBase Created";

?>
