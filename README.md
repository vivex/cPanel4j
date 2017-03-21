# cPanel4j
Tomcat Installer Plugin For cPanel (Shared Hosting Control Panel)

#Installation Instructions
Follow the below url for updated installation instruction - 

http://cpanel4j.com/how-to-install-cpanel4j/



To install cPanel4j Your system must match following requirements-

- CentOs ( >=6)

- cPanel (>= version 11.0)

- Java (JRE version =7 )

- MySQL (also create a database & user for cpanel4j)

- PHP
- ModSecurity™ Tools Disabled

Make sure that  there is no instance of tomcat already installed on your system.

Make sure java is installed properly by typing echo $JAVA_HOME

Make sure you have disabled the ModSecurity™ Tools because this tool blocks redirect to tomcat

Download the zip folder  from github (https://github.com/vivex/cPanel4j )

cd /
wget https://github.com/vivex/cPanel4j/archive/master.zip
Unzip this zip file –

unzip master.zip
Go to cPanel4j-master folder

cd cPanel4j-master
open Config.php and enter the database info

vi Config.php
Fill database connection info , in javaHome put the java home (you can get it by typing echo $JAVA_HOME)  and save this file (:wq).

now run the cPanel4j Installer –

sh cpanel4j_installer.sh
It will install the cpanel4j into your cPanel system, now you can see cpanel4j icons in your cPanel.

