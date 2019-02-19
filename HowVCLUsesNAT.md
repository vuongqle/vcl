---
title: How VCL Uses NAT
last_updated: Feb 14, 2019
permalink: How-VCL-Uses-NAT.html
---
VCL can make use of Network Address Translation (NAT) to allow users to connect to reserved systems via a single public IP address. This is done by configuring a node to act as a NAT host. VCL then configures networking rules on the NAT host so that users are given a port or set of ports to connect to on the NAT host that are then forwarded to the VCL node deployed for a given user. VCL dynamically configures the networking rules as nodes are provisioned and deprovisioned.


A NAT host must have at least 2 network adapters - one with an address on a network that each of the provisioned nodes will have an address, and another adapter with a publicly accessible address.


The NAT host must be configured to allow root to ssh to it from the management node. It is advisable to have this ssh process set up to listen on a 3rd network adapter configured to be on a private or control network.


NAT hosts are added to VCL under Manage Computers. The checkbox for **Use as NAT Host** must be selected and **NAT Public IP Address** and **NAT Internal IP Address** must be filled in with the IP addresses from the appropriate networks.
