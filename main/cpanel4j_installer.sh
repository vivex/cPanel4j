echo "cPanel4J - Tomcat Installer Plugin For cPanel \n"

echo "Starting The Installation \n"

rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_installer.conf
rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_instances.conf
rm -f /usr/local/cpanel/base/frontend/x3/dynamicui/dynamicui_tomcat_installer.conf
rm -f /usr/local/cpanel/base/frontend/x3/dynamicui/dynamicui_tomcat_instances.conf

/usr/local/cpanel/scripts/install_plugin icon_installer.tar.gz

mkdir /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j

/usr/local/cpanel/bin/rebuild_sprites

cp Config.php plugin/

cp -r plugin/* /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j

ln -s /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j /usr/local/cpanel/base/frontend/x3/cpanel4j


cronCmd="* * * * * php /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j/cron.php > cpanel4j_Cron_log.txt"
isCronThere=$(grep "$cronCmd" /var/spool/cron/root)
if [ -z "$isCronThere" ]; then
   echo $cronCmd  >> /var/spool/cron/root
fi

startUpCmd="php /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j/startup.sh"
isStartThere=$(grep "$startUpCmd" /etc/rc.d/rc.local)

if [ -z "$isStartThere" ]; then
   echo $startUpCmd  >> /etc/rc.d/rc.local
fi

echo "\nIcons created"
php sql_install.php










