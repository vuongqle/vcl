---
title: Upgrade From Previous Version (2.2.1 to 2.3)
last_updated: Nov 15, 2018
sidebar: mydoc_sidebar
permalink: upgrade-from-previous-version-2.2.1-2.3.html
---


This page provides information on how to upgrade from VCL 2.2.1 to VCL 2.3. Please note it only applies for the upgrade from 2.2.1 to 2.3, this may or may not work for other versions.


### Basic Steps


* [Download and Extract 2.3 code](#download-and-extract-23-code)
* [Shutdown httpd and VCLD services](#shut-down-services)
* [Create backup of VCL Database](#create-a-backup-of-vcl-database)
* [Update mysql schema](#update-mysql-schema)
* [Update Web code](#update-web-code)
* [Restart httpd service](#restart-httpd-service)
* [Update Management node code](#update-management-node-code)
* [Restart vcld service](#restart-vcld-service)

### Download and Extract 2.3 Code

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

This step updates the mysql schema.

            cd /root/apache-VCL-2.3
            mysql vcl < mysql/update-vcl.sql

        *One item of note:* A new resource group is added in update-vcl.sql - **all profiles.** Access to manage the group is added to the **VCL->admin** node in the privilege tree if that node exists. If not, you will need to add it manually after starting httpd again. To add it manually, pick a node in the privilege tree, scroll to **Resources**, click **Add Resource Group**, select **serverprofile/all profiles** from the drop-down box, check **available**, **administer**, **manageGroup**, and **manageMapping**, and click **Submit New Resource Group**.

### Update web code

This step we will move the 2.2.1 web directory out of the way, so we can copy in the new web code base. After copying in the new code, we will migrate your configuration changes. These instructions assume that you installed the vcl web code at /var/www/html/vcl. If you installed it elsewhere, replace /var/www/html/vcl with your vcl web root.


1. move your old code out of the way

        cd /var/www/html
        mv vcl ~/vcl_2.2.1_web

2. copy the new code in place

            cd /root/apache-VCL-2.3
            cp -r web /var/www/html/vcl

3. copy your 2.2.1 config files

        cd ~/vcl_2.2.1_web/.ht-inc
        cp conf.php secrets.php pubkey.pem keys.pem /var/www/html/vcl/.ht-inc

4. Make the maintenance directory writable by the web server user. You will need to know what user httpd runs as on your server. This can be found with:

        ps aux | grep httpd

    Look at the first column. One process will be owned by root. The remaining processes will be owned by the web server user. Now, own /var/www/html/vcl/.ht-inc/maintenance to that user (replacing 'apache' with your web server user if different):

        chown apache /var/www/html/vcl/.ht-inc/maintenance

5. Make some changes to conf.php:
    1. A new user group permission that controls who can manage block allocations globally or for a specific affiliation has been added. It can be granted to any user group under Privileges->Additional User Permissions->Manage Block Allocations. Users with this permission are notified of new block allocation requests. **Remove**

            $blockNotifyUsers

        from conf.php.
    2. A new user group permission that controls who can look up users globally or for a specific affiliation has been added. It can be granted to any user group under **Privileges->Additional User Permissions->User Lookup**. Users with this permission can look up information about other users. **Remove**

            $userlookupUsers

        from conf.php
    3. Multilingualization has been added VCL. So, DEFAULTLOCALE has been added to conf.php to set the default locale. **Add**

            define("DEFAULTLOCALE", "en_US");

        to conf.php, changing en_US if needed to match your locale. You can look in /var/www/html/vcl/locale to see which ones are available.

    4. Users authenticated using Shibboleth without also having an LDAP server can now be added before they log in. **Add**

            define("ALLOWADDSHIBUSERS", 0);

        to conf.php. If you are using Shibboleth and would like to be able to add users to groups before the user has ever logged in to VCL, you can set this to 1. However, please note that if you typo the userid, there is no way to verify it, and the user will be added with the typoed userid.

    5.  Some LDAP related items have been simplified in the code using some additional options in $authMechs. For any LDAP entries, you need to add two options. "lookupuserbeforeauth" is used if you need VCL to look up the full DN of a user and use that when doing the bind that authenticates the user (if you don't know what this means, leave it set to 0). If you need to set it to 1, then you will need to set "lookupuserfield" to what LDAP attribute to use when looking up the user's DN (typically either 'cn', 'uid', or 'samaccountname'). **Add**

            "lookupuserbeforeauth" => 0,
            "lookupuserfield" => '',

        to each LDAP array you have in the $authMech array in conf.php.

    6. If you are using any Local accounts for authentication, you need to modify the entries for $addUserFunc and $updateUserFunc. Change

            $addUserFunc[$item'affiliationid'] = create_function('', 'return 0;');
            $updateUserFunc[$item'affiliationid'] = create_function('', 'return 0;');

        to

            $addUserFunc[$item'affiliationid'] = create_function('', 'return NULL;');
            $updateUserFunc[$item'affiliationid'] = create_function('', 'return NULL;');

## Restart httpd service

        service httpd start or /etc/init.d/httpd start

### Update management node code

This step will make a backup copy of the 2.2.1 vcl code base and then copy the new code over the existing code to preserve any drivers or other files you've added.

1. Copy 2.2.1 code base to a backup location

        cd <your vcl MN code root path>

        ie. cd /usr/local/

        cp -r vcl ~/vcl_2.2.1_managementnode

2. Copy in the 2.3 code base to /usr/local, copying in should preserve any drivers or other files you've added.

        /bin/cp -r /root/apache-VCL-2.3/managementnode/* /usr/local/vcl

3. Run install_perl_libs.pl to add any new perl library requirements:

        /usr/local/vcl/bin/install_perl_libs.pl

### Restart vcld service

        service vcld start or /etc/init.d/vcld start
