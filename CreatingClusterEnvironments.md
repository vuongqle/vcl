---
title: Creating Cluster Environments
tags: [creating, cluster, environments]
keywords:
last_updated: Nov 1, 2018
summary:
sidebar: mydoc_sidebar
permalink: creating-cluster-environments.html
---

Cluster environments are created from existing VCL images.

The terms parent and child are used in this document and with the VCL provisioning:

* **parent** =  the primary image or selectable image through the VCL New Reservation menu
* **child** = the second image or images that are also loaded and are the sub images associated with the parent image

A file containing the parent and child public IP addresses are added to a cluster_info text file that is located on each node. The file locations are either /etc/cluster_info or C:\cluster_info


The firewall on each node in a cluster is automatically configured by VCL to allow all traffic from each other node in the cluster.

### To Create a Cluster Image

* Select Manage Images -> Edit Image Profiles
* Select edit beside the parent image
* Click on  Advanced Options
* Select Manage Subimages
* In the window select a child image
    * The child image can be the same as the parent image or another.
* Repeat adding as many subimages as needed (or have nodes for).


Additional features that can assist in a cluster are the vcl_post_load and vcl_post_reserve scripts. These two scripts are useful to configure the cluster, start or restart services, or other tasks the require unique settings. Since the hostname and IP address of VCL nodes will change when provisioned customization will be needed to make things work properly.

The scripts are:

* vcl_post_load
    * File location in Linux /etc/init.d/vcl_post_load
    * File location in Windows is SYSTEMROOT, usually C:\Windows\vcl_post_reserve.cmd
    * Executed after a node is loaded during the post_load process during a new reservation or reload
    * Created by the image owner
    * Ideal for editing configuration files and starting services
* vcl_post_reserve
    * Linux File location /etc/init.d/vcl_post_load
    * Windows File location is SYSTEMROOT, usually C:\Windows\vcl_post_load.cmd
    * Executed during an active reservation, when user hits "Connect" button
    * Created by the image owner
    * Ideal for making custom changes to configuration files, such as Cluster on Demand
