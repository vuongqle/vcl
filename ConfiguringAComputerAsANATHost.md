---
title: Configuring a Computer as a NAT Host
last_updated: Feb 14, 2019
permalink: Configuring-a-Computer-as-a-NAT-Host.html
---

[Management Nodes](management-nodes.html) and [Computers](computers.html) can be used as NAT Hosts. Any individual VM can be used as a NAT host provided that it is connected to the appropriate networks. Additionally, VM Hosts that are full Linux systems such as KVM can be used as NAT hosts.

### Configuring a Computer as a NAT Host
Under Manage Computers->Edit Computer Profiles, select Edit for the desired computer. At the bottom of the dialog box that is displayed is a section for configuring it as a NAT host.

<img src="images/image2017-5-5 9_46_13.png" width="500" border="1">

- Check the box for **Use as NAT Host** (note that **Connect Using NAT** and **Use as NAT Host** are mutually exclusive)
- Set **NAT Public IP Address** to the IP address on the computer that is connected to the Internet. This is the address that will be displayed to users to which they will connect.
- Set **NAT Internal IP Address to the IP address** on the computer that is on the same network as the VMs to which it will be providing NAT service.

### Configuring a Management Node as a NAT Host
Under Management Nodes->Edit Management Node Profiles, select Edit for the desired management node. At the bottom of the dialog box that is displayed is a section for configuring it as a NAT host.

<img src="images/image2017-5-5 9_50_48.png" width="500" border="1">

- Check the box for **Use as NAT Host**
- Set **NAT Public IP Address** to the IP address on the management node that is connected to the Internet. This is the address that will be displayed to users to which they will connect.
- Set **NAT Internal IP Address** to the IP address on the management node that is on the same network as the VMs to which it will be providing NAT service.

### Example
This example uses 3 networks. NAT Host can refer to a computer or a management node.

- Private
    - internal, used for management node to provision VMs
        - IP space: 192.168.100.0/22
        - NAT Host IP: 192.168.100.1
- NAT
    - internal, used to provide NAT service
        - IP space: 192.168.200/22
        - NAT Host IP: 192.168.200.1
- Public
    - public connection to the Internet
        - NAT Host IP: 100.100.100.50

In this example, each VM would be entered under the Manage Computers part of the site with an address from the Private network and an address from the NAT network would be entered for the VMs public IP.

*To configure the NAT host, the following 2 values would be used:*
- **NAT Public IP Address**: 100.100.100.50
- **NAT Internal IP Address**: 192.168.200.1
