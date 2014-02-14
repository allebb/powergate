Installing PowerGate
====================

If you haven't already create a new database on your server named ```powerdns```, if you wish to use MySQL as the datastorage medium, execute the following dabase creation script, this will populate your MySQL database with the tables required.

```sql
/**********************************************************
* PowerDNS Database Creation Script for MySQL             *
**********************************************************/

USE `bindhub_dns`;

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

Then continue to install PowerDNS on your MASTER server (The server of which will also run PowerGate) as follows:-

TBD