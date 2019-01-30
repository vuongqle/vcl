---
title: VCL Command Line Management "vcld --setup"
last_updated: Jan 30, 2018
sidebar: mydoc_sidebar
permalink: VCL-cmdline-management-"vcld--setup".html
---

The VCL service provides a method for handling certain actions from the cmdline on the management node. Through the cmdline tool, you can capture a base image, update local VCL User accounts, test the RPC-XML access and more.

        cd /usr/local/vcl/bin
        ./vcld --setup

A list of different options will be available:

        VCL Management Node Setup
        ----------------------------------------------------------------------------------------------------
        Check Configuration
        1: Check Windows OS Module

        Image Management
        2: Capture a Base Image

        Management Node Configuration
        3: Test RPC-XML Access

         Management Node Operations
        4: Check private IP addresses

        User Accounts
        5: Add Local VCL User Account
        6: Set Local VCL User Account Password

        VMware Provisioning Module
        7: VM Host Operations

        Windows Image Configuration
        Activation
        8: Configure Key Management Service (KMS) Activation
        9: Configure Multiple Activation Key (MAK) Activation

        [vcld]
        Make a selection (1-9, 'c' to cancel):

* Check Configuration
    * Check Windows OS Module

* Image Management
    * Capture a Base Image - Provides a method for capturing a base image from a fresh install. This medhod requires that the computer and virtual host are already defined in the VCL computer inventory, the machines are accessible by ssh from the VCL management node. Once selecting this option, the user will be prompted to answer serval questions in order to trigger an image capture.

* Management Node Configuration
    * Test RPC-XML Access - Selecting this option will trigger a test of the RPC-XML API for the web server component. For a successful test, the proper account needs to be configured. See page on how-to configure the RPC-XML settings: [XML-RPC based API](https://cwiki.apache.org/confluence/display/VCL/XML-RPC+based+API)

* Management Node Operations
    * Check private IP addresses -

* User Accounts
    * Add Local VCL User Account - Adds a new local VCL User account to the database
    * Set Local VCL User Account Password - Sets or updates an existing Local VCL account

* VMware Provisioning Module
    * VM Host Operations - Allows for cleaning up VMware datastores and repositories. After selecting this option, a list of managed VMhosts is provided to perform the actions on. Available operations are:
        1. Purge deleted and unused images from repository datastore
        2. Purge deleted and unused images from virtual disk datastore

*When selecting Operation #2 it will next prompt for the number days since the last reservation and also since image revision was created.*

* Windows Image Configuration
    * Configure Key Management Service (KMS) Activation - This option provides a method to list, Add or Delete KMS servers stored in the database.
    * Configure Multiple Activation Key (MAK) Activation - This option List, Adds, or Deletes product keys for Windows environments.
