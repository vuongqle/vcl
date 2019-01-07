---
title: xCat Configuration
last_updated: Jan 07, 2019
sidebar: mydoc_sidebar
permalink: xCat-Configuration.html
---

## Variable Table Configuration Options

There are a few xCAT options which may be configured using the variable table in the database.  At the current time, these must be configured manually using a database query or phpMyAdmin.

Each of the following variables may be configured for a specific management node or globally.  To configure a variable for a specific management node, the exact value of the hostname column in the managementnode table must be appended to the variable.name value.  If both the global and management node specific variables are configured, the one configured for a specific managment node takes precedence.

## Concurrent Load Throttle

Controls the maximum number of compute nodes which may be loading at the same time.  When a new or reload reservation is processed, this variable is checked before the compute node reload is started.  If this variable is not set in the database, a default value of 10 is used.

The xCAT.pm module calls nodeset to retrieve the status of all of the nodes controlled by the management node and then counts how many have their status set to either 'install' or 'image'.  For each of these compute nodes, the code checks whether there is an running vcld process for that compute node.  The nodes are not considered against the load throttle limit if there is no process running.  If the total number of nodes is greater than or equal to the throttle limit, the reservation will wait up to 30 minutes for existing loading nodes to finish.  Once the actively loading node count is less than the throttle limit, the waiting reservation proceeds to begin its load process.

**variable.name:** | **xcat \ throttle \ managementnode.hostname**
                                   | **xcat \ throttle**

## xCAT Command Timeout Limit

The xCAT utilities will occasionally fail and display a timeout error message under some circumstances, especially when several compute nodes are being loaded concurrently.  This is a normal error.  Making subsequent attempts to run the command usually succeeds.  This variable controls how may timeout errors may occur before the xCAT.pm module gives up.


**variable.name:** | **xcat \ timeout_error_limit \ managementnode.hostname**
                         | **xcat \ timeout_error_limit**

## rpower Error Limit

xCAT's rpower command fails more often than most other commands.  This variable controls how many consecutive rpower errors may occur before giving up.

**variable.name:** | **xcat \ rpower_error_limit \ managementnode.hostname**
                                  | **xcat \ rpower_error_limit**
