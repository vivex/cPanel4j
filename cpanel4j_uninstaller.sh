//unsintaller.sh

echo "cPanel4J - UnInstaller"
rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_installer.conf
rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_instances.conf
rm -f /usr/local/cpanel/base/frontend/x3/dynamicui/dynamicui_tomcat_installer.conf
rm -f /usr/local/cpanel/base/frontend/x3/dynamicui/dynamicui_tomcat_instances.conf


rm -rf /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j
/usr/local/cpanel/bin/rebuild_sprites
echo "Files Removed ,Now Removing Database"

php sql-uninstall.php


echo "User Files Have to be deleted Manually"







