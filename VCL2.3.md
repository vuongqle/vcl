---
title: VCL 2.3 Installation
last_updated: Nov 9, 2018
sidebar: mydoc_sidebar
permalink: VCL-2.3-installation.html
---

# Install & Configure:

1. [Install and Configure the Database](#install-and-configure-the-database)
2. [Install and Configure the Web Components](#install-and-configure-the-web-components)
3. [Install and Configure the Management Node Components](#install-and-configure-the-management-node-components)
4. [Configure Authentication](#configure-authentication)

## Install and Configure the Database


### 1. Download & Extract the Apache VCL Source

1. If you have not already done so, download and the Apache VCL source to the database server:

        wget --trust-server-names ''

2. Extract the files:

        tar -jxvf apache-VCL-2.3.tar.bz2

### 2. Install MySQL Server
1. Install MySQL Server 5.x:

        yum install mysql-server -y

2. Configure the MySQL daemon (mysqld) to start automatically:

        /sbin/chkconfig --level 345 mysqld on

3. Start the MySQL daemon:

        /sbin/service mysqld start

4. If the iptables firewall is being used and the web server and management nodes will be on different machines, port 3306 should be opend up

        vi /etc/sysconfig/iptables


        -A RH-Firewall-1-INPUT -m state --state NEW -s <web server IP> -p tcp --dport 3306 -j ACCEPT
        -A RH-Firewall-1-INPUT -m state --state NEW -s <management node IP> -p tcp --dport 3306 -j ACCEPT
        service iptables restart

### 3.  Create the VCL Database
1. Run the MySQL command-line client:

        mysql

2. Create a database:

        CREATE DATABASE vcl;

3. Create a user with SELECT, INSERT, UPDATE, DELETE, and CREATE TEMPORARY TABLES privileges on the database you just created:

        GRANT SELECT,INSERT,UPDATE,DELETE,CREATE TEMPORARY TABLES ON vcl.* TO 'vcluser'@'localhost' IDENTIFIED BY 'vcluserpassword';



***Replace vcluser and vcluserpassword with that of the user you want to use to connect to the database***


**The GRANT command will automatically create the user if it doesn't already exist**

4. Exit the MySQL command-line client:

        exit

5. Import the vcl.sql file into the database

        mysql vcl < apache-VCL-2.3/mysql/vcl.sql


    The vcl.sql file is included in the mysql directory within the Apache VCL source code

## Install and Configure the Web Components

### Prerequisites


The following instructions assume these tasks have previously been completed:
* [Apache VCL 2.3 has been downloaded](https://vcl.apache.org/downloads/download.cgi)

* [VCL database has been installed and configured](VCL-2.3-Database-Installation.html)


##### Web Server:

* Apache HTTP Server v1.3 or v2.x with SSL enabled

* PHP 5.0 or later


    <div markdown="span" class="alert alert-info" role="alert"><i class="fa fa-info-circle"></i> <b>Note:
</b> The VCL web frontend may run under other web server platforms capable of running PHP code, but has only been tested to work with Apache HTTP Server.</div>


    ***The VCL web frontend may run under other web server platforms capable of running PHP code, but has only been tested to work with Apache HTTP Server.***

##### Required Linux Packages:

* httpd - Apache HTTP Server
* mod_ssl - SSL/TLS module for the Apache HTTP server
* php - The PHP HTML-embedded scripting language
* libmcrypt - Encryption algorithms library (this requirement can be removed with a [patch](Patch-to-remove-mcrypt-dependency.html))

##### Required PHP Modules:

Required php Modules:

* php-gd
* php-json (required if your PHP version is 5.2 or later)
* php-mysql
* php-openssl
* php-sysvsem
* php-xml
* php-xmlrpc
* php-ldap (if you will be using LDAP authentication)
* php-process (for RHEL/CentOS 6)

### 1. Install the Required Linux Packages & PHP Modules

1. If your web server is running a Red Hat-based OS, the required components can be installed with:

    For RHEL / CentOS 5

            yum install httpd mod_ssl php php-gd php-mysql php-xml php-xmlrpc php-ldap -

    For RHEL / CentOS 6

            yum install httpd mod_ssl php php-gd php-mysql php-xml php-xmlrpc php-ldap php-process -y

    ***You may need the optional server rpm repository for the php-process package to add this run the following command:***

            rhn-channel --add --channel=rhel-x86_64-server-optional-6

2. Configure the web server daemon (httpd) to start automatically:

        /sbin/chkconfig --level 345 httpd on

3. Start the web server daemon:

        /sbin/service httpd start

4. If SELinux is enabled, run the following command to allow the web server to connect to the database:

        /usr/sbin/setsebool -P httpd_can_network_connect=1

5. If the iptables firewall is being used, port 80 and 443 should be opened up:

        vi /etc/sysconfig/iptables


        -A RH-Firewall-1-INPUT -m state --state NEW -p tcp --dport 80 -j ACCEPT
        -A RH-Firewall-1-INPUT -m state --state NEW -p tcp --dport 443 -j ACCEPT


        service iptables restart

### 2. Install the VCL Frontend Web Code

 1. If you have not already done so, download and extract the source files on the web server:

        wget --trust-server-names ''

        tar -jxvf apache-VCL-2.3.tar.bz2

2. Copy the web directory to a location under the web root of your web server and navigate to the destination .ht-inc subdirectory:

        cp -r apache-VCL-2.3/web/ /var/www/html/vcl

        cd /var/www/html/vcl/.ht-inc

3. Copy secrets-default.php to secrets.php:

        cp secrets-default.php secrets.php

4. Edit the secrets.php file:

        vi secrets.php

    * Set the following variables to match your database configuration:
        * $vclhost
        * $vcldb
        * $vclusername
        * $vclpassword
    * Create random passwords for the following variables:
        * $cryptkey
        * $pemkey
    * Save the secrets.php file

5. Run the genkeys.sh

        ./genkeys.sh

6. Copy **conf-default.php** to **conf.php**:

        cp conf-default.php conf.php

7. Modify conf.php to match your site
~~~~~~~~~~~~~~~~~~~~~~~
        vi conf.php
~~~~~~~~~~~~~~~~~~~~~~~~

{% include note.html content="Modify every entry under "Things in this section must be modified". Descriptions and pointers for each value are included within conf.php." %}

    **Modify every entry under "Things in this section must be modified". Descriptions and pointers for each value are included within conf.php.**

    * COOKIEDOMAIN - set this to the domain name your web server is using or leave it blank if you are only accessing the web server by its IP address

8. Set the owner of the **.ht-inc/maintenance** directory to the web server user (normally 'apache'):

        chown apache maintenance

9. Open the testsetup.php page in a web browser:

    * If you set up your site to be "https://my.server.org/vcl/" open https://my.server.org/vcl/testsetup.php
    * Debug any issues reported by testsetup.php

### 3. Log In to the VCL Website

1. Open the index.php page in your browser (https://my.server.org/vcl/index.php)
    * Select Local Account
    * Username: admin
    * Password: adminVc1passw0rd
2. Set the admin user password (optional):
    1. Click User Preferences
    2. Enter the current password: adminVc1passw0rd
    3. Enter a new password
    4. Click Submit Changes

### 4. Add a Management Node to the Database

1. Click the Management Nodes link
    1. Click Add
    2. Fill in these required fields:
        * **Hostname**: The name of the management node server. This value doesn't necessarily need to be a name registered in DNS nor does it need to be the value displayed by the Linux hostname command. For example, if you are installing all of the VCL components on the same machine you can set this value to localhost.

            **Take note of the value you enter for Hostname. In a later step performed during the management node installation, the value enter for Hostname must match the value you enter for FQDN in the /etc/vcl/vcld.conf file on the management node.**

        * **IP address**: the public IP address of the management node
        * **SysAdmin Email Address**: error emails will be sent to this address
        * **Install Path**: this is parent directory under which image files will be stored - only required if doing bare metal installs or using VMWare with local disks
        * **End Node SSH Identity Key Files**: enter /etc/vcl/vcl.key unless you know you are using a different SSH identity key file
    3. Optionally, fill in these fields:
        * **Address for Shadow Emails**: End users are sent various emails about the status of their reservations. If this field is configured, copies of all of those emails will be sent to this address.
        * **Public NIC configuration method**: this defaults to Dynamic DHCP - if DHCP is not available for the public interface of your nodes, you can set this to Static. Then, the IP configuration on the nodes will be manually set using Public Netmask, Public Gateway, Public DNS Server, and the IP address set for the computer under Manage Computers

2. Click Confirm Management Node

3. Click Submit

4. Click the Management Nodes link
    1. Select Edit Management Node Grouping
    2. Click Submit
    3. Select the checkbox for your management node
    4. Click Submit Changes

5. Install & Configure phpMyAdmin (Optional):

    [phpMyAdmin](https://www.phpmyadmin.net/) is a free and optional tool which allows [MySQL](https://www.mysql.com/) to be administered using a web browser. It makes administering the VCL database easier. This tool can be installed on the VCL web server. To install phpMyAdmin, follow the instructions on: [VCL 2.3 phpMyAdmin Installation & Configuration](VCL-2.3-phpMyAdmin-Installation-and-Configuration.html)



[Further steps if using only VMWare](VCL-2.3-Further-Steps-if-Using-VMware.html)

[Further steps if using xCAT](VCL-2.3-Further-Steps-if-Using-xCAT.html)

-------------------------------------------------------------------------------------

Previous Step: [VCL 2.3 Database Installation](#install-and-configure-the-database)

Next Step: [VCL 2.3 Management Node Installation](#install-and-configure-the-management-node-components)

--------------------------------------------------------------------------------------


## Install & Configure the Management Node Components
