---
title: How to Add Local VCL Accounts
last_updated: Jan 08, 2019
sidebar: mydoc_sidebar
permalink: How-to-Add-Local-VCL-Accounts.html
---

Local VCL accounts are contained within the VCL database. The admin account is an example of a local VCL account. Additional local accounts can be added after the backend management node component has been installed by executing the vcld file with the -setup argument:

    /usr/local/vcl/bin/vcld -setup

*It is safe to run vcld -setup while the normal vcld daemon process is running on a management node. Running vcld -setup will not affect it.*

You will see a menu. Enter the number next to the VCL Base Module entry:

    [root@mgt-node]# /usr/local/vcl/bin/vcld -setup
    VCL Management Node Setup
    ----------------------------------------------------------------------------
    Select a module to configure:
    1. VCL Base Module
    2. VCL Image State Module
    3. Windows OS Module

    [vcld]
    Make a selection (1-3, 'c' to cancel): 1

Enter the number next to the Add Local VCL User Account entry:

    ----------------------------------------------------------------------------
    Choose an operation:
    1. Add Local VCL User Account

    [vcld/User Accounts]
    Make a selection (1, 'c' to cancel): 1

Enter the requested information:

    Enter the user login name ('c' to cancel): localuser

    Enter the first name ('c' to cancel): Local

    Enter the last name ('c' to cancel): User

    Enter the email address [not set]: localuser@example.com

    Enter the password ('c' to cancel): ******

After adding the local user account, you can continue to navigate the menus or press Ctrl-C to exit.
