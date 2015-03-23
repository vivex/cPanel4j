echo "cPanel4J - Tomcat Installer Plugin For cPanel \n"

echo "Starting The Installation \n"

rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_installer_plugin.conf

rm -f /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_instance_plugin.conf

echo "width=>47,itemorder=>1,file=>tomcat_installer_plugin,description=>Tomcat Installer,itemdesc=>Tomcat Installer,height=>47,group=>my_plugin,subtype=>img,imgtype=>icon,url=>cpanel4j/page.live.php?action=create_instance,type=>image " > /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_installer_plugin.conf

echo "width=>47,itemorder=>2,file=>tomcat_instance_plugin,description=>View Tomcat Instances,itemdesc=>View Tomcat Instances,height=>47,group=>my_plugin,subtype=>img,imgtype=>icon,url=>cpanel4j/page.live.php?action=list,type=>image"  > /usr/local/cpanel/base/frontend/paper_lantern/dynamicui/dynamicui_tomcat_instance_plugin.conf

cp icons/tomcat_installer_plugin.png /usr/local/cpanel/base/frontend/paper_lantern/styled/basic/icons

cp icons/tomcat_instance_plugin.png /usr/local/cpanel/base/frontend/paper_lantern/styled/basic/icons

mkdir /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j

/usr/local/cpanel/bin/rebuild_sprites

cp -r cpanel4j/plugin/* /usr/local/cpanel/base/frontend/paper_lantern/cpanel4j


echo "\nIcons created"
php sql_install.php







