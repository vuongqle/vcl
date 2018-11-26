---
title: Create a Windows Base Image
last_updated: Nov 15, 2018
sidebar: mydoc_sidebar
permalink: Create-A-Windows-Base-Image.html
---

This page describes how to create a Windows base image.These instructions should work regardless of the provisioning engine being used (xCAT, VMware, etc.).  **Ignore the VMware Only** sections if you are attempting to create an image using xCAT or some other bare metal provisioning engine.

### Requirements

You will need the following:

* Windows installation ISO file
* Windows product key or KMS server address

 The following must be done before an image can be captured:

* The web, database, and management node has been installed and the management node is checking into the database.
* A computer for the machine being captured has already been added to the VCL database
* VMware Only:
    * A VM host computer on which the guest is running as been added to the VCL database
    * The guest VM has been assigned to the VM host via the Virtual Hosts link on the VCL website

These instructions assume you have root access and are using a bash shell.


### VMware Only: Create a Virtual Machine

#### Create a Virtual Machine

##### VMware ESX, ESXi 4, 5, vcenter

The instructions assume that VMware has been configured with the following bridged networks:

* Private: bridged to private interface: eth0
* Public: bridged to public interface: eth1

Use the VMware vSphere client for the following task:

* Click File > New > Virtual Machine
* Configuration: Custom
* Name: win7
* Datastore: datastore
* Virtual Machine Version: 7
* Guest Operating System: Windows
    * Version: Microsoft Windows 7 (32-bit)
* Number of virutal sockets: 1
* Number of cores per virtual socket: 1
* Memory Size: 4 GB
* How many Nics: 2
    * NIC 1: Private, Adapter: E1000, Connect at Power On: Yes
    * NIC 2: Public, Adapter: E1000, Connect at Power On: Yes
* SCSI controller: LSI Logic SAS
* Disk: Create a new virtual disk
    * Capacity: 24 GB
    * Disk Provisioning: Thin Provision
    * Location: Specify a datastore or datastore cluster
        * Click Browse
        * Select the local datastore
        * Click OK
* Virtual Device Node: SCSI (0:0)
    * Mode: Not Independent (unchecked)
* Edit the virtual machine settings before completion: Yes
* In the Hardware pane, select Add...
    * Device Type: CD/DVD Drive
    * Select CD/DVD Media: Use ISO image
    * Select ISO Image:
        * Click Browse
        * Select the location datastore (were the ISO is located)
        * Click Open
        * Select Windows7-SP1-32.ISO
        * Click Open
* Connect at power on: Yes (checked)
* Select the New NIC (adding) entry with Private listed next to it** Under MAC Address, select Manual
    * Enter the private MAC address you retrieved earlier
    * Click Finish
* Click Finish.

### Start the VM and Install Windows

* Select the win7 VM
* Click the play button to power on the VM
* View the Console tab to watch the VM boot
wait for?
* Enter the regional information:
    * Language to install: English
    * Time and currency format: English (United States)
    * Keyboard or input method: US
* Click Next
* Click Install now

        Setup is starting...

* Click the checkbox next to "I accept the license terms"
* Click Next
* Click Custom (advanced)
* Where do you want to install Windows?: Disk 0 Unallocated Space
* Click Next

        Installing Windows...
        Windows restarts
        Starting Windows
        Setup is updating registry settings

* A screen titled "Set Up Windows" appears:
    * Type a user name: root
    * Type a computer name: it's best to name the computer after the OS (Example: win7sp1)
* Enter a password, password hint, and click Next
* Help protect your computer and improve Windows automatically: Ask me later
* Select a time zone, set the correct time, and click Next

        Windows is finalizing your settings
        Preparing your desktop
        Desktop appears

* If asked to set a network location, choose Work network

        The root account logs in...

### Enable RDP

* Open Control Panel > System and Security > System
* Click Remote settings
* Select Allow connections from computers running any version of Remote Desktop (less secure)
* Click OK

Use an RDP client to connect to the Windows computer using either its public or private IP address as appropriate.

### Connect via RDP

* Find the IP address assigned to your VM on the Public port:
    * Start->Search
    * Enter cmd
    * Run cmd
    * type ipconfig and look your public IPv4 address x.x.x.x address
* Connect to the Windows 7 computer using RDP
* Login to the RDP session as root

### Disable User Account Control

User Account Control (UAC) is the mechanism that causes may of the pop-up windows to appear when you attempt to run programs on Windows 7 and Windows Server 2008. VCL will disable it when the image is captured but you can disable it while configuring the base image to make things a little easier.

* Open the Control Panel
* Click System and Security > Change User Account Control settings (Under Action Center)
* Move the slider to the bottom: Never notify
* Click OK
* Reboot the computer


### *VMware Only:* Install VMware Tools
1. Power on the VM if it is not already powered on
2. Install VMWare Tools  (Note: you must have a CD-ROM drive configured for the VM in order to install VMware Tools)
    * Click on the VM menu and select "Install VMWare Tools"
    * Select Typical and proceed through the setup pages accepting the defaults
    * Reboot the VM when installation is complete

### Install Cygwin SSHD

#### Run vcld -setup

1. Run the following command on the management node:

            /usr/local/vcl/bin/vcld -setup

2. Navigate the menu options

    (Note: the names and numbers of the menu items may not match your installation):

    1. Select a module to configure: VCL Image State Module

    2. Choose an operation: Capture Base Image

    3. Enter the VCL login name or ID of the user who will own the image:

        Enter your VCL user ID or the user ID of the user you want to own the image.  Pressing Enter without entering a user login ID will cause admin to be the owner of the new base image.

    4. Enter the hostname or IP address of the computer to be captured:

        Enter the name or private IP address of the computer which has already added to the VCL database.


    5. Select the OS to be captured:

        1. VMware Linux
        2. VMware Windows 2003 Server
        3. VMware Windows 7
        4. VMware Windows Server 2008
        5. VMware Windows Vista
        6. VMware Windows XP

    6. Image architecture:

        1. x86
        2. x86_64

    7. Use Sysprep:

        1. Yes
        2. No

        Sysprep is usually only required if the image will be loaded on bare metal computers with varying different hardware.

    8. Enter the name of the image to be captured:

        The name you enter is the name that will be displayed in the list of environments.  It may contain spaces but including other special characters is not recommended.


The following happens once you enter an image name and press enter:

* A new image is added to the VCL database

* An imaging request is added to the VCL database

* The vcld -setup automatically initiates 'tail -f /var/log/vcld.log' to monitor the vcld log file.  The output should be displayed on the screen.


Watch the vcld logfile output to determine if the image capture process is successful or terminated because a problem occurred.  When the capture process terminates, there will either be a message near the end of the output saying "image capture successful" or there will be several WARNING messages, the last of which says something to the effect "image failed to be captured".  Further troubleshooting is required if the image fails to be captured.

### Add the Base Image to an Image Group


The vcld -setup utility does not add the new base image to any image groups.  You must add the image to an image group using the VCL website after the image capture process is complete.  Reservations for the image cannot be made until this is done.  To add the image to an image group, browse to the VCL website and select Manage Images > Edit Image Grouping.
