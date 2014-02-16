#Installing PowerDNS and PowerGate

This guide is intended for and tested on Ubuntu Server (specifically Ubuntu Server 12.04 LTS) but should work equally well with other Debian based distrubution too.

## Server installation

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

Now uncomment the `launch` setting and set the value to `gmysql` like so:

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
gmysql-dnssec=no
```

Perfect, we're about half way done now!

### On the master DNS server only...

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

You are now done for the master server!

Now you need to configure each of your slave servers!


### On each of the Slave DNS servers...

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
USE powerdns;
INSERT INTO supermasters (ip, nameserver, account) VALUES ('10.0.0.1', 'ns2.example.com', '');
exit
```

Keep in mind that, I'm assuming that the 'slave' server that you are executing the above query on is named 'ns2.example.com' you'll need to also ammend the 'ns2.example.com' to match your current slave server!

Now start the slave DNS server like so:

```shell
service pdns start
```

In the same way as on the master DNS server, you can check the logs for PowerDNS using Syslog for example `cat /var/log/syslog`.

## Testing our Master-Slave configuration

Once you've installed each of your servers it is a good idea to first check that there are no logs in `/var/log/syslog` for each of your DNS servers.

Next, we'll log on to the master server and manually enter a new domain like so:

```shell
mysql -uroot -p
```

Now run the following MySQL queries to create a new domain and a couple of records...

```sql
USE powerdns;

```

Now exit mysql and log on to one of your slave servers, if you use MySQL on there you should see the records have now been replicated/transfered.

You can test by attempting to emulate a DNS lookup against any one of your DNS servers like so:-

```shell
dig @10.0.0.2 example.com
```
The above example shows us testing DNS resolution against our NS2 slave server (hence 10.0.0.2 as the lookup server).

You can use this to check if DNS records etc. have properly transfered to each of your DNS servers.

If you got a response like this:

```shell
root@some-machine:~# dig @172.25.87.202 example.com

; <<>> DiG 9.8.1-P1 <<>> @172.25.87.202 example.com
; (1 server found)
;; global options: +cmd
;; Got answer:
;; ->>HEADER<<- opcode: QUERY, status: NOERROR, id: 15043
;; flags: qr aa rd; QUERY: 1, ANSWER: 0, AUTHORITY: 1, ADDITIONAL: 0
;; WARNING: recursion requested but not available

;; QUESTION SECTION:
;example.com.			IN	A

;; AUTHORITY SECTION:
example.com.		86400	IN	SOA	ns1.example.com. hostmaster.example.com. 2 10800 3600 604800 3600

;; Query time: 28 msec
;; SERVER: 172.25.87.202#53(172.25.87.202)
;; WHEN: Sun Feb 16 15:39:54 2014
;; MSG SIZE  rcvd: 80

root@some-machine:~# 
```

Checking the MySQL database on the slave server should now display both records in the `domains` table and records inf the `records` table, for example:

```shell
mysql> use powerdns;
Reading table information for completion of table and column names
You can turn off this feature to get a quicker startup with -A

Database changed
mysql> select * from domains;
+----+-------------+---------------+------------+-------+-----------------+---------+
| id | name        | master        | last_check | type  | notified_serial | account |
+----+-------------+---------------+------------+-------+-----------------+---------+
|  1 | example.com | 10.0.0.1 | 1392564097 | SLAVE |            NULL |         |
+----+-------------+---------------+------------+-------+-----------------+---------+
1 row in set (0.00 sec)

mysql> select * from records;
+----+-----------+-----------------+------+-------------------------------------------------------------------+-------+------+-------------+
| id | domain_id | name            | type | content                                                           | ttl   | prio | change_date |
+----+-----------+-----------------+------+-------------------------------------------------------------------+-------+------+-------------+
|  1 |         1 | example.com     | SOA  | ns1.example.com. hostmaster.example.com. 2 10800 3600 604800 3600 | 86400 |    0 |        NULL |
|  2 |         1 | example.com     | NS   | ns1.example.com                                                   | 86400 |    0 |        NULL |
|  3 |         1 | example.com     | NS   | ns2.example.com                                                   | 86400 |    0 |        NULL |
|  4 |         1 | ns1.example.com | A    | 10.0.0.1                                                     | 86400 |    0 |        NULL |
|  5 |         1 | ns2.example.com | A    | 10.0.0.2                                                     | 86400 |    0 |        NULL |
+----+-----------+-----------------+------+-------------------------------------------------------------------+-------+------+-------------+
5 rows in set (0.00 sec)

mysql>
```

