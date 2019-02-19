---
title: Computers
last_updated: Feb 15, 2019
permalink: Computers.html
---

## How are computers used in VCL?
In VCL, the terms computer, machine, and node are used interchangeably. There are several ways computers are used. Computers are used for parts of the VCL system itself (Web Frontend, Database, Management Nodes), computers that host virtual machines, (optionally) computers that perform Network Address Translation (NAT), and computers (both virtual and bare metal) that are deployed and made available to users via reservations.

To setup a VCL system, a large number of computers are not necessary. A single, moderately powerful computer can be used as a virtual host to run one virtual machine that will be used to run all of the VCL system components with additional virtual machines being deployed on the same host to be utilized by end users.

## Administering Computers

Computers are managed through the Manage Computers section of the site.

From the **Manage** menu - **Select Manage Computers**

<img src="images/Screen Shot 2017-02-20 at 12.37.25 PM.png" width="350" border="2">

 Select **Edit Computer Profiles**, and click the **submit** button.

<img src="images/Screen Shot 2017-02-03 at 2.55.27 PM.png" width="450" border="1">

The **Computer Profiles** page will open where you can add new computers or manage any existing computers previously created.

Click the **Add New Computer** button.

<img src="images/Screen Shot 2017-02-03 at 2.59.50 PM.png" width="450" border="1">

This will open a dialog window to enter information for new computers. There two options for adding computers, either adding a **Single Computer** or **Multiple Computers at a time.** The two screen shots below show the differences.

## Single Computer

<img src="images/Screen Shot 2017-02-20 at 1.12.35 PM.png" width="450" border="1">

### Description of fields

**Name** -  Hostname of the computer or computers. Used for internal VCL management of computers and does not need to match public hostnames if applicable

**Owner** - The owner of the compute resource

**Type** - The type of computer that relates to the provisioning methods. Currently there are 3 types of computers
* Bare Metal - Uses xCAT to provision bare metal based reservations
* Lab - Uses the Lab provisioning module to broker access only to for standalone Linux lab machines
* Virtual Machine - VMware and KVM based provisioning, the typical use case for VCL computers

**Public IP Address** - Public IP address of computer, is a required field, if using DHCP for public network this address can be arbitrary. VCL will update the computer record with the assigned DHCP address

**Private IP Address** - Private IP address of computer, is a required field. Used on the VCL Private network to communicate with computer for provisioning reservations

**Public MAC Address** - MAC address for Public network adapter, is a required field. VCL provisioning assigns this MAC address to public network adapter when creating related virtual machine files in order to receive IP address for Public network.

**Private MAC Address** - MAC address for Private network adapter, is a required field. VCL provisioning assigns this MAC address to private network adapter when creating related virtual machine files in order to receive IP address for Private network.

**Provisioning Engine** - Defines which provisioning method (or module) to use for this computer. Please refer to Provisioning Modules for information on different types

**State** - Initial state of the computer

**Platform** - Hardware architecture, i386, i386_lab (used for the Lab provisioning module)  

**Schedule** - Times a computer is available for use. For cases were certain computers should only be available during certain hours. i.e. work hours  The default Schedule is VCL 24x7

**RAM (MB)** - Assigned memory of the computer, is a required field.
* If Type is Virtual Machine, value is assigned to the computer when provisioned. Value should be high enough to cover most of the VCL image memory requirements.
* If Bare Metal this is the physical amount of memory available to the server.

**No. Cores** - Assigned cores (or vCPUs), is a required field.
* If Type is Virtual Machine, value is assigned to the computer when provisioned. Value should be high enough to cover the VCL image core requirements.
* If Bare Metal this is the physical number of cores available to the server.
Processor Speed (MHz): Processor Speed of cores available to the computer

**Network** - Network speed available to the vm.  

**Predictive Loading Module** - Used to determine the best action to take on a computer after a user's reservation. There are three values:
1. Reload with last image - Simply reload the last image that was used
2. Reload image based on recent user demand - Choose images to reload based on the overall reservation demand and already preloaded computers
3. Unload/power off after reservation - Simply turn off or decommission the computer after the reservation. This prevents too many hot-standby vm's running.

**Connect Using Nat:** Optional

Nat Host:

Use as NAT Host:

NAT Public IP Address:

NAT Internal IP Address:  

## Multiple Computers

<img src="images/Screen Shot 2017-02-20 at 1.07.51 PM.png" width="450" border="1">

The primary differences are the **Name**, **Star**, **End**, **Start** and **End IP Address** fields.

**Name** -  Hostnames of computers

The hostnames of the computers that will be added. Note these hostnames are for internal use only and do not need to exist in public DNS or domain name services. The hostnames will be generated from the Name field and they can only differ by the value of a number in the first part of the name. Place a '%' character in the name field where the number will be. Then use the **Start** and **End** fields for the firs and last numbers to be used in the hostnames.

**Start**: Starting number for adding sequentially named virtual machines. i.e **vm1** through vm10

**End**: Ending number for adding sequentially named virtual machines. i.e vm1 through **vm10**

**Start Public IP Address**: The first IPv4 address in the sequence for the Public facing network, i.e. 1.1.1.1

**End Public IP Address**: The last IPv4 address in the sequence for the Public facing network, i.e.  1.1.1.10

**Start Private IP Address**: The first IPv4 address in the sequence for the Private facing network, i.e. 10.10.1.1

**End Private IP Address**: The last IPv4 address in the sequence for the Private facing network, i.e.  1.1.1.10

**Start MAC Address**: The first MAC address to be assigned to first computer in the sequence.
* Virtual Machines use a standard MAC address prefix, i.e.**00:50:56**
* Two MAC addresses are created in sequential order for each computer, one for each **Public** and **Private** interface
