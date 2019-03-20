---
title: Using the VCL Dashboard
last_updated: March 19, 2019
permalink: Using-The-VCL-Dashboard.html
---

<img src="images/image2017-2-24 15_34_47.png" width="500" border="1">


The VCL Dashboard provides various information about the current state of the VCL system. It consists of blocks of information showing what is happening or has recently happened with some part of VCL. The following blocks are included:

* **Current Status** - lists the following 4 items:
    * Active Reservations - the number of reservations currently in use by users (doesn't include reload reservations, future reservations, timed out reservations, etc)
    * Online Computers - the number of defined computers not in maintenance or failed states
    * In Use Computers - the number of computers currently in use by users (can be greater than Active Reservations due to cluster reservations
    * Failed Computers - the number of computers in the failed state
* **Top 5 Images in Use** - lists the top 5 images being used by reservations where the duration of the reservation is less than 24 hours
* **Top 5 Long Term Images in Use** - lists the top 5 images being used by reservations where the duration of the reservation is greater than 24 hours
* **Top 5 Images From Past Day** - lists the top 5 images with a reservation start time within the previous 24 hours
* **Top Recent Computer Failures** - lists the top 5 computers that have had a reservation failure within the past 5 days (useful for locating problematic computers)
* **Block Allocation Status** - lists the following 3 items:
    * Active Block Allocations - lists the number of block allocations with a currently active time slot
    * Block Computer Usage - displays the number of reserved computers out of the total number of computers allocated to active block allocations - displayed in the form "x / y (z%)"
    * Failed Block Computers - displays the number of failed computers out of the total number of computers allocated to active block allocations - displayed in the form "x / y (z%)"
* **Management Nodes** - lists out all management nodes with the following information:
    * Time Since Check-in - time since the management node last checked in to the database; displayed in green if the time is < 60 seconds, orange if the time is < 2 minutes, red if > 2 minutes; times greater than 24 hours are just displayed as "> 24 hours"
    * Reservations Processing - the number of reservations assigned to the management node that are not in complete or maintenance states.
* **Top Recent Image Failures** - lists the top 5 images that have had a reservation failure within the past 5 days (useful for locating problematic images)
* **Past 12 Hours of Active Reservations** - graph that displays a chart of the number of reservations over the past 12 hours
* **Notable Reservations** - lists the following information about reservations currently being processed that are in these states: new, reload, reloading, image, checkpoint
    * Start - reservation start time
    * ReqID - ID from request table for the reservation
    * User - username and affiliation of user
    * Computer - hostname of computer
    * States - current and last states of reservation
    * Image - name of image reserved
    * Install Type - OS installation type for image (OS.installtype from database)
    * Management Node - hostname of management node processing reservation
* **Failed Imaging Reservations** - lists the following information about any failed imaging reservations along with a button "" to restart the image capture process again after attempting to fix any issues encountered during the capture process
    * Start - reservation start time
    * ReqID - ID from request table for the reservation
    * Computer - hostname of computer
    * VM Host - host computer if computer is a VM
    * Image - name of image reserved
    * Owner - username of user who made the reservation
    Management Node - hostname of management node processing reservation
