---
title: Create a Linux Base Image
last_updated: Nov 15, 2018
sidebar: mydoc_sidebar
permalink: Create-A-Linux-Base-Image.html
---
This page describes how to create a Linux base image.

### Requirements

* Computer being captured has been added to the VCL database
* Computer has been installed with Linux
* Two network adapters are enabled on the computer:
    * eth0 - connected to the private network
    * eth1 - connected to the public network
* The ability to log in as root via SSH using an identity key on the private network from management node

#### Configure SSH Identity Key Authentication
1. On the Linux computer being captured, create a /root/.ssh directory:

        mkdir /root/.ssh

2. On the management node, copy the public SSH identity key to the authorized_keys file on the Linux computer being captured:

        scp /etc/vcl/vcl.key.pub <hostname or IP address>:/root/.ssh/authorized_keys

3. Or replace the above two steps with the following on the management node:

        ssh-copy-id -i /etc/vcl/vcl.key <hostname or IP address>

4. Make sure you can login from the management node to the Linux computer being captured using the identity key:

        ssh -i /etc/vcl/vcl.key <hostname or IP address>

#### Configure the ifcfg-* Files

1. Navigate to the network-scripts directory:

        cd /etc/sysconfig/network-scripts

2. Delete any ifcfg-*.bak files:

        rm -f /etc/sysconfig/network-scripts/ifcfg-*.bak

3. Edit every ifcfg-eth* file in the network-scripts directory. Remove the HWADDRESS= line:

        vi ifcfg-eth0

        vi ifcfg-eth1

    The ifcfg-eth0 file should contain the following:

        DEVICE=eth0
        BOOTPROTO=dhcp
        ONBOOT=yes

    The ifcfg-eth1 file should contain the following:

        DEVICE=eth1
        BOOTPROTO=dhcp
        ONBOOT=yes

4. Reboot the computer:

        shutdown -r now

5. Check the ifcfg-eth* files to make sure there are no ifcfg-eth* files and that the HWADDRESS= lines have not been automatically added back:

        ls /etc/sysconfig/network-scripts

        cat /etc/sysconfig/network-scripts/ifcfg-eth0

        cat /etc/sysconfig/network-scripts/ifcfg-eth1

#### Run vcld -setup
1. Run the following command on the management node:

        /usr/local/vcl/bin/vcld -setup

2. Navigate the menu options (Note: the names and numbers of the menu items may not match your installation):
    * Select a module to configure: VCL Image State Module
    * Choose an operation: Capture Base Image
    * Enter the VCL login name or ID of the user who will own the image:

        Enter your VCL user ID or the user ID of the user you want to own the image.  Pressing Enter without entering a user login ID will cause admin to be the owner of the new base image.

    * Enter the hostname or IP address of the computer to be captured:

        Enter the name or private IP address of the computer which has already added to the VCL database.

    * Select the OS to be captured:
        1. VMware Linux
        2. VMware Windows 2003 Server
        3. VMware Windows 7
        4. VMware Windows Server 2008
        5. VMware Windows Vista
        6. VMware Windows XP

    * Image architecture:
        1. x86
        2. x86_64
    * Use Sysprep:
        1. Yes
        2. No

            Sysprep is usually only required if the image will be loaded on bare metal computers with varying different hardware.

    * Enter the name of the image to be captured:

        The name you enter is the name that will be displayed in the list of environments.  It may contain spaces but including other special characters is not recommended.

The following happens once you enter an image name and press enter:

* A new image is added to the VCL database
* An imaging request is added to the VCL database
* The vcld -setup automatically initiates 'tail -f /var/log/vcld.log' to monitor the vcld log file.  The output should be displayed on the screen.


Watch the vcld logfile output to determine if the image capture process is successful or terminated because a problem occurred.  When the capture process terminates, there will either be a message near the end of the output saying "image capture successful" or there will be several WARNING messages, the last of which says something to the effect "image failed to be captured".  Further troubleshooting is required if the image fails to be captured.

#### Add the Base Image to an Image Group

The vcld -setup utility does not add the new base image to any image groups.  You must add the image to an image group using the VCL website after the image capture process is complete.  Reservations for the image cannot be made until this is done.  To add the image to an image group, browse to the VCL website and select Manage Images > Edit Image Grouping.
