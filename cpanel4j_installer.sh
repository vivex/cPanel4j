#!/bin/bash

###############################################################
##                                                           ##
##                  cPanel4j Installer File                  ##
##           Will install icons, files & database            ##
##               Author contact@viveksoni.net                ##
##             Copyright (C) 2015  Vivek Soni                ##
##   Instructions & More Info -> cpanel4j.viveksoni.net      ##
##       Released under the GNU General Public License       ##
##                                                           ##
###############################################################
 
echo "cPanel4J - Tomcat Installer Plugin For cPanel "

echo "Starting The Installation "

rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_installer.conf
rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_instances.conf

/usr/local/cpanel/scripts/install_plugin icon_installer.tar.gz

/usr/local/cpanel/bin/rebuild_sprites

mkdir /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j


cp -r plugin/* /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j

cp Config.php cPanel4jCore/

mv  cPanel4jCore /

chmod -R 755 /cPanel4jCore/tomcat-7.0.59-template /cPanel4jCore/tomcat-8.0.18-template

touch /cPanel4jCore/cPanel4JLog.log
chmod 777 /cPanel4jCore/cPanel4JLog.log

cronCmd="* * * * * php /cPanel4jCore/cron.php > /cPanel4jCore/cpanel4j_cron.log"
isCronThere=$(grep "$cronCmd" /var/spool/cron/root)
if [ -z "$isCronThere" ]; then

  cat <<EndXML >> /var/spool/cron/root
* * * * * php /cPanel4jCore/cron.php > /cPanel4jCore/cpanel4j_cron.log
EndXML
fi

startUpCmd="php /cPanel4jCore/startup.sh"
isStartThere=$(grep "$startUpCmd" /etc/rc.d/rc.local)

if [ -z "$isStartThere" ]; then
   echo $startUpCmd  >> /etc/rc.d/rc.local
fi

echo "Icons created"

php sql_install.php










