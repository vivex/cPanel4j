<?php
//Test.php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include "DBWrapper.php";
include "Tomcat.php";

$tomcat = new Tomcat();
$result = $tomcat->createInstance("VivekSoni.com", "vivek", "1");