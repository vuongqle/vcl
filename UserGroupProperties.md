---
title: User Groups Properties
last_updated: March 19, 2019
permalink: User-Groups-Properties.html
---

Each User Group has the following properties:

**Name** and **affiliation** - Each user group has its own unique name and affiliation. User groups can have the same name but a different affiliation. This allows for things like having an admin group for each affiliation. If a user sets **View User Groups: matching my affiliation** under User Preferences->General Preferences, then the affiliation portion is not displayed when adding and editing a user group.

**Owner** - This is the user that owns the user group. For custom user groups, only the owner can set the attributes of a user group that are listed on this page. The owner is entered in the form of username@affiliation

**Editable by** - This is a user group this is allowed to edit the membership of the user group. Users in the user group must also have the groupAdmin privilege granted somewhere in the Privilege Tree to be able to edit the group.

**Initial Max Time** - This is the initial maximum duration a user in this group can select when creating reservation. For initial, total, and max extend times, the highest value of all of a user's groups is what is used for a given user.

<img src="images/image2017-3-1 14_23_49.png" width="500" border="1">

**Total Max Time** - This is the total allowed duration a user may have for normal reservation, including the initial time and all extensions. This only affects reservations where the length is set by specifying a reservation duration. This setting does not apply for reservations where the length is determined by explicitly setting a start and end time for the reservation.

**Max Extend Time** - This is the amount of time a user can extend a reservation at a time. The idea behind this setting and having separate initial and total reservation times is to provide more opportunity for users to make reservations on resource constrained installations of VCL.

**Max Overlapping Reservations** - This setting controls how many reservations a user may have with overlapping times. The allowed values are 0 or 2 or more (i.e. 1 is not valid). If a user attempts to make a reservation that would exceed the allowed number of overlapping reservations, an error will be displayed to the user stating the user cannot have any more overlapping reservations. In order to see this setting for user groups, a user must be in a user group that has **the Set Overlapping Reservation Count** Additional User Group Permission under Privileges->Additional User Permissions.
