---
title: Test
last_updated: Nov 18, 2019
sidebar: mydoc_sidebar
permalink: test.html
---
THIS IS A TEST

##TEST


## Overview

VCL provides a way for scheduling a maintenance window during which the site sill be down for maintenance. This is done through the Site Maintenance section of the site. Users will be alerted for a specified amount of time leading up to the maintenance window that it is coming. A file is created for each scheduled maintenance window on the web server itself. Then, during the maintenance window, the web server can determine that it is within a maintenance window without needing to connect to the database server, allowing maintenance to be done on the database with the web server still showing the message to users. Obviously, if the web server will be taken down for maintenance, the users cannot be shown a maintenance notice during the time the web server is down.