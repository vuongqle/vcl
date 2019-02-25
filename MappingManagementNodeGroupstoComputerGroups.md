---
title: Mapping Management Node Groups to Computer Groups
last_updated: Feb 14, 2019
permalink: Mapping-Management-Node-Groups-to-Computer-Groups.html
---

VCL must know which management nodes can control which computers. This is referred to as mapping, which is VCL's method of relating resource types to one another. Rather than directly relating management nodes to computers, management node groups are mapped to computer groups. This is done under Manage->Management Nodes->Edit Grouping & Mapping. Access to this part of the site is granted by a user having mgmtNodeAdmin and computerAdmin at the same node where management node and computer groups both have the manageMapping attribute set.

<img src="images/image2017-2-22 15_31_43.png" width="500" border="1">

Once on the Grouping & Mapping page, users with access will see tabs titled **Map By Management Node Group** and **Map By Computer Group.**

<img src="images/image2017-2-22 15_35_4.png" width="500" border="1">

Mapping can be done by first selecting a management node group, then assigning which computer groups are mapped to it. This is done under the **Map By Management Node Group** tab. Multiple computer groups can be selected from the left or right boxes by holding down Ctrl while clicking on a computer group. Use the Add and Remove buttons to move computer groups between the boxes.

<img src="images/image2017-2-22 15_44_15.png" width="500" border="1">

Mapping can also be done by first selecting a computer group, then assigning which management node groups are mapped to it. This is done under the **Map By Computer Group** tab. Multiple management node groups can be selected from the left or right boxes by holding down Ctrl while clicking on a management node group. Use the Add and Remove buttons to move management node groups between the boxes.

<img src="images/image2017-2-22 15_46_44.png" width="500" border="1">
