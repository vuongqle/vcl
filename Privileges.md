---
title: Privileges
last_updated: Feb 06, 2018
sidebar: mydoc_sidebar
permalink: privileges.html
---

Users are granted access to parts of the VCL web site and to resources through the Privilege tree. User permissions and resource attributes can both be cascaded down from one node to all of its children. Additionally, cascaded user permissions and resource attributes can be blocked at a node so that they do not cascade down to that node or any of its children.

## User Permissions

There are ten user permissions that can be granted to users. They can be granted to users directly or to user groups.

* **addomainAdmin** - allows users to do administrative tasks with AD Domains in AD Domain groups with administer or manageGroup granted at the same node
* **computerAdmin** - allows users to do administrative tasks with computers in computer groups with administer or manageGroup granted at the same node
* **groupAdmin** - grants users access to the Manage Groups portion of the site
* **imageAdmin** - allows users to do administrative tasks with images in image groups with administer or manageGroup granted at the same node
* **imageCheckOut** - allows users to make reservations for images in image groups with available granted at the same node
* **mgmtNodeAdmin** - allows users to do administrative tasks with management nodes in management node groups with administer or manageGroup granted at the same node
* **nodeAdmin** - allows users to add and delete child nodes at the specified node
* **resourceGrant** - grants users access to control what resource attributes are assigned at the same node
* **scheduleAdmin** - allows users to do administrative tasks with schedules in schedule groups with administer or manageGroup granted at the same node
* **userGrant** - grants users access to control what user permissions are assigned at the same node

## Resource Attributes

There are three resource attributes that can be assigned to a resource group at any node in the privilege tree.

* **available** - makes resources in the group available at the node - this is only has meaning for image groups and computer groups and relates to the imageCheckOut and imageAdmin user permissions
* **administer** - makes the resources in the group available to be administered by users with the appropriate user permissions at the same node (i.e. imageAdmin for image groups, computerAdmin for computer groups, etc)
* **manageGroup** - makes the resources in the group available to have their grouping controlled by users with the appropriate user permissions at the same node
* **manageMapping** - makes the resources in the group available to have their mapping controlled by users with the appropriate user permissions at the same node
