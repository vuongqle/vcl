---
title: Additional Steps for Windows
tags: [Windows, Image, Creation]
keywords: steps, Windows, image, creation
last_updated: Nov 1, 2018
summary:
sidebar: mydoc_sidebar
permalink: additional-steps-for-windows.html
---
On the **Connect!** page, the following connection information will be displayed:

* The remote VCL computer's IP address
* The user ID you will use: Administrator

  *Note: the user ID is always Administrator for Windows imaging reservations*
* A one-time password for the Administrator account


Log in using Remote Desktop Connection. You can either enter the connection information manually after launching the Remote Desktop Connection program or you can use a pre-configured RDP file:

* Click the Get RDP File button
* Save the RDP file to your computer
* Double-click the RDP file to launch the Remote Desktop Connection program with the VCL connection information pre-configured

You have up to 8 hours to configure and save your image before the reservation expires and the node is wiped clean.

The following steps form a good guideline of what to do while creating your image:

1. Run Windows Update before saving an image.  It's OK if the computer needs to reboots.  It can take 4-6 minutes for the computer to reboot before you can connect via RDP again.
2. Install your applications. Here are some suggestions/tips on loading software to the remote machine:

    * Copy the software from your local computer to the remote computer by sharing your local drives through Remote Desktop Connection.  The shared drives show up in Windows Explorer on the remote machine.  Do not run the software's installation program directly from the shared drive.  Copy it to the remote computer first, run the setup program, then delete the installation files.
    * If you have an ISO image of the software, copy the ISO file to the remote machine using a shared drive then mount the ISO image as a drive letter.  The freeware program [MagicDisc](http://www.magiciso.com/tutorials/miso-magicdisc-overview.htm) does this very well
        * Install MagicDisc, mount the ISO file, and then run the application's installation program from the mounted drive.  When the installation is done, unmount the drive and delete the ISO file from the remote machine.  Uninstall MagicDisc before saving your image if you don't want it included.
        If you have a physical CD or DVD, and windows 8 or above, you can create an ISO image from the disc.
    * If you have access to enough network storage, you can copy the software installation files to the network filespace and access them from the remote machine.
    * Copy the media contents using SCP from the remote computer.  If you have a personal machine running an SSH server you can use this method.  You could also do the same thing using FTP.
3. Post install of software:
    * Remove any copies of software media (needlessly takes up space in the image which increases the loading time for new reservations of the image)
    * Make sure all wanted desktop icons are in "All users" desktop.
    * Configure any application customizations
    * [Perform customizations to the default user profile](how-to-configure-the-windows-default-profile.html)
4. [Save the Image](save-the-image.html)
