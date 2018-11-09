---
title: Computer Predictive Loading Modules and Power Off
last_updated: Nov 9, 2018
sidebar: mydoc_sidebar
permalink: computer-predictive-loading-modules-power-off.html
---

At the end of a reservation VCL must do something with the node that was just used. This action is controlled by the Predictive Loading Modules. Previous to version 2.4, these were set as an attribute of Management Nodes. In the 2.4 release, they were moved to be an attribute of Computers. As of version 2.4, there are 3 options to choose from:

* Reload with last image - deploy the same image as just used
* Reload image based on recent user demand - deploy an image selected by analyzing historical usage
* Unload/power off after reservation - power off the node and, for VMs, unregister it

**Reload with last image** is pretty straightforward. It does a good job of handling spikes for specific images because, the more the image is used concurrently, the more that image is reloaded as those reservations end.

**Reload image based on recent user demand** analyzes reservation log data to determine the most popular set of images over a time frame. The time frame used varies depending on the percentage of nodes being used. The higher the percentage of nodes in use, the shorter the time frame. Then, it selects the most popular image during that time frame that is not already preloaded on at least two nodes. This module does a good job providing a variety of preloaded images. However, it does not handle spikes for specific images well.

**Unload/power off after reservation** is different in that it doesn't actually load the node with anything. Instead, it powers off bare metal nodes and shuts down and unregisters virtual nodes. This is useful to reduce load on hypervisors and to save power. However, it requires a full load time the next time the node is used.

**Exceptions**

There are 2 exceptions to the predictive loading modules:

* upcoming reservation
* admin selected reload image

If there is an upcoming reservation for the node within the next 50 minutes, the image from that reservation will be loaded on the node.

If, during the previous reservation, an administrator selected to reload the node with a certain image from the Manage Computers section of the site, it will be loaded with that image.
