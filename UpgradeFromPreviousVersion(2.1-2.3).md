---
title: Upgrade From Previous Version (2.1 to 2.3)
last_updated: Nov 9, 2018
sidebar: mydoc_sidebar
permalink: upgrade-from-previous-version-2.1-2.3.html
---

This page provides information on how to upgrade from VCL 2.1 to VCL 2.3. Please note it only applies for the upgrade from 2.1 to 2.3, this may or may not work for other versions.

### Basic Steps

* [Download and Extract 2.3 code](#download-and-extract-23-code)
* [Shutdown httpd and VCLD services](#shut-down-services)
* [Create backup of VCL Database](#create-a-backup-of-vcl-database)
* [Update mysql schema](#update-mysql-schema)
* [Update Web code, create a backup, copy in new, make changes](#update-web-code)
* [Restart httpd service](#restart-httpd-service)
* [Update Management node vcl code, create a backup, copy in new, make changes](#update-management-node-code)
* [Restart vcld service](#restart-vcld-service)

### Download and Extract 2.3 code


1. Follow instructions on [VCL 2.3](vcl-2.3.html) Release page to download and verify apache-VCL-2.3.tar.bz2 and put in in /root
2. extract VCL 2.3 code

        tar xjf apache-VCL-2.3.tar.bz2

### Shut Down Services

Shutdown the httpd and vcld services

        service httpd stop or /etc/init.d/httpd stop

        service vcld stop or /etc/init.d/vcld stop

### Create a Backup of VCL Database

We will create a backup of the vcl database. This will provide a restore point if necessary.

        mysqldump vcl > ~/vcl-pre2.3-upgrade.sql

### Update MySQL Schema

1. This step updates the mysql schema.

        cd /root/apache-VCL-2.3
        mysql vcl < mysql/update-vcl.sql

    *One item of note:* A new resource group is added in update-vcl.sql - **all profiles.** Access to manage the group is added to the **VCL->admin** node in the privilege tree if that node exists. If not, you will need to add it manually after starting httpd again. To add it manually, pick a node in the privilege tree, scroll to **Resources**, click **Add Resource Group**, select **serverprofile/all profiles** from the drop-down box, check **available**, **administer**, **manageGroup**, and **manageMapping**, and click **Submit New Resource Group**.

2. Grant CREATE TEMPORARY TABLES to mysql user

    The web code now requires access to create temporary tables in mysql. You need to grant the user your web code uses to access mysql the "CREATE TEMPORARY TABLES" permission. Look at the secrets.php file in your web code for the user and hostname. For example, if your web code is installed at /var/www/html/vcl, your secrets.php file would be /var/www/html/vcl/.ht-inc/secrets.php. Look for $vclhost and $vclusername. The secrets.php file might have something like:

        $vclhost = 'localhost';

        $vcluser = 'vcluser';

    Then, you need to issue the grant command to mysql. Using the values from above as examples, connect to mysql and then issue the grant command:

        mysql

        GRANT CREATE TEMPORARY TABLES ON `vcl`.* TO 'vcluser'@'localhost';

        exit

### Update Web Code

This step we will move the 2.1 web directory out of the way, so we can copy in the new web code base. After copying in the new code, we will migrate your configuration changes. These instructions assume that you installed the vcl web code at /var/www/html/vcl. If you installed it elsewhere, replace /var/www/html/vcl with your vcl web root.

1. Move your old code out of the way

        cd /var/www/html

        mv vcl ~/vcl_2.1_web

2. Copy the new code in place

        cd /root/apache-VCL-2.3

        cp -r web /var/www/html/vcl

3. Copy your 2.1 config files

        cd ~/vcl_2.1_web/.ht-inc

        cp secrets.php pubkey.pem keys.pem /var/www/html/vcl/.ht-inc

4. Make the maintenance directory writable by the web server user. You will need to know what user httpd runs as on your server. This can be found with

        ps aux | grep httpd

    Look at the first column. One process will be owned by root. The remaining processes will be owned by the web server user. Now, own /var/www/html/vcl/.ht-inc/maintenance to that user (replacing 'apache' with your web server user if different):

        chown apache /var/www/html/vcl/.ht-inc/maintenance

5. Update conf.php. When upgrading from 2.1, it is recommended to start with a fresh copy of conf-default.php from 2.3 and then apply your changes to it again.

        cd /var/www/html/vcl/.ht-inc

        cp conf-default.php conf.php

    Look at each value in the top section labeled **Things in this section** must be modified and set the value to what you had in your old conf.php file. If you are using LDAP authentication, you can copy all entries from $authMech out of your 2.1 conf.php file into your 2.3 conf.php file. However, note that you will need to add the following two additional keys to each entry. A description of these keys can be found in the 2.3 conf-default.php file.

        "lookupuserbeforeauth" => 0,

        "lookupuserfield" => '',

### Restart httpd service

        service httpd start or /etc/init.d/httpd start

### Update management node code

This step will make a backup copy of the 2.1 vcl code base and then copy the new code over the existing code to preserve any drivers or other files you've added.

1. Copy 2.1 code base to a backup location

        cd <your vcl MN code root path>

        ie. cd /usr/local/

        cp -r vcl ~/vcl_2.1_managementnode

2. Copy in the 2.3 code base to /usr/local, copying in should preserve any drivers or other files you've added.

        /bin/cp -r /root/apache-VCL-2.3/managementnode/* /usr/local/vcl

3. Run install_perl_libs.pl to add any new perl library requirements:

        /usr/local/vcl/bin/install_perl_libs.pl

### Restart vcld service

        service vcld start or /etc/init.d/vcld start
