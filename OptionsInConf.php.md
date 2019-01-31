---
title: Options in conf.php
last_updated: Jan 31, 2018
sidebar: mydoc_sidebar
permalink: options-in-conf.php.html
---

Many parts of the VCL web frontend are configured in the web/.ht-inc/conf.php file. conf.php is created as part of installation by making a copy of conf-default.php. In addition to this page, most of the options are documented within the file.

#### Options All Sites Should Modify/Review

**HELPURL** -There is a Help link in the Navigation menu. This is the URL to which that item links.

**HELPEMAIL** - If an unexpected error occurs, users will be prompted that they can email this address for further assistance.

**ERROREMAIL** - If an unexpected error occurs, the code will send an email about it to to this address with some detailed information about the error. The error will also be logged wherever php errors are logged on the web server if that is enabled in php.ini.

**ENVELOPESENDER** - An email address for envelope sender of mail messages. If a message gets bounced, it goes to this address.

**date_default_timezone_set** - This sets the timezone within php. Normally, this should be set to your timezone. A list of available values can be found at [http://php.net/manual/en/timezones.php](http://php.net/manual/en/timezones.php)

**DEFAULTLOCALE** - This sets the default locale for the web site. Available [locales](https://vcl.apache.org/docs/multilingualization.html) can be found in web/locale. Additional locales can also be added.

**$clickThroughText** - When creating images, the user creating the image must agree to a click through license agreement. The idea here is that, since many people may be granted access to create images, those people need to know they are responsible for ensuring that any software installed within the image is appropriately licensed.


#### Additional Options

**ONLINEDEBUG** - This controls displaying more detailed error information; 1 enables the errors to be displayed, 0 disables them

**BASEURL** - This and SCRIPT make up the URL of the site. This part includes everything from the https:// part through the directory containing the php script, but does not include a trailing slash.

**SCRIPT** - This and BASEURL make up the URL of the site. This part starts with a slash (/) and is the name of the primary php script of VCL. Typically, it is left as /index.php.

**HOMEURL** - URL users are directed to when clicking the HOME link in the navigation area or after being logged out when clicking the Logout link in the navigation area.

**COOKIEDOMAIN**- Domain used for setting browser cookies. If left empty, the full hostname or IP address being used to access the site is used.

**DEFAULTGROUP** - For any users that are not members of any user groups, reservation duration lengths from this group are used.

**DEFAULT_AFFILID** - When entering users on various parts of the site, a username followed by @ and an affiliation are used to specify the user. If no affiliation is specified, the affiliation with DEFAULT_AFFILID is used.

**DAYSAHEAD** - Number of days in advance that start times of normal reservations can be made. For example, if this is set to 4, and today is Monday, users can make normal reservations with a start time as far away as Thursday. Note that only the name of the day is listed when selecting a start day for the reservation. So, if a value greater than 7 is used, it could be a little confusing for the user to see multiples of the same day listed in the drop down box.

**DEFAULT_PRIVNODE** - This is unlikely to ever need changing. It is the ID of the toplevel node in the privilege tree.

**SCHEDULER_ALLOCATE_RANDOM_COMPUTER** - Set this to 1 to have the scheduler assign a randomly allocated computer of those available; set it to 0 to assign the computer with the lowest combination of specs.

**PRIV_CACHE_TIMEOUT** - Time (in minutes) that a user's privileges are cached in a session before reloading them.

**MIN_BLOCK_MACHINES** - This defines the minimum number of block allocation machines that can be requested.

**MAX_BLOCK_MACHINES** - This defines the maximum number of block allocation machines that can be requested.

**DOCUMENTATIONURL** - The URL used for the Documentation link in the navigation list.

**USEFILTERINGSELECT** - Set this to 1 to use a Dojo filteringselects for some of the select boxes. The filteringselect can be a little slow for a large number of items.

**FILTERINGSELECTTHRESHOLD** - If USEFILTERINGSELECT = 1, only use them for selects up to this size. Use regular selects when the number of items exceeds this value.

**SEMTIMEOUT** - When scheduling resources for a reservation, the scheduler acquires a semaphore on the resources before actually scheduling them. This is the timeout value for that semaphore.

**DEFAULTTHEME** - This is the theme that will be used for the login screen and when the site is placed in maintenance if $_ COOKIE['VCLSKIN'] is not already set by a previous visit to the site.

**HELPFAQURL** - (deprecated) This sets the URL for a link displayed as part of a help request form that is no longer enabled.

**ALLOWADDSHIBUSERS** - This is only related to using Shibboleth authentication for an affiliation that does not also have LDAP set up (i.e. affiliation.shibonly = 1). Set this to 1 to allow users be manually added to VCL before they have ever logged in through things such as adding a user to a user group or directly granting a user a privilege somewhere in the privilege tree. Note that if you enable this and typo a userid, there is no way to verify that it was entered incorrectly so the user will be added to the database with the typoed userid.

**MAXINITIALIMAGINGTIME** - For imaging reservations, users will have at least this long as the max selectable duration.

**MAXSUBIMAGES** - (future use) Maximum allowed number for subimages in a config.

**$ENABLE_ITECSAUTH** - Enable use of ITECS accounts (also called "Non-NCSU" accounts). Some dependencies for this to work have never been contributed to ASF.

**$xmlrpcBlockAPIUsers** - $xmlrpcBlockAPIUsers is an array of ids from the user table for users that are allowed to call XMLRPC functions designed specifically to be called by vcld.

**NOAUTH_HOMENAV** - Boolean value of 0 or 1 to enable documentation links on login page and page where authentication method is selected. 0 = disabled; 1 = enabled. [See additional documentation.](Adding-Navigation-Links-To-The-Login-Pages.html)

**$NOAUTH_HOMENAV** - Array of documentation links to display on login page and page where authentication method is selected when NOAUTH_HOMENAV is set to 1. [See additional documentation.](Adding-Navigation-Links-To-The-Login-Pages.html)

**QUERYLOGGING** - Boolean value of 0 or 1 to control logging of non SELECT database queries for auditing or debugging purposes; queries are logged to the querylog table. This table can get quite large and should be periodically rotated or purged of older entries. It is very useful to have for audit purposes.

**XMLRPCLOGGING** - Boolean value of 0 or 1 to control logging of XMLRPC calls for auditing or debugging purposes; queries are logged to the xmlrpcLog table. This table typically does not grow very fast unless your site is making heavy use of the XMLRPC API.

**$authMechs** - This array contains information on how to authenticate users when they log in as well as connection information for looking up information about users in LDAP. Please see additional information on configuring [LDAP authentication](http://vcl.apache.org/docs/ldapauth.html) and [Shibboleth authentication](https://vcl.apache.org/docs/shibauth.html).

### User Handling Functions

The following are arrays of functions/arguments that are used for validating, adding, and updating users. Generally, they do not need to be modified, but allow for doing some customizations on how to handle users.

**$affilValFunc** - Array of functions that are used to validate users. Each key is an affiliation id, and each value is a function name.

**$affilValFuncArgs** - Array of values that are used as arguments to the corresponding entry in $affilValFunc. Each key is an affiliation id, and each value is the argument. Only a single argument is allowed for each entry.

**$addUserFunc** - Array of functions that are used to add users. Each key is an affiliation id, and each value is a function name.

**$addUserFuncArgs** - Array of values that are used as arguments to the corresponding entry in $addUserFunc. Each key is an affiliation id, and each value is the argument. Only a single argument is allowed for each entry.

**$updateUserFunc** - Array of functions that are used to update users. Each key is an affiliation id, and each value is a function name.

**$updateUserFuncArgs** - Array of values that are used as arguments to the corresponding entry in $updateUserFunc. Each key is an affiliation id, and each value is the argument. Only a single argument is allowed for each entry.

**$findAffilFuncs** - Array of functions used to separate username@affiliation into username and affiliation.

#### Supporting Files

**require_once(".ht-inc/authmethods/itecsauth.php");** - Uncomment this line if ITECS authentication is enabled. $ENABLE_ITECSAUTH must also be set to 1. Some dependencies for this to work have never been contributed to ASF.

**require_once(".ht-inc/authmethods/ldapauth.php");** - Uncomment this line if LDAP authentication is enabled.

**require_once(".ht-inc/authmethods/shibauth.php");** - Uncomment this line if Shibboleth authentication is enabled.
