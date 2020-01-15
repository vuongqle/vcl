---
title: User Groups
last_updated: March 19, 2019
permalink: User-Groups.html
---

Overview

Many tasks in VCL that relate to users are done with User Groups rather than directly with users. This simplifies management of users as actions can be done to large groups at once. User groups are added, edited, and deleted under Manage Groups. User groups cannot be deleted when they are in use in some part of VCL. Attempting to delete a group that is in use will display a message stating where the group is still being used. In order to see this portion of the web site, a user needs to have the **groupAdmin** user permission somewhere in the Privilege tree.

<img src="images/image2017-3-13 13_50_43.png" width="500" border="1">

Each user group has an affiliation associated with it. Groups from other affiliations along with displaying the affiliation part of user groups can be hidden under User Preferences->General Preferences.

## Types

There are two types of user groups in VCL: **normal** groups whose membership is manually managed through the web frontend or XML RPC API, and **federated** groups whose membership is automatically managed by mirroring user groups in an LDAP system. Each user group has certain attributes associated with it. There are various places within VCL that user groups can be used, with the primary place being granting access to resources in the privilege tree.

## Membership

Membership of normal user groups can be edited by the owner of a group as well as members of the "Editable by" group. Membership of federated groups can be viewed but cannot be modified via the web frontend or via the XML RPC API because the membership is automatically managed by mirroring groups in LDAP. The membership of the federated groups is only updated when users log in and only that user's membership in groups is modified. As such, viewing the membership of a federated group may show old users. However, those users will be removed as each of them log in to VCL again.

## Managing Attributes

Only the owner of a normal group can modify the name, owner, editable by, time restrictions, and max overlapping reservations for a group.

Any user that is a member of a user group that has the additional user group permission **Manage Federated** User Groups can modify the time restrictions and max overlapping reservations for federated user groups.
