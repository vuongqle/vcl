---
title: Special Built-in User Groups
last_updated: March 19, 2019
permalink: Special-Built-In-User-Groups.html
---

VCL has a few User Groups that have special meaning:

* adminUsers
* Allow No User Check
* Default for Editable by
* global - *not used by web frontend*
* manageNewImages
* Specify End Time

**adminUsers** - This is a user group that comes supplied with VCL at installation time and is intended to be used for site wide admin users. By default, it has been granted full access to all parts of VCL that exist after installation is complete. There is nothing in the VCL code that treats this user group specially. The group could be renamed, removed, or have various permissions changed. However, when an upgrade is done to a new version of VCL, the upgrade process will attempt to grant this group any new permissions that have been added in the new version of VCL. So, it is helpful to keep the group around.

**Allow No User Check** - This user group is considered to be a "system group" and cannot be deleted or renamed. It allows users to see the **Disable timeout for disconnected users** checkbox when making a basic reservation. This prevents the reservation from being timed out due to the user being disconnected for too long. The time after which a reservation is timed out due to a user being disconnected is set under the **Re-connect To Reservation Timeout** value on the [Site Configuration](Site-Configuration.html) page. The default value for this is 15 minutes. Reservations longer than what is set under the **Connected User Check Threshold** value on the Site Configuration page are never timed out due to a user being disconnected. The default value for this is 24 hours. Imaging and server reservations are never timed out due to a user being disconnected.

**Default for Editable by** - This user group is a little more unusual. When creating user groups, a user group must be specified that is allowed to edit the membership of the group being created. In the past, if a user did not know what group to select, there was often not a group that was an obvious choice. This resulted in users selecting random groups, granting anyone in those groups access to edit the membership of the group being created. The Default for Editable by group was introduced to help solve this problem. This group is the default group selected. If the user changes it and submits another group as the Editable by group, that other group is then saved in a cookie to be used as the default group the next time. Membership of this group should be kept to a minimal set of site wide admins.

**manageNewImages** - This user group is a system group that cannot be renamed or deleted. It is the user group set as the Owning User Group for the [special resource groups](special-resource-groups.html) that are used in relation to new image creation. Users in this group will have access to all newly created images by any user.

The **Specify End Time** user group allows users to see the At this time ending option for basic and imaging reservations, which allows a user to specify the exact end time of a reservation instead of just specifying a duration. This allows a user to make a reservation for much longer than is allowed by selecting a duration.
