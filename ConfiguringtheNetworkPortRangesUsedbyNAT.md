---
title: Configuring the Network Port Ranges Used by NAT
last_updated: Feb 14, 2019
permalink: Configuring-the-Network-Port-Ranges-Used-by-NAT.html
---

VCL can be configured to use any port range on NAT hosts as the set of ports that users connect to on that host that are then forwarded to the VCL nodes. This is a **global** setting for all NAT hosts and cannot be set individually for specific NAT hosts. The range is set under the **Site Configuration** section of the site in the box labeled **NAT Port Ranges**. TCP/UDP is not specified. Ranges should be listed one per line. The lowest value should not be less than 1024 and the maximum value is 65535. The default range for VCL is 10000-60000.

<img src="images/image2017-5-5 10_50_51.png" width="500" border="1">
