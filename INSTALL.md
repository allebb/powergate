Installing PowerDNS and PowerGate
=================================

This guide is intended for and tested on Ubuntu Server (specifically Ubuntu Server 12.04 LTS) but should work equally well with other Debian based distrubution too.

Firstly, log onto your server and run the following commands, these steps should be configured on each of your DNS servers:

```shell
apt-get update && apt-get upgrade
apt-get install mysql-server
apt-get install pdns-server pdns-backend-mysql
```

This will install PowerDNS and the MySQL backend, PowerDNS is also capable of using various other bacekends such as SQLite, Postgres and Oracle to name a few but this guide is specifically for MySQL.

When you are greeted with the 'Configuring pdns-backend-mysql' prompt asking if you wish to *Configure database for pdns-backend-mysql with dbconfig-common?* select **No**, we'll manually set this up in the next few steps!

First of all we'll create a dedicated PowerDNS MySQL user of which PowerDNS will use to connect with and manipulate the PowerDNS data with, login to MySQL using the 'root' password you would have just been prompted for like so:

```shell
mysql -uroot -p
```

Now execute the following command to create a new MySQL user, dedicated MySQL table (for PowerDNS) and set permissions:

```sql
CREATE DATABASE powerdns;
CREATE USER 'pdns'@'127.0.0.1' IDENTIFIED BY 'YOUR_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON powerdns.* TO 'pdns'@'127.0.0.1';
FLUSH PRIVILEGES; 
```

Thats great, now execute the MySQL database creation script as follows:-

```sql
/**********************************************************
* PowerDNS Database Creation Script for MySQL             *
**********************************************************/

USE `powerdns`;

CREATE TABLE domains (
id INT auto_increment,
name VARCHAR(255) NOT NULL,
master VARCHAR(128) DEFAULT NULL,
last_check INT DEFAULT NULL,
type VARCHAR(6) NOT NULL,
notified_serial INT DEFAULT NULL,
account VARCHAR(40) DEFAULT NULL,
primary key (id)
)ENGINE=InnoDB;

CREATE TABLE records (
id INT auto_increment,
domain_id INT DEFAULT NULL,
name VARCHAR(255) DEFAULT NULL,
type VARCHAR(6) DEFAULT NULL,
content VARCHAR(255) DEFAULT NULL,
ttl INT DEFAULT NULL,
prio INT DEFAULT NULL,
change_date INT DEFAULT NULL,
primary key(id)
)ENGINE=InnoDB;

CREATE TABLE supermasters (
ip VARCHAR(25) NOT NULL,
nameserver VARCHAR(255) NOT NULL,
account VARCHAR(40) DEFAULT NULL
)ENGINE=InnoDB;

CREATE INDEX rec_name_index ON records(name);
CREATE INDEX nametype_index ON records(name,type);
CREATE INDEX domain_id ON records(domain_id);
CREATE UNIQUE INDEX name_index ON domains(name);
```

Perfect! - We are now done with the MySQL server side of things, lets move on to PowerDNS configuration...

Next we'll stop the PowerDNS daemon as Ubuntu starts this automatcally, we'll do this like so:

```shell
service pdns stop
```

Now we need to configure PowerDNS to use MySQL as the backend storage medium, we need to edit the configuration like so:

```shell
vi /etc/powerdns/pdns.conf
```

Now add the following line to the bottom of the file:

```
launch=gmysql
```

Save the file, and now lets edit the next file, this file will contain our MySQL server connection details:

```shell
vi /etc/powerdns/pdns.d/pdns.local
```

The contents of which should be added and edited to match your MySQL server credentials and database name etc:

```
gmysql-host=127.0.0.1
gmysql-user=pdns
gmysql-password=YOUR_PASSWORD_HERE
gmysql-dbname=powerdns
```

Perfect, we're about half way done now!

### On the master DNS server...

We need to edit the PowerDNS configuration file to set this server as a 'master' and to ensure that it will allow automatic replication of its data to the 'trusted' slave servers, translated this means we want to allow zone transferes and enable master operation.

To do this we need to edit the main PowerDNS configuration file like so:

```shell
vi /etc/powerdns/pdns.conf
```

Now uncomment the following configuration items and set their values to the following:

```
allow-axfr-ips=10.0.0.2
disable-axfr=no
master=yes
webserver=yes
webserver-address=0.0.0.0
```

As you can see from the above configuration lines, the `allow-axfr-ips` is set to 10.0.0.2 you will need to configure this with the appropriate IP address for your slave servers, you can add extra slave servers by seperating them with a comma!

By enabling the 'webserver' setting we can monitor statistics using a web browser, for example try accessing http://10.0.0.1:8081, you can limit the remote connections to localhost only if you wish by setting the webserver-address to '127.0.0.1'!

We should now be able to start the PowerDNS daemon and things should be ready to work (once some domains and records are added :))

We can start the server like so:

```shell
service pdns start
```

If you experience any errors, PowerDNS defaults logging to via `syslog`, you can check the contents using `cat /var/log/syslog`. 

The Master DNS server is the server of which is going to have PowerGate installed on and therefore as PowerGate is a RESTful API interface, we also need to install some extra bits and peices too, so we are now going to install the following dependencies:

* Nginx - A powerful yet lightweight web server.
* PHP 5.5 - Powergate is written in PHP on top of the Laravel framework, we need PHP in order for the application to run!


### On each of the Slave DNS servers

The slave server configuration is a little simplier (less configuration changes to make), so lets jump streight in and edit the PowerDNS configuration file like so:

```shell
vi /etc/powerdns/pdns.conf
```

Now we simply need to uncomment the `slave` option and set it to `yes` like so:

```
slave=yes
```

We now need to access the local MySQL database and add our master server (in this example we are using 'ns1.example' with an IP address of '10.0.0.1') to the `supermasters` table.

So lets execute the follow statement on the MySQL client:

```sql
INSERT INTO supermasters (ip, nameserver, account) VALUES ('10.0.0.1', 'ns2.example.com', '');
```

Keep in mind that, I'm assuming that the 'slave' server that you are executing the above query on is named 'ns2.example.com' you'll need to also ammend the 'ns2.example.com' to match your current slave server!