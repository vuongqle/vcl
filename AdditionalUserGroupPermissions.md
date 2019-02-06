---
title: Additional User Group Permissions
last_updated: Jan 31, 2018
sidebar: mydoc_sidebar
permalink: additional-user-group-permissions.html
---

In addition to the primary set of [User Permissions](resources-groups-privileges.html#user-permissions), there are several additional permissions that can be granted to user groups. These are things that don't make sense to have tied to a particular privilege node because they aren't directly used in conjunction with resources. They are accessed under Privileges -> Additional User Group Permissions.


* **Manage Additional User Group Permissions** - grants users access to see this portion of the site (should only be granted to high level admins)
* **Manage Block Allocations (affiliation only)** - grants access to manage Block Allocations requested by users of the same affiliation as the logged in user
* **Manage Block Allocations (global)** - grants access to manage Block Allocations requested by users of any affiliation
* **Manage Federated User Groups (affiliation only)** - grants access to manage federated user groups having the same affiliation as the logged in user
* **Manage Federated User Groups (global)** - grants access to manage federated user groups having any affiliation
* **Manage VM Profiles** - grants access to manage VM Host Profiles under Virtual Hosts -> VM Host Profiles
* **Schedule Site Maintenance** - grants access to manage Site Maintenance
* **Set Overlapping Reservation Count** - grants access to set how many overlapping reservations users in a user group may have
* **Site Configuration (affiliation only)** - grants access to manage things under the Site Configuration part of the site for the affiliation matching the logged in user (does not include access to Global only settings)
* **Site Configuration (global)** - grants access to manage things under the Site Configuration part of the site for all affiliations and for Global settings
* **User Lookup (affiliation only)** - grants access to look up information about users having the same affiliation as the logged in user
* **User Lookup (global)** - grants access to look up information about users of any affiliation
* **View Dashboard (affiliation only)** - grants access to view information on the Dashboard related to users matching the affiliation of the logged in user
* **View Dashboard (global)** - grants access to view information on the Dashboard related to any user
* **View Debug Information** - grants access to view various debug related information throughout the site (things like the request.id of a reservation being displayed on the Reservations page)
* **View Statistics by Affiliation** - grants access to select the affiliation (or compilation of all affiliations) for which to view statistics
