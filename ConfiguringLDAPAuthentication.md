---
title: Configuring LDAP Authentication
last_updated: Feb 12, 2019
permalink: Configuring-LDAP-Authentication.html
---

## Benefits of LDAP Authentication

Configuring the VCL website to authenticate users via LDAP is highly recommended if your organization has an existing LDAP directory infrastructure.

### Account Synchronization and Security
Using LDAP for VCL website user authentications relieves the VCL system administrators from having to create and manage user accounts, as well as synchronize user passwords.  The passwords of the users who authenticate via LDAP are never stored within any part of VCL.

### Group Synchronization
VCL allows user groups and user group membership from the LDAP directory to be synchronized to the VCL system.

## LDAP Requirements

### Server Requirements
Any variety of LDAP server can be used.  LDAP is one of the core technologies used by Microsoft Active Directory (AD) so a very common configuration is to use an AD domain controller to authenticate users to the VCL website.  You could also use OpenLDAP or some other LDAP provider.

VCL does not require any particular LDAP schema, directory structure, or directory organization.  The VCL website LDAP configuration can be configured to handle any structure or custom user object attributes.

### SSL/LDAPS

Secure LDAP (ldaps://) and the corresponding SSL certificate must be properly configured and working on the LDAP server.

### Firewall

The VCL web server must be able to connect to the LDAP server via the secure LDAP port, usually TCP 636.

### Privileged LDAP Account

*The privileged LDAP account requirement is not necessary if your LDAP directory server permits anonymous binds.*

In order to utilize VCL LDAP group synchronization feature, a privileged user account must be configured in the LDAP directory.  The user account must have permissions to read the following attributes of all users who will log in to the VCL website:

* First name
* Last name
* User login
* Email address (optional)

### VCL Web Server Requirements

#### php-ldap

The VCL web code requires the php-ldap package.  Execute the following command if the package is not already installed:

    sudo yum install -y php-ldap

#### Trusted LDAP Server Certificate

The VCL web server must trust the LDAP server's SSL certificate.  If the LDAP server's SSL certificate was issued by a public, trusted certificate authority (CA) then no additional steps are usually required.

If the LDAP server's SSL certificate was self-signed, the certificate of the root CA used to sign the LDAP server's certificate must be installed on the VCL web server.  If the VCL web server is running a Red Hat-based Linux distribution, this normally means that the root CA certificate needs to be added to the following root CA certificate bundle file:

    /etc/pki/tls/certs/ca-bundle.crt

View the certificate authorities included in the ca-bundle.crt file by executing the following command:

    openssl crl2pkcs7 -nocrl -certfile /etc/pki/tls/certs/ca-bundle.crt | openssl pkcs7 -print_certs -noout

To add a root CA certificate to ca-bundle.crt, download the root CA certificate to the following directory on the VCL web server:

    /etc/pki/ca-trust/source/anchors/

Then execute the following command:

    update-ca-trust extract

The certificate authority should now appear in the ca-bundle.crt file:

    openssl crl2pkcs7 -nocrl -certfile /etc/pki/tls/certs/ca-bundle.crt | openssl pkcs7 -print_certs -noout
    subject=/DC=org/DC=example/DC=ad/DC=vcl/CN=vcl-DC01-CA-1
    issuer=/DC=org/DC=example/DC=ad/DC=vcl/CN=vcl-DC01-CA-1

After adding the certificate, restart httpd:

    service httpd restart

You can verify that the certificate is properly installed using this command:

    openssl s_client -showcerts -CAfile /etc/pki/tls/certs/ca-bundle.crt -connect your.ldap.server.here:636

If you see "Verify return code: 0 (ok)" at the end of the output then it is installed correctly. If you see a different return code, then you'll need to troubleshoot the problem.

You may need to add a line to /etc/openldap/ldap.conf to point to the ca-bundle.crt file. If so, add the following:

    TLS_CACERT /etc/pki/tls/certs/ca-bundle.crt


You already have this if you have an Active Directory system set up.

Next, you (probably) need to add an affiliation to VCL so that users logging in via the new LDAP connection will all be associated together.

Finally, you need to modify the web code conf.php file to have information about how to connect to the LDAP server.

You will also need to make sure your web server can trust the SSL certificate and access it through any firewalls.

## Add LDAP Authentication Parameters to conf.php
The conf.php file on the VCL web server contains an authentication configuration parameter section for each affiliation:

    / var/www/html/vcl-trunk/.ht-inc/conf.php

Within ***conf.php*** is a variable named **$authMechs**.  This variable is a [PHP associative array](https://www.w3schools.com/php/php_arrays.asp) containing authentication parameters for each authentication method shown in the drop-down box on the VCL website's login page.


## VCL Login Page Drop-Down Box Entry Names
You will need to add an entry to the **$authMechs** array with information specific to your LDAP directory.  The keys of the ***$authMechs associative array*** variable determine the the names and order of the authentication method drop-down box on the VCL website login page.  For example, if the variable contains the following:

    $authMechs = array(
      "VCL University" => array(),
      "Local Account" => array(),
      "Chicago Cubs" => array(),
    );

The following entries would be shown:

<img src="images/image2017-2-22 16_44_49.png" width="400" border="1">

## conf.php LDAP Authentication Parameters

Each **$authMechs** array entry must contain several parameter-value pairs.  The parameters related to LDAP authentication are:


/////////////////////////////////////////INSERT TABLE HERE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

## Add the LDAP Configuration Parameters to conf.php

The code in ***conf.php*** where **$authMechs** is defined contains a commented-out "***EXAMPLE1 LDAP***" section:

    /*"EXAMPLE1 LDAP" => array("type" => "ldap",
                               "server" => "ldap.example.com",   # hostname of the ldap server
                               "binddn" => "dc=example,dc=com",  # base dn for ldap server
                               "userid" => "%s@example.com",     # this is what we add to the actual login id to authenticate a user via ldap
                                                                 #    use a '%s' where the actual login id will go
                                                                 #    for example1: 'uid=%s,ou=accounts,dc=example,dc=com'
                                                                 #        example2: '%s@example.com'
                                                                 #        example3: '%s@ad.example.com'
                               "unityid" => "samAccountName",    # ldap field that contains the user's login id
                               "firstname" => "givenname",       # ldap field that contains the user's first name
                               "lastname" => "sn",               # ldap field that contains the user's last name
                               "email" => "mail",                # ldap field that contains the user's email address
                               "defaultemail" => "@example.com", # if for some reason an email address may not be returned for a user, this is what
                                                                 #    can be added to the user's login id to send mail
                               "masterlogin" => "vcluser",       # privileged login id for ldap server
                               "masterpwd" => "*********",       # privileged login password for ldap server
                               "affiliationid" => 3,             # id from affiliation id this login method is associated with
                               "lookupuserbeforeauth" => 0,      # set this to 1 to have VCL use masterlogin to lookup the full DN of the user
                                                                 #   and use that for the ldap bind to auth the user instead of just using the userid
                                                                 #   field from above
                               "lookupuserfield" => '',          # if lookupuserbeforeauth is set to 1, this is the attribute to use to search in ldap
                                                                 #   for the user.  Typically either 'cn', 'uid', or 'samaccountname'
                               "help" => "Use EXAMPLE1 LDAP if you are using an EXAMPLE1 account"), # message to be displayed on login page about when
                                                                                                    #   to use this login mechanism*/


You can either uncomment the "***EXAMPLE1 LDAP***" section and modify it or copy and paste the following into the file:

    "My LDAP" => array(
        "type" => "ldap",
        "server" => "",
        "binddn" => "",
        "userid" => "",
        "unityid" => "",
        "firstname" => "",
        "lastname" => "",
        "email" => "",
        "defaultemail" => "",
        "masterlogin" => "",
        "masterpwd" => "",
        "affiliationid" => 0,
        "lookupuserbeforeauth" => 1,
        "lookupuserfield" => '',
        "help" => ""
    ),

A completed entry inserted at the beginning of the **$authMechs** variable would look like this:

    $authMechs = array(
        "VCL University" => array(
                "type" => "ldap",
                "server" => "dc01.vcl.ad.example.org",
                "binddn" => "OU=VCL Users,DC=vcl,DC=ad,DC=example,DC=org",
                "userid" => "%s@vcl.ad.example.org",
                "unityid" => "samAccountName",
                "firstname" => "givenName",
                "lastname" => "sn",
                "email" => "mail",
                "defaultemail" => "@vcl.ad.example.org",
                "masterlogin" => "vcl-reader",
                "masterpwd" => "**********",
                "affiliationid" => 3,
                "lookupuserbeforeauth" => 1,
                "lookupuserfield" => 'samAccountName',
                "help" => "Select <b><u>VCL University</u></b> if you are a student of VCL University."
        ),
        /"Shibboleth (UNC Federation)" => array("type" => "redirect",
        ...

## Test the LDAP Connection

It's helpful to test the LDAP configuration parameters you enter into ***conf.php*** using a utility such as  ldapsearch before attempting to log into the VCL website as an LDAP user.  The **ldapsearch** utility is part of the openldap-clients package.  Install openldap-clients by executing the following command on the VCL web server:

    sudo yum install -y openldap-clients

If the Active Directory domain controller is not properly registered in DNS, add it to the /etc/hosts file on the VCL web server

    ldapsearch -h dc01.vcl.ad.example.org -x -b 'OU=VCL Users,DC=vcl,DC=ad,DC=example,DC=org' -D 'vcl-reader@vcl.ad.example.org' -W cn=*
