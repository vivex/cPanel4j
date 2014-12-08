#!/bin/bash
# setup-instance.sh

tomcatVersion="7"
instanceTemplate="/usr/local/tomcat-instance-template/"$tomcatVersion
username="bizwebsi"
domainName="bizwebsite.in"
connectorPort="8989"
ajpPort="3232"
shutDownPort="8023"
catalinaHome="/usr/share/apache-tomcat-7.0.57"
userTomcatDir=/home/$username/tomcat-$tomcatVersion/$domainName
mkdir -p $userTomcatDir
cp -r $instanceTemplate/logs $instanceTemplate/conf $instanceTemplate/temp $instanceTemplate/webapps $userTomcatDir

#Now writing conf/server.xml file
configFile='<?xml version="1.0" encoding="utf-8"?>
<Server port="'$shutDownPort'" shutdown="SHUTDOWN">
  <Listener className="org.apache.catalina.startup.VersionLoggerListener" />
  <Listener className="org.apache.catalina.core.AprLifecycleListener" SSLEngine="on" />
  <Listener className="org.apache.catalina.core.JasperListener" />
  <Listener className="org.apache.catalina.core.JreMemoryLeakPreventionListener" />
  <Listener className="org.apache.catalina.mbeans.GlobalResourcesLifecycleListener" />
  <Listener className="org.apache.catalina.core.ThreadLocalLeakPreventionListener" />
  <GlobalNamingResources>
    <Resource name="UserDatabase" auth="Container"
              type="org.apache.catalina.UserDatabase"
              description="User database that can be updated and saved"
              factory="org.apache.catalina.users.MemoryUserDatabaseFactory"
              pathname="conf/tomcat-users.xml" />
  </GlobalNamingResources>
  <Service name="Catalina">
    <Connector port="'$connectorPort'" protocol="HTTP/1.1"
               connectionTimeout="20000"
               redirectPort="8443" />
    <Connector port="'$ajpPort'" protocol="AJP/1.3" redirectPort="8443" />
    <Engine name="Catalina" defaultHost="localhost">
      <Realm className="org.apache.catalina.realm.LockOutRealm">
        <Realm className="org.apache.catalina.realm.UserDatabaseRealm"
               resourceName="UserDatabase"/>
      </Realm>

      <Host name="localhost"  appBase="webapps"
            unpackWARs="true" autoDeploy="true">
        <Valve className="org.apache.catalina.valves.AccessLogValve" directory="logs"
               prefix="localhost_access_log." suffix=".txt"
               pattern="%h %l %u %t &quot;%r&quot; %s %b" />

      </Host>
    </Engine>
  </Service>
</Server>'

echo $configFile  >> $userTomcatDir/conf/server.xml


#Now creating service file in to /etc/init.d/

configFile='#!/bin/bash
#description: Tomcat-'$domainName' start stop restart
#processname: tomcat-'$username'-'$domainName'
#chkconfig: 234 20 80
CATALINA_HOME='$catalinaHome'
export CATALINA_BASE='$userTomcatDir'
case $1 in
start)
sh $CATALINA_HOME/bin/startup.sh
;;
stop)
sh $CATALINA_HOME/bin/shutdown.sh
;;
restart)
sh $CATALINA_HOME/bin/shutdown.sh
sh $CATALINA_HOME/binstartup.sh
;;
esac
exit 0'

echo $configFile >> /etc/init.d/$username-$domainName-tomcat-$tomcatVersion

echo "DONE"