Congratulations, it would appear that your Master-Slave setup is now complete!

If you did not recieve a repsonse simular to the above then please move on the the 'Troubleshooting DNS' section below.

## Troubleshooting DNS

* Restart DNS services on each of your DNS servers when making configuration changes like so `service pdns restart`.
* Useful log infomation can be found in `/var/log/syslog` and is recommended that when you experience issues that this is the first place you check on both the MASTER DNS and SLAVE servers.
* If the slave server is NOT recieving updates, try executing `pdns_control notify-hosts example.com 10.0.0.2` to force a notificatio for the required domain to the slave. (replacing the IP address of the slave that is not getting the update(s))
* If you've recently added a new slave DNS server but it is not getting updates ensure that you have added the IP address to the master server's `allow-axfr-ips` configuration line, multiple IP addresses should be seperated with a comma.
* If you wish to emulate an automatic update, you can increment the SOA of a domain like so to trigger an update `UPDATE records SET content = 'ns1.example.com hostmaster.example.org 3' WHERE type = 'SOA' AND name = 'example.com';` (In this example I've incremented the SOA serial from '2' to '3'). After such an update is executed on the master, you should see an entry in the syslog (on the master server) like so:

		```
		Feb 16 15:46:18 ns1 pdns[4661]: 1 domain for which we are master needs notifications
		Feb 16 15:46:18 ns1 pdns[4661]: Queued notification of domain 'example.com' to 172.25.87.201
		Feb 16 15:46:18 ns1 pdns[4661]: Queued notification of domain 'example.com' to 172.25.87.202
		Feb 16 15:46:18 ns1 pdns[4661]: Received NOTIFY for example.com from 172.25.87.201 but slave support is disabled in the configuration
		Feb 16 15:46:18 ns1 pdns[4661]: gmysql Connection successful
		Feb 16 15:46:18 ns1 pdns[4661]: AXFR of domain 'example.com' initiated by 172.25.87.202
		Feb 16 15:46:18 ns1 pdns[4661]: gmysql Connection successful
		Feb 16 15:46:18 ns1 pdns[4661]: gmysql Connection successful
		Feb 16 15:46:18 ns1 pdns[4661]: AXFR of domain 'example.com' to 10.0.0.2 finished
		Feb 16 15:46:19 ns1 pdns[4661]: Received unsuccessful notification report for 'example.com' from 10.0.0.1:53, rcode: 4
		Feb 16 15:46:19 ns1 pdns[4661]: Removed from notification list: 'example.com' to 172.25.87.201:53
		Feb 16 15:46:19 ns1 pdns[4661]: Removed from notification list: 'example.com' to 172.25.87.202:53 (was acknowledged)
		Feb 16 15:46:21 ns1 pdns[4661]: No master domains need notifications
		```
		
The data should be replicated on the slave now and if you check the syslog on the slave servers they should also have the follow data:

	```Feb 16 15:46:18 ns2 pdns[2120]: 1 slave domain needs checking, 0 queued for AXFR
		Feb 16 15:46:18 ns2 pdns[2120]: Received serial number updates for 1 zones, had 0 timeouts
		Feb 16 15:46:18 ns2 pdns[2120]: Domain example.com is stale, master serial 3, our serial 2
		Feb 16 15:46:18 ns2 pdns[2120]: Initiating transfer of 'example.com' from remote '172.25.87.201'
		Feb 16 15:46:18 ns2 pdns[2120]: gmysql Connection successful
		Feb 16 15:46:18  pdns[2120]: last message repeated 2 times
		Feb 16 15:46:18 ns2 pdns[2120]: AXFR started for 'example.com', transaction started
		Feb 16 15:46:18 ns2 pdns[2120]: AXFR done for 'example.com', zone committed
		```


## Example configuration files

My versions of the **/etc/powerdns/pdns.conf** files can be viewed here:

* [Master Server](https://gist.github.com/bobsta63/9036100) - The file resided on ns1.example.com (10.0.0.1)
* [Slave Server](https://gist.github.com/bobsta63/9036453) - The file resided on ns2.example.com (10.0.0.2)

I'm not suggesting that you should use these or possible enable other features such as chroot'd enviroment but I'm providing these configuration here as 'working' examples.
