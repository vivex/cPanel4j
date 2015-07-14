<?php

/**
 * cPanel4J
 * sql_install.php
 * Author: Vivek Soni (contact@viveksoni.net)
 * Instructions & More Info: www.cpanel4j.com
 * Released under the GNU General Public License
 */

namespace cPanel4jCore;

include 'Config.php';



/* MYSQL Commands to remove tomcat-instances table  */

class DBConnect extends Config
{

    private $connection;

    public function __construct ()
    {
        $this->connection = mysql_connect($this->host, $this->userName, $this->password);
        mysql_select_db($this->database, $this->connection);
    }

    public function getConnection ()
    {
        return $this->connection;
    }

}

$query1 = "DROP TABLE `tomcat-instances`";

$dbConnect = new DBConnect();
$connection = $dbConnect->getConnection();
mysql_query($query1, $connection);

echo "\n" . mysql_error();
echo "\nDataBase Deleted \n";
?>
