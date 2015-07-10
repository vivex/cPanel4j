//unsintaller.sh
###############################################################
##                                                           ##
##                cPanel4j UnInstaller File                  ##
##               Will remove icons & database                ##
##               Author contact@viveksoni.net                ##
##             Copyright (C) 2015  Vivek Soni                ##
##        Instructions & More Info -> www.cpnel4j.com        ##
##       Released under the GNU General Public License       ##
##                                                           ##
###############################################################
echo "\ncPanel4J - UnInstaller"
rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_installer.conf
rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_instances.conf
rm -f /usr/local/cpanel/base/frontend/x3/dynamicui/dynamicui_tomcat_installer.conf
rm -f /usr/local/cpanel/base/frontend/x3/dynamicui/dynamicui_tomcat_instances.conf


rm -rf /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j
rm -rf /usr/local/cpanel/base/frontend/x3/cpanel4j
rm -rf /cPanel4jCore


/usr/local/cpanel/bin/rebuild_sprites
echo "\nFiles Removed ,Now Removing Database"

php sql_uninstall.php

echo "************************************************************************"

echo "              cPanel4j Removed Succesfully.                             "
echo "   Delete software file manually by running command  \"rm -rf /cPanel4jCore\" "
echo " Open etc/rc.d/rc.local file and remove line which contains php /cPanel4jCore/startup.sh "

echo "                           Thats it                                             " 

echo "************************************************************************"







