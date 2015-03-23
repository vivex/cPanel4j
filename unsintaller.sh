//unsintaller.sh

echo "cPanel4J - UnInstaller"
rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_installer_plugin.conf
rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_instance_plugin.conf
rm /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_installer_plugin.conf
rm /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_instance_plugin.conf
rm /usr/local/cpanel/base/frontend/paper_lantern/styled/basic/icons/tomcat_installer_plugin.png
rm /usr/local/cpanel/base/frontend/paper_lantern/styled/basic/icons/tomcat_instance_plugin.png 
rm -rf /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j
/usr/local/cpanel/bin/rebuild_sprites
echo "Files Removed ,Now Removing Database"

php sql-uninstall.php


echo "User Files Have to be deleted Manually"







