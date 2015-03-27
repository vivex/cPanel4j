echo "cPanel4J - Tomcat Installer Plugin For cPanel \n"

echo "Starting The Installation \n"

rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_installer.conf
rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_instances.conf
rm -f /usr/local/cpanel/base/frontend/x3/dynamicui/dynamicui_tomcat_installer.conf
rm -f /usr/local/cpanel/base/frontend/x3/dynamicui/dynamicui_tomcat_instances.conf

/usr/local/cpanel/scripts/install_plugin icon_installer.tar.gz

/usr/local/cpanel/bin/rebuild_sprites

mkdir /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j
mkdir /usr/local/cpanel/base/frontend/x3/cpanel4j


cp -r plugin/* /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j
cp -r plugin_x3/* /usr/local/cpanel/base/frontend/x3/cpanel4j

cp Config.php cPanel4jCore/

cp -r cPanel4jCore /

chmod -R 755 /cPanel4jCore/tomcat-7.0.59-template /cPanel4jCore/tomcat-8.0.18-template


cronCmd="* * * * * php /cPanel4jCore/cron.php > cpanel4j_Cron_log.txt"
isCronThere=$(grep "$cronCmd" /var/spool/cron/root)
if [ -z "$isCronThere" ]; then

  cat <<EndXML >> /var/spool/cron/root
* * * * * php /cPanel4jCore/cron.php > cpanel4j_Cron_log.txt
EndXML
fi

startUpCmd="php /cPanel4jCore/startup.sh"
isStartThere=$(grep "$startUpCmd" /etc/rc.d/rc.local)

if [ -z "$isStartThere" ]; then
   echo $startUpCmd  >> /etc/rc.d/rc.local
fi

echo "\nIcons created"
php sql_install.php










