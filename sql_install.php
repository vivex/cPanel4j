<?php

/**
 * sql_install.php
 * Author: Vivek Soni (contact@viveksoni.net)
 * Instructions & More Info: www.cpanel4j.com
 * Released under the GNU General Public License
 */
/* MYSQL Commands fo cPanel4J */

namespace cPanel4jCore;

include 'Config.php';

/**
 * Will create the tomcat-instances table
 */
class DBConnect extends Config
{

    private $connection;

    public function __construct ()
    {
        $this->connection = mysqli_connect($this->host, $this->userName, $this->password, $this->database);
    }

    public function getConnection ()
    {
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
 `delete_flag` tinyint(4) NOT NULL DEFAULT  '0',
 `cron_flag` tinyint(4) NOT NULL DEFAULT  '0',
 `status` varchar(100)  NULL DEFAULT NULL ,
 `installed` int(11) NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`),
 UNIQUE KEY `shutdown_port` (`shutdown_port`),
 UNIQUE KEY `http_port` (`http_port`),
 UNIQUE KEY `ajp_port` (`ajp_port`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1";

$dbConnect = new DBConnect();
$connection = $dbConnect->getConnection();
mysqli_query($connection, $query1);

echo "\n" . mysql_error();
echo "\nDataBase Created \n";
?>
