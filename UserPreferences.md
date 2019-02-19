---
title: User Preferences
last_updated: Feb 14, 2019
permalink: user-preferences.html
---

User preferences allows the user to control some basic settings related to their specific VCL account. This interface is accessed at Manage menu under User Preferences


<img src="images/Screen Shot 2017-03-17 at 3.03.28 PM.png" width="400" border="1">

### Personal Information

Personal information contains the First, Last, Preferred name and email address. In a federated environment only the Preferred name can be changed.


<img src="images/Screen Shot 2017-03-17 at 3.23.31 PM.png" width="300" border="1">


### RDP Preferences

RDP(Remote Desktop Protocol) preferences allow to change variables for the VCL provided RDP file. This file is provided for each reservation using the RDP connect method.

<img src="images/Screen Shot 2017-03-17 at 3.22.01 PM.png" width="400" border="1">

**Resolution**: Changes the size of the Remote Desktop Application window.

**Color Depth**: Specify the maximum color resolution (color depth) for a remote session. Limiting the color depth can improve connection performance, particularly over slow links, and reduce server load.

**Audio**: If yes, will play audio or sound on the local machine

**Map Local Drives**: If yes, allows you to share local drives to the remote VCL machine you are going to connect to with full read/write access.

**Map Local Printers**: If yes, allows you to share local printers to the remote VCL machine you are going to connect to.

**Map Local Serial Ports**: If yes, allows you to share local serial ports to the remote VCL machine you are going to connect to.

**RDP Port**: Configuration option to change the network port the RDP client should use to connect to the remote machine.

### General Preferences

Under general preferences you can set additional variables related to viewing user groups, email notifications, enabling ssh keys or PKI (Pubic Key Infrastructure).


<img src="images/Screen Shot 2017-03-17 at 3.36.38 PM.png" width="300" border="1">

**View User Groups**: (admin only) Allows VCL administrators to limit the only view user groups matching their affiliation. Useful with VCL instances that support multiple affiliations.

**Send email notifications about reservations**: Enable or Disable VCL email notifications. Enabled by default.

**Use public key authentication for SSH logins**: Enable or Disable ssh key login for ssh connect methods.

**Public keys**: Location to copy your ssh public key
