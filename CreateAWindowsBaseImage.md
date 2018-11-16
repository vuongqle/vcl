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
