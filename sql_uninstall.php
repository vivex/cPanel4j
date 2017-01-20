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
        $this->connection = mysqli_connect($this->host, $this->userName, $this->password, $this->database);
    }

    public function getConnection ()
    {
        return $this->connection;
    }

}

$query1 = "DROP TABLE `tomcat-instances`";

$dbConnect = new DBConnect();
$connection = $dbConnect->getConnection();
mysqli_query($connection, $query1);

echo "\n" . mysql_error();
echo "\nDataBase Deleted \n";
?>
