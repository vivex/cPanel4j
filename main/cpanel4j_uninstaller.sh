//unsintaller.sh

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

php sql-uninstall.php


echo "\n cPanel4j Removed Succesfully. \n You have to delete software file manually by running command  \"rm -rf /cPanel4jCore\" "

echo "\n Open etc/rc.d/rc.local file and remove line which contains php /cPanel4jCore/startup.sh"

echo "Thats it"







