---
title: Manual Database Manipulation
last_updated: Feb 07, 2019
permalink: Manual-Database-Manipulation.html
---

As a VCL administrator, there will likely be times when direct interaction with the VCL database is required.  You may need to query the database in order to troubleshoot a problem or run a custom query for a report.  There are also a very small number of configuration tasks that require direct interaction with the VCL database.

## Using phpMyAdmin

For most people, phpMyAdmin is an easier method of interacting with the database versus using the mysql command line utility.  Installation and configuration of phpMyAdmin is beyond the scope of this document.  For more information, see [https://www.phpmyadmin.net/.](https://www.phpmyadmin.net/.)

## Using the mysql Command Line Utility

The database may be queried or altered using the ***mysql*** utility.  Use this method only if you are comfortable using command line utilities and have some knowledge of [MariaDB](https://mariadb.com/kb/en/mariadb/sql-commands/) syntax.

### Installing the mysql Command Line Utility

The ***mysql binary*** is installed as part of the **mariadb** package.  If you are connecting to the VCL database from the console of the either the actual database server or a VCL management node, the mariadb package is probably already installed.  If you are connecting from another host and the ***mysql*** command is not available, install the **mariadb** package by executing the following command:

    $ sudo yum install -y mariadb

## Connecting to the VCL Database Server

### Connecting Directly From the Database Server

The easiest location to connect to the VCL database is from the console of the database server itself.  You may be able to simply run the following command:

    $ mysql -u <username> -p vcl
    Enter password:
    MariaDB [vcl]>

*You may see an error similar to the following:*

    ERROR 2002 (HY000): Can't connect to local MySQL server through socket '/srv/mysql/mysqld.sock' (2)

If you see this error, determine which Unix socket file the server is using:

    $ netstat -ln | grep mysql
    unix  2      [ ACC ]     STREAM     LISTENING     515320596 /var/run/mysql/mysqld.sock

And adjust the command accordingly:

    $ mysql -S /var/run/mysql/mysqld.sock -u <username> -p vcl
    Enter password:
    MariaDB [vcl]>

### Connecting From Another Host

If you don't have console or SSH access to the database server, you can probably connect from a VCL management node (assuming you have a management node running on a machine other than the database server).  The database server's firewall must allow traffic from the remote computer that **mysql** is executed on to the database server, usually TCP port 3306.

You will need to know the database server hostname or IP address, username, password, and name of the database (usually vcl).  If you are connecting from a VCL management node, the settings stored in the ***/etc/vcl/vcld.conf*** file should allow you to connect:

| **Setting in  /etc/vcl/vcld.conf**| **mysql Command Argument** |
| server=vcl-db-01.my.org | --host=vcl-db-01.my.org |
| LockerWrtUser=vclmn | --user=vclmn |
| wrtPass=<password> | -p, Enter password:
|                                    |~or --password=<password> |
| database=vcldb | --database=vcldb |


    $ mysql --host=vcl-db-01.my.org --user=vclmn -p --database=vcldb
    Enter password: <password>
    MariaDB [vcldb]>
