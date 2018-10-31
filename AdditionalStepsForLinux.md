---
title: Additional Steps for Linux
tags: [Linux, Image, Creation]
keywords: steps, linux, image, creation
last_updated: Oct 31, 2018
summary:
sidebar: mydoc_sidebar
permalink: additional-steps-for-linux.html
---

On the Connect! page, the following connection information will be displayed:

* The remote VCL computer's IP address
* The userid to use (it should be your own)
* The password to use when connecting

Log in using SSH. You can use X11 forwarding to run graphical applications

After you have logged in to the remote machine, you can gain root access by running the command **sudo bash**  

You have up to 8 hours to configure and save your image before the reservation expires and the node is wiped clean.

The following steps form a good guideline of what to do while creating your image:

1. Install your applications. Here are some suggestions/tips on loading software to the remote machine:
    *  If you have access to enough network storage, it may be easiest to copy the software installation files in network filespace and access them from the remote machine.
    * Copy the media to the remote machine using SCP (WinSCP is a good and simple SCP application for Windows)
2. Post install of software:
    * Remove any copies of software installation files because they needlessly cause the image size to be larger. This may increase the loading time for reservations of the image.
    * Configure any application customizations
3. [Save the Image](save-the-image.html)
