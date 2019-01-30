---
title: How to Remotely Automate and Manage User Groups
last_updated: Jan 28, 2018
sidebar: mydoc_sidebar
permalink: How-to-Remotely-Automate-and-Manage-User-Groups.html
---

managegroups.py is a script for remotely managing VCL user groups. It uses VCL's XML RPC API to provide an easy command line driven way of doing group management.

*NOTE: This script requires python 3.x.*

### Download Information

managegroups.py is not included in any official releases. You can download it from our subversion repository.

[Download managegroups.py](https://svn.apache.org/repos/asf/vcl/sandbox/useful_scripts/managegroups.py)

### Available Commands

Running managegroups.py with no arguments provides help on how to use it. It is used by specifying one of the following commands along with parameters specific to each command.

* #addUserGroup - creates a new user group
* #getUserGroupAttributes - gets information about a user group
* #deleteUserGroup - deletes a user group
* #editUserGroup - modifies attributes of a user group
* #getUserGroupMembers - gets members of a user group
* #addUsersToGroup - adds users to a group
* #removeUsersFromGroup - removes users from a group
* #emptyGroupMembership - removes all users currently in a group

### Return Status

These are the possible values of the return status:

* 0 - successful execution
* 1 - missing authentication information
* 2 - problem with command line parameters
* 3 - for commands that pass in a filename, problems encountered reading the file
* 4 - no users specified to add to or remove from a group
* 5 - error encountered while performing XML RPC API call

### Return Output

The output of managegroups.py will always start with one of:

* SUCCESS: - indicates successful run of the command
* ERROR: - indicates a problem running the command
* WARNING: - indicates a no errors, but possibly unexpected results (i.e. specifying a file when adding users to a group, but the file is empty)
    * There is one exception. If you specify a parameter, but omit the argument for it, the option parser used in the script will generate an error. However, in those cases, the return status of the command will always be 2.

### Authentication

The script needs to know what userid/password to use and what URL to access. These can either be defined as variables within the script (look at the very top of the file) or specified as parameters on the command line, before specifying which of managegroups's commands to use.


There are two ways to specify authentication information because some people may prefer to have the password saved in the file, but not in a command line history, while others may prefer to have the password saved in command line history, but not in the file. Any of the authentication options specified on the command line will override any defined in the file.

The options for specifying authentication on the command line are

* -u username - log in to VCL site with this user, must be in username@affiliation form
* -p "vclpass" - password used when logging in to VCL site, use quotes if it contains spaces
* -r vclurl - XMLRPC URL of VCL site (it will end with index.php?mode=xmlrpccall - i.e. https://vcl.ncsu.edu/scheduling/index.php?mode=xmlrpccall)

### addUserGroup

Use this command to create a new user group.

parameters:

* -n name - name of new user group
* -a affiliation - affiliation of new user group
* -o owner - user that will be the owner of the group in username@affiliation form
* -m ManagingGroup - name of user group that can manage membership of the new group
* -i InitialMaxTime - (minutes) max initial time users in this group can select for length of reservations
* -t TotalMaxTime - (minutes) total length users in the group can have for a reservation (including all extensions)
* -x MaxExtendTime - (minutes) max length of time users can request as an extension to a reservation at a time

on success, returns;

        SUCCESS: User group successfully created

### getUserGroupAttributes

Use this command to get existing information about a user group's attributes (it does not include the current membership of the group).
parameters:

* -n name - name of an existing user group
* -a affiliation - affiliation of user group

on success, returns:

        SUCCESS: Attributes retrieved

followed by:

        owner: <user group owner>
        managingGroup: <name of managing user group>
        initialMaxTime: <max allowed initial reservation time>
        totalMaxTime: <total allowed reservation time>
        maxExtendTime: <make time allowed per extension>

### deleteUserGroup

Use this command to delete an existing user group.
parameters:

* -n name - name of an existing user group
* -a affiliation - affiliation of user group

on success, returns:

        SUCCESS: User group successfully deleted

### editUserGroup

Use this command to modify attributes of an existing user group (it is not used for editing the membership of the group). You can specify any combination of the parameters labeled as optional.
parameters:

* -n name - name of an existing user group
* -a affiliation - affiliation of user group
* -N NewName - (optional) new name for the user group
* -A NewAffiliation - (optional) new affiliation for the user group
* -O NewOwner - (optional) new owner for the user group in username@affiliation form
* -M NewManagingGroup - (optional) new user group that can manage membership of the user group in group@affiliation form
* -I NewInitialMaxTime - (optional) new max initial time users in the group can select for length of reservations
* -T NewTotalMaxTime - (optional) new total length users in the group can have for a reservation (including all extensions)
* -X NewMaxExtendTime - (optional) new max length of time users can request as an extension to a reservation at a time

on success, returns:

        SUCCESS: User group successfully updated

### getUserGroupMembers

Use this command to get the current members of a group. Note that it is possible for a group to have no members.
parameters:

* -n name - name of an existing user group
* -a affiliation - affiliation of user group

on success, returns:

        SUCCESS: Membership retrieved

followed by one user per line in username@affiliation form

### removeUsersFromGroup

Use this command to remove users from an existing user group.
parameters:

* -n name - name of an existing user group
* -a affiliation - affiliation of user group

Additionally, at least one of these must be specified:

* -l UserList - comma delimited list of users to add (no spaces) in username@affiliation form
* -f filename - name of file containing users to add (one user per line) in username@affiliation form

on success, returns:

        SUCCESS: Users successfully removed from group

### emptyGroupMembership

Use this command to empty the membership of an existing user group.
parameters:

* -n name - name of an existing user group
* -a affiliation - affiliation of user group

on success, returns:

        SUCCESS: Users successfully removed from group

### Examples
The last example includes the authentication information on the command line. For the other examples, the authentication would have been specified inline in the script. Authentication information was only included in one example to make the others more readable.

Create a new user group with name FallUsers, affiliation Local, admin as the owner, and adminUsers@Local as the managing group:

        managegroups.py addUserGroup -n FallUsers -a Local -o admin@Local -m adminUsers@Local -i 240 -t 360 -x 30

Change the name of an existing user group named FallUsers, affiliation Local to be SpringUsers:


        managegroups.py editUserGroup -n FallUsers -a Local -N SpringUsers

Add two users specified on the command line to a group:

        managegroups.py addUsersToGroup -n FallUsers -a Local -l student1<at:var at:name="Local,student2" />Local

Add all users in a specified file to a group:

        managegroups.py addUsersToGroup -n FallUsers -a Local -f newusers.txt

The file would contain something like the following:

        userid1@Local
        userid2@Local
        userid3@Local

Remove all members of a group:

        managegroups.py emptyGroupMembership -n CS101 -a Local

Delete a user group:

        managegroups.py -u admin@Local -p passwordhere -r 'https://your.vcl.site/index.php?mode=xmlrpccall' deleteUserGroup -n CS101 -a Local
