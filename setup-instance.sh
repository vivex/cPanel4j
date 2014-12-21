#!/bin/bash
# setup-instance.sh
# setup-instance.sh domain.com username version connectorPort ajpport shutdownport
if [ "$#" -ne 6 ] ; then
echo "Error: invalid arguments"
exit
fi

domainName=$1
username=$2
tomcatVersion=$3
connectorPort=$4
ajpPort=$5
shutDownPort=$6

re='^[0-9]+$'
if ! [[ $connectorPort =~ $re ]] ||  ! [[ $ajpPort =~ $re ]] ||  ! [[ $shutDownPort =~ $re ]]; then
   echo "Error: Invalid Port Numbers" 
exit
fi

instanceTemplate=$tomcatVersion
catalinaHome="/usr/share/apache-tomcat-"$tomcatVersion

userTomcatDir=/home/$username/public_html/$domainName/tomcat-$tomcatVersion/

mkdir -p $userTomcatDir

if [ ! -d  "$instanceTemplate" ] || [ ! -d "$userTomcatDir" ] || [ ! -d "$catalinaHome" ] || [ ! -d "$instanceTemplate/temp" ] || [ ! -d "$instanceTemplate/conf" ] ||  [ ! -d "$instanceTemplate/webapps" ] ||  [ ! -d "$instanceTemplate/logs" ] ; then
echo "Error: Instance Template Directory not exists"
exit
fi

cp -r $instanceTemplate/logs $instanceTemplate/conf $instanceTemplate/temp $instanceTemplate/webapps $userTomcatDir

#Now writing conf/server.xml file
configFile='<?xml version="1.0" encoding="utf-8"?>\n
<Server port="'$shutDownPort'" shutdown="SHUTDOWN">\n
  <Listener className="org.apache.catalina.startup.VersionLoggerListener" />\n
  <Listener className="org.apache.catalina.core.AprLifecycleListener" SSLEngine="on" />\n
  <Listener className="org.apache.catalina.core.JasperListener" />\n
  <Listener className="org.apache.catalina.core.JreMemoryLeakPreventionListener" />\n
  <Listener className="org.apache.catalina.mbeans.GlobalResourcesLifecycleListener" />\n
  <Listener className="org.apache.catalina.core.ThreadLocalLeakPreventionListener" />\n
  <GlobalNamingResources>\n
    <Resource name="UserDatabase" auth="Container"\n
              type="org.apache.catalina.UserDatabase"\n
              description="User database that can be updated and saved"\n
              factory="org.apache.catalina.users.MemoryUserDatabaseFactory"\n
              pathname="conf/tomcat-users.xml" />\n
  </GlobalNamingResources>\n
  <Service name="Catalina">\n
    <Connector port="'$connectorPort'" protocol="HTTP/1.1"\n
               connectionTimeout="20000"\n
               redirectPort="8443" />\n
    <Connector port="'$ajpPort'" protocol="AJP/1.3" redirectPort="8443" />\n
    <Engine name="Catalina" defaultHost="localhost">\n
      <Realm className="org.apache.catalina.realm.LockOutRealm">\n
        <Realm className="org.apache.catalina.realm.UserDatabaseRealm"\n
               resourceName="UserDatabase"/>\n
      </Realm>\n

      <Host name="localhost"  appBase="webapps"\n
            unpackWARs="true" autoDeploy="true">\n
        <Valve className="org.apache.catalina.valves.AccessLogValve" directory="logs"\n
               prefix="localhost_access_log." suffix=".txt"\n
               pattern="%h %l %u %t &quot;%r&quot; %s %b" />\n
\n
      </Host>\n
    </Engine>\n
  </Service>\n
</Server>\n'

echo -e $configFile  >> $userTomcatDir/conf/server.xml


#Now creating service file in to /etc/init.d/

configFile='#!/bin/bash \n
#description: Tomcat-'$domainName' start stop restart \n
#processname: tomcat-'$username'-'$domainName' \n
#chkconfig: 234 20 80 \n
CATALINA_HOME='$catalinaHome' \n
export CATALINA_BASE='$userTomcatDir' \n
case $1 in \n
start) \n
sh $CATALINA_HOME/bin/startup.sh \n
;; \n
stop) \n
sh $CATALINA_HOME/bin/shutdown.sh \n
;; \n
restart) \n
sh $CATALINA_HOME/bin/shutdown.sh \n
sh $CATALINA_HOME/binstartup.sh \n
;; \n
esac \n
exit 0\'

echo -e $configFile >> /etc/init.d/$username-$domainName-tomcat-$tomcatVersion

# Almost Done 


echo "DONE"


