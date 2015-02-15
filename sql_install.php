/* MYSQL Commands fo cPanel4J */

create user 'cpanel4j'@'localhost' IDENTIFIED BY 'testing';



CREATE TABLE `tomcat-instances` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_name` varchar(300) NOT NULL,
 `domain_name` varchar(300) NOT NULL,
 `tomcat_version` float NOT NULL,
 `shutdown_port` int(11) NOT NULL,
 `http_port` int(11) NOT NULL,
 `ajp_port` int(11) NOT NULL,
 `create_date` datetime NOT NULL,
 `delete_flag` tinyint(4) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
