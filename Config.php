<?php

namespace cPanel4jCore;

/**
 * Author: VIVEK SONI (contact@viveksoni.net)
 * Tomcat Class
 * Plugin Directory: /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j
 * Cron Command: * * * * * php /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j/cron.php > cpanel4j_Cron_log.txt
 *
 */
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
