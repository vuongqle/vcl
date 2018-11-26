---
title: Install Windows for a Base Image
last_updated: Nov 15, 2018
sidebar: mydoc_sidebar
permalink: Install-Windows-for-a-Base-Image.html
---

This page describes how to mount the Windows installation media and install Windows for a base image.

### Mount the Installation Media

The Windows installation media needs to be mounted as a drive on the computer. The method to do this varies widely based on the provisioning engine being used and resources available. The following lists some ways to mount the installation media:

#### VMware - Configure the VM to mount the ISO image as a CD-ROM drive

Note: these instructions assume a VM has already been created

1. Copy the Windows installation ISO file to the VMware host server
2. Add a CD-ROM drive which mounts the Windows installation ISO image by editing the virtual machine settings:
    1. Connection: Use ISO image:
    2. Browse to path of Windows installation ISO image
    3. Save the VM configuration

#### xCAT using IBM Advanced Management Module

1. Copy the Windows installation ISO file to the management node
2. Determine the IP address or hostname of the IBM Advanced Management Module (AMM) for the BladeCenter chassis which contains the blade you are installing
3. Open a web browser and enter the AMM's address
4. Log in to the AMM
5. Select Inactive session timeout value: no timeout
6. Click Start New Session
7. Click Remote Control
8. Click Start Remote Control
9. Set the Media Tray and KVM dropdown menus to the blade you are installing
10. Click Select Image and click the arrow button to the right of it
11. Navigate to the Windows installation ISO file which was saved to the management node and click Open
12. Click Mount All
