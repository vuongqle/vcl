---
title: Management Nodes
last_updated: Feb 15, 2019
permalink: Management-Nodes.html
---
Management nodes handle all of the provisioning aspects of VCL. vcld is the backend daemon that runs on the management nodes and processes everything related to reservations. There can be as many management nodes in a VCL installation as needed, but every installation must have at least one. A management node is limited by it's capacity as to how many worker/user nodes it can manage. As a result, the larger the VCL instantiation is, the more management nodes will need to be used. The vcld portion of VCL can be installed on a management node by using the [VCL installation script](VCL-installation.html), and passing arguments to only install the management node portions if that is desired.

## Adding and Editing Management Nodes

Management node information is manipulated via the VCL web site under the Management Nodes section and after selecting *Edit Management Node Profiles.*

<img src="images/image2017-1-24 16_47_44.png" width="500" border="1">

<img src="images/image2017-1-24 16_49_34.png" width="500" border="1">

The fields denoted with * are the only required fields. The remaining fields are optional and use of them depends on a number of factors.

**SysAdmin Email Address(es)**: Comma delimited list of email addresses for sysadmins who should receive error emails from this management node. Leave empty to disable this feature.

**Address for Shadow Emails**: Single email address to which copies of all user emails should be sent. This is a high traffic set of emails. Leave empty to disable this feature.

**Check-in Interval (sec)**: the number of seconds that this management node will wait before checking the database for tasks.

**Install Path**: path to parent directory of image repository directories (typically /install) - only needed with bare metal installs or VMWare with local disk

**Time Server(s)**: comma delimited list of time servers for this management node

**End Node SSH Identity Key Files**: comma delimited list of full paths to ssh identity keys to try when connecting to end nodes (optional)

**SSH Port for this Node**: SSH port this node is listening on for image file transfers

**Enable Image Library**: Enable sharing of images between management nodes. This allows a management node to attempt fetching files for a requested image from other management nodes if it does not have them.

**Image Library Management Node Group**: This management node will try to get image files from other nodes in the selected group.

**Image Library User**: userid to use for scp when copying image files from another management node

**Image Library SSH Identity Key File**: path to ssh identity key file to use for scp when copying image files from another management node

**Public NIC Configuration Method**: Method by which public NIC on nodes controlled by this management node receive their network configuration

- **Dynamic DHCP** - nodes receive an address via DHCP from a pool of addresses
- **Manual DHCP** - nodes always receive the same address via DHCP
- **Static** - VCL will configure the public address of the node

**Public Netmask**: Netmask for public NIC

**Public Gateway**: IP address of gateway for public NIC

**Public DNS Server**: comma delimited list of IP addresses of DNS servers for public network

**Available Public Networks**: This is a list of IP networks, one per line, available to nodes deployed by this management node. Networks should be specified in x.x.x.x/yy form.  It is for deploying servers having a fixed IP address to ensure a node is selected that can actually be on the specified network.

**Affiliations Using Federated Authentication for Linux Images**: Comma delimited list of affiliations for which user passwords are not set for Linux image reservations under this management node. Each Linux image is then required to have federated authentication set up so that users' passwords are passed along to the federated authentication system when a user attempts to log in. (for clarity, not set setting user passwords does not mean users have an empty password, but that a federated system must authenticate the users)

**Use as NAT Host**: controls whether or not the management node can be used as a NAT host

**NAT Public IP Address**: This is the IP address on the NAT host of the network adapter that is public facing. Users will connect to this IP address.

**NAT Internal IP Address**: This is the IP address on the NAT host of the network adapter that is facing the internal network. This is how the NAT host will pass traffic to the VCL nodes.

## Management Node Grouping & Mapping
### Grouping

Access to manage the profile information of a management node is always available to the owner of the node. Others can be given access by putting the management node in a group and assigning the **administer** attribute for that group under the [Privileges](privileges.html) section of the site. Putting the management node in a group is done under Management Nodes->Edit Grouping & Mapping.

<img src="images/image2017-1-25 14_16_7.png" width="500" border="1">

### Mapping

Configuration of what management nodes control which computers is done by mapping management node groups to computer groups.Typically, management nodes are mapped to computers such that only a single management node will process reservations for any given computer. It should work for multiple management nodes to be able to control multiple computers (at least for VMs), but this has not been well tested.

<img src="images/image2017-1-25 14_22_9.png" width="500" border="1">

## Configuration Tips

### logrotate
vcld writes quite a bit of information to a log file located at /var/log/vcld.log. It is a good idea to set up logrotate to rotate this log so that it doesn't consume too much space. A file named /etc/logrotate.d/vcld can be created with the following contents to have it rotated (assuming logrotate is installed):

    /var/log/vcld.log {
      compress
      missingok
      nomail
      notifempty
      rotate 10
      size=500M
      postrotate
        service vcld restart
      endscript
    }

## Mail
By default, VCL sends emails to users about the status of their reservations. Also, it is a good idea to set a value for SysAdmin Email Address so that error notification emails will be sent to administrators. Due to these emails, an email program such as sendmail or postfix should be installed and configured so that mail can properly be sent from the management nodes.

## Firewall
Management nodes are on physical networks with all of the provisioned nodes. So, the firewall on the management node should be carefully configured to only allow minimal access from the compute nodes. Management nodes often provide DHCP, DNS, and sometimes NTP services to the compute nodes. If so, exceptions should be allowed for these protocols. All other access to the management nodes should be blocked from the compute nodes unless it is related to some other service specifically being served out to the compute nodes.
