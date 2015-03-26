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


$query1 = "DROP TABLE `tomcat-instances`";

$dbConnect = new DBConnect();
$connection = $dbConnect->getConnection();
mysql_query($query1,$connection);

echo "\n".mysql_error();
echo "\nDataBase Deleted";

?>
