---
title: Capture a  Base Image
last_updated: Nov 15, 2018
sidebar: mydoc_sidebar
permalink: Capture-A-Base-Image.html
---

### Run vcld -setup
1. Run the following command on the management node:
        /usr/local/vcl/bin/vcld -setup

2. Navigate the menu options

    (Note: the names and numbers of the menu items may not match your installation):

    1. Select a module to configure: **VCL Image State Module**
    2. Choose an operation: **Capture Base Image**
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
