<?php

//this file should be unEncrypted

namespace cPanel4jCore;

class Config
{

    protected $host = "localhost";
    protected $userName = "root";
    protected $password = "root";
    protected $database = "cpanel4j";
    public $javaHome = "/usr/local/jdk/";
    protected $reservedPorts = array('8080', '80', '25565', '3306', '2638', '2086', '2087', '2095', '2096', '2083', '2082', '587', '3776');

}

?>
