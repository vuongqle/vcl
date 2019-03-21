---
title: Configuring Time Sources
last_updated: March 19, 2019
permalink: Configuring-Time-Sources.html
---

The time source variable is configured on the Site Configuration tool.

<img src="images/Screen Shot 2017-03-15 at 3.42.29 PM.png" width="500" border="1">


Enter a list of known time servers in the **Time Servers:** field on the Site Configuration.

* Used to set the time servers on provisioned machines at creation time.
* Supports a comma delimited list
* Recommended to have more than 1 time source

The default VCL install will have preconfigured values set to use public time servers, there is however no guarantee this will work in your environment.
