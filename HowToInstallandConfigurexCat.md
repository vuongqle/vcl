---
title: How to Install and Configure xCat
last_updated: Jan 23, 2019
sidebar: mydoc_sidebar
permalink: How-to-Install-and-Configure-xCat.html
---

## IBM BladeCenter Configuration

### Set the boot order of the blades

The blades need to be configured to attempt to boot from the network before booting from the hard drive.

* Log in the the BladeCenter Advanced Management Module
* Select Blade Tasks > Configuration
* Select the Boot Sequence tab:
    <img src="images/BootOrder1.png" width="600" border="1">
* Click on one of the blade names
* Change the boot order so that Network precedes Hard drive 0:
    <img src="images/BootOrder2.png" width="600" border="1">
* Select the Apply to all blades checkbox
* Click Save

### Configure SNMPv3
* Select Login Profiles
* Select a login ID that is not used
    <img src="images/AMMuser0.png" width="600" border="1">
* Enter a Login ID and password
    <img src="images/AMMuser1.png" width="600" border="1">
* Under role, select Supervisor
* Click Save
* Click the user which was just created
* Scroll to the bottom and click Configure SNMPv3 User
    <img src="images/AMMuser2.png" width="700" border="1">
* Enter the username and password of the user and set the rest of the fields as follows (replace 0.0.0.0 with the management node's control network IP address for security):
    <img src="images/AMMuser3.png" width="600" border="1">
* Click Save

## Install xCat

On the VCL management node:

#### Download the xCAT yum repository files:

    wget -N -P /etc/yum.repos.d
    http://sourceforge.net/projects/xcat/files/yum/xcat-dep/rh6/x86_64/xCAT-dep.repo

    wget -N -P /etc/yum.repos.d
    http://sourceforge.net/projects/xcat/files/yum/2.8/xcat-core/xCAT-core.repo

#### Install xCAT

        yum clean all
        yum install -y xCAT

#### Configure the xCAT site table

Set the "master" and "nameservers" keys in the site table to point to the xCAT management node's private IP address:

        chtab key=master site.value=x.x.x.x

        chtab key=nameservers site.value=x.x.x.x

Set the "forwarders" key in the site table to point to a DNS server:

        chtab key=forwarders site.value=y.y.y.y

Set the "dhcpinterfaces" key in the site table to the xCAT management node's private network interface. This causes the DHCP server running on the management node to only listen on this interface:

        chtab key=dhcpinterfaces site.value=ethX

#### Configure the xCAT networks table

Disable all networks detected by xCAT except the private network so that xCAT does not attempt to add unwanted information to DHCP or other services.  First, dump the contents of the networks table to determine the network names created by xCAT:

        tabdump networks

For each network except the private network, run the following command to set the network to disabled:

        chtab netname=<netname> networks.disable=1

#### Configure the blade node group

Set the nodehm table to use blade control methods:

        chtab node=blade nodehm.mgt=blade

Set the noderes table to use the xnba network boot method:

        chtab node=blade noderes.netboot=xnba

Set the xCAT master, TFTP, and NFS srever values to point to the xCAT management node's private IP address:

        chtab node=blade noderes.xcatmaster=x.x.x.x

        chtab node=blade noderes.tftpserver=x.x.x.x


#### Configure the xCAT passwd table

Set the username and password for the blade key to the credentials used to log in to the Advanced Management Module:

        chtab key=blade passwd.username=<xcatuser> passwd.password=<xcatpassword>

Set the root password for the system key. When a Linux image is loaded via Kickstart by xCAT, this will be the root password initially set on the loaded computer:

        chtab key=system passwd.username=root passwd.password=<linuxpassword>

#### Add a node to xCAT for each chassis Advanced Management Module

        nodeadd bcamm-X groups=mm

        nodeadd bcamm-Y groups=mm

#### Add a node to xCAT for every blade controlled by the management node

        nodeadd vcl-X-1..vcl-X-14 groups=all,blade,compute

        nodeadd vcl-Y-1..vcl-Y-14 groups=all,blade,compute

For each blade, set the mp.mpa value to the AMM node name, and mp.id value to the corresponding blade slot

        for i in {1..14} ; do chtab node=vcl-X-$i mp.mpa=bcamm-X mp.id=$i ; done

        for i in {1..14} ; do chtab node=vcl-Y-$i mp.mpa=bcamm-Y mp.id=$i ; done

Set the hosts.ip value to the private IP address for each AMM and blade

        chtab node=bcamm-X hosts.ip=<x.x.x.x>

        chtab node=vcl-X-1 hosts.ip=<x.x.x.x>

        ...

        chtab node=vcl-Y-14 hosts.ip=<x.x.x.x>

Create the /etc/hosts file

        makehosts -n

Retrieve MAC address for each blade

        getmacs blade -i eth0

Initialize DHCP and create a definition for each blade

        makedhcp -n

        chkconfig dhcpd on

        makedhcp blade

Download a CentOS ISO image

        wget -N -P /install/software
        http://ftp.linux.ncsu.edu/pub/CentOS/6/isos/x86_64/CentOS-6.5-x86_64-bin-DVD1.iso

Import the CentOS image into the xCAT install tree

        copycds /install/software/CentOS-6.5-x86_64-bin-DVD1.iso

Configure the blades to install the CentOS using xCAT's stock "compute" template

        chdef blade os=centos6.5 arch=x86_64 profile=compute

Set the blades to install the image on the next reboot

        nodeset blade install

Power cycle each blade

        rpower blade boot
