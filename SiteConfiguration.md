---
title: Site Configuration
tags: [site, configuration]
keywords:
last_updated: Oct 31, 2018
summary:
sidebar: mydoc_sidebar
permalink: site-configuration.html
---
The site configuration settings provide a method for VCL administrators to change certain settings related to the VCL system and how it processes reservations. This is a new feature added in Apache VCL 2.4 and is located on the left navigation menu in the VCL web interface. The Additional User Permission (Privileges->Additional User Permissions) Site Configuration grants access to this portion of the site.

|**Time Source**|This is the default list of time servers to be used on installed nodes. These can be overridden for each management node under the settings for a given management node. Separate hostnames using a comma (,).|
|**Connected User Check Threshold**| Do not perform user-logged-in time out checks if reservation duration is greater than the specified value (in hours). The default value is 24 hours. As examples, if set to 24, a reservation for 23 hours would be timed out if a user remains disconnected for too long, and a reservation for 25 hours would not be timed out if a user remains disconnected for too long. <br/>The duration after which a disconnected user is timed out is set by Re-connect To Reservation Timeout.<br>|
|**Acknowledge Reservation Timeout**|Once a reservation is ready, users have this long to click the Connect button before the reservation is timed out (in minutes, does not apply to server reservations). The default value is 15 minutes.|
|**Connect to Reservation Timeout**|After clicking the Connect button for a reservation, users have this long to connect to a reserved node before the reservation is timed out (in minutes, does not apply to server reservations). The default value is 15 minutes.|
|**Re-connect To Reservation Timeout**|After disconnecting from a reservation, users have this long to reconnect to a reserved node before the reservation is timed out (in minutes, does not apply to imaging or server reservations). The default value is 15 minutes.|
|**User Reservation Password Length**|For reservations not using federated authentication, VCL generates random user passwords. This specifies how many characters should be in the password. The default value is 6 characters.|
|**User Reservation Password Special Characters**|For reservations not using federated authentication, VCL generates random user passwords. This specifies if characters other than letters and numbers should be included in the passwords. The default is not to include special characters.|
|**In-Use Reservation Check**|Frequency at which a general check of each reservation is done (in minutes). The default value is 5 minutes.|
|**In-Use Reservation Check (servers)**|Frequency at which a general check of each server reservation is done (in minutes). The default value is 15 minutes.|
|**In-Use Reservation Check (clusters)**|Frequency at which a general check of each cluster reservation is done (in minutes). The default value is 15 minutes.|
|**First Notice For Reservation Ending**|When getting close to the end time of a reservation, users are notified two times that the reservation is about to end. This is the time before the end of the reservation that the first of those notices should be sent. The default value is 10 minutes.|
|**Second Notice For Reservation Ending**|When getting close to the end time of a reservation, users are notified two times that the reservation is about to end. This is the time before the end of the reservation that the second of those notices should be sent. The default value is 5 minutes.|
|**NAT Port Ranges**|This is the list of port ranges available for use on NAT servers to be used as ports that are forwarded to reserved nodes. Type of port (TCP/UDP) is not specified. List ranges one per line (ex: 10000-20000). The default range is 10000-60000. See the NAT documentation for further information. |
