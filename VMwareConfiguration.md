---
title: VMware Configuration
last_updated: Jan 07, 2019
sidebar: mydoc_sidebar
permalink: VMware-Configuration.html
---

## Terminology

**VM Host**

* A VM host is a physical computer running a VMware hypervisor
* A VCL computer entry must be added for each VM host (Manage Computers > Edit Computer Information)
* After the computer has been added to VCL, it is designated as a VM host by changing the computer state to vmhostinuse (Manage Computers > Computer Utilities)

**VM**
* A VM is a virtual machine managed by VCL
* A computer entry must be added to VCL for each VM (Manage Computers > Edit Computer Information)
* Each VM must be assigned to a VM host (Virtual Hosts > VM Hosts tab > Configure Host)
* VMs do not need to be created manually in VMware, VCL automatically creates and deletes VMs

**VM Host Profile**
* A VM host profile contains several parameters which describe how a VM host is configured so that VCL knows how to manage it
* Each VM host is assigned a VM host profile
* A VM host profile may be assigned to multiple VM hosts if they are configured identically
* VM host profiles may be added or modified via Virtual Hosts > VM Host Profiles tab

**VMware Products Supported**
* VMware Server 2.x
* VMware ESX 3.5 - 4.x
* VMware ESXi 4.x
* VMware ESXi 5.x

## VM Host Management Options

 The VCL management node must be able to control the VM host and the VMs running on it.  VMware provides several different ways of doing this.  VCL currently supports the following methods for remote VM host management:

* VMware vSphere SDK
* Use SSH to execute commands directly on the VM (not officially supported by VMware)

The vSphere SDK can only be used if management is not restricted due to the VMware license key installed on the host.  This mainly affects hosts running the free version of ESXi.  Remote management using any of the methods supported by VMware is restricted once a free license key is entered.

If remote management is restricted, the VM host can be managed if SSH is enabled on it.  VCL will execute vim-cmd and other commands on the VM host via SSH.

### How to enable SSH on the VM host:

VMware Server 2.x
* Enable the SSH daemon and configure identity key authentication according to the underlying VM host OS


ESX/ESXi 3.5 & 4.0
* Connect to the console of the ESX/ESXi host
* Press ALT-F1 - you should see a black screen with the VMware product name at the top
* Type the word unsupported and press Enter (you won't see the letters appear as you type them)
* You should see a password prompt, type in the root password and press Enter
* Edit the file: vi /etc/inetd.conf
* Uncomment the first line beginning with #ssh by deleting the # character
* Save the file - press Esc and then :wq
* Kill the inetd process
    * Determine the PID of the inetd process: *ps grep inetd.* You should see a line that looks like: 5065 5065 busybox inetd
    * Kill the process (enter the PID from the output of the previous command): kill -HUP 5065

ESXi 4.1

Beginning with ESXi 4.1, SSH can be enabled using the vSphere Client:
* Select the ESXi host
* Select the Configuration tab
* Select Security Profile under Software
* Click Properties
* Select Remote Tech Support (SSH)
* Click Options
* Select Start automatically
* Click Start
* Click OK

ESX 5.0

In the case of ESX 5.0:
* Select the ESXi host
* Select the Configuration tab
* Select Security Profile under Software
* Click Properties
* Select SSH Server
* Click Options
* Confirm that Start automatically is selected
* Click OK

How to configure ESX/ESXi to use SSH identity key authentication:

SSH identity key authentication must be configured if SSH is used to manage the VM host.

* Create an SSH key pair on the management node (or use a key you previously created):

        ssh-keygen -t rsa -f /etc/vcl/vcl.key -N '' -b 1024 -C 'VCL root account'

* Log into the ESX host via SSH (password authentication should work) and create the directory:

        ssh <ESXi host> 'mkdir /.ssh'

* Copy the public key to the ESXi host:
ESXi 4.x:

        scp /etc/vcl/vcl.key.pub <ESXi host>:/.ssh/authorized_keys

* ESXi 5.x:

        scp /etc/vcl/vcl.key.pub <ESXi host>:/etc/ssh/keys-root/authorized_keys

* Test making an SSH connection using the key:

        ssh -i /etc/vcl/vcl.key <ESXi host>

***IMPORTANT:*** Under ESXi 4.x, the authorized_keys file is erased when the ESXi VM host is rebooted. Complete the following steps to make the authorized_keys file persistent:

*Note: VCL will perform these steps automatically when the 1st reservation assigned to the host is processed.*

* Create a compressed tarball file containing the /.ssh directory:

        tar -C / -czf bootbank/vcl.tgz .ssh

* Edit the /bootbank/boot.cfg file and append ' --- vcl.tgz' to modules line as shown in the following example:

        kernel=b.z
        kernelopt=
        modules=k.z — s.z — c.z — oem.tgz — license.tgz — m.z — state.tgz — vcl.tgz
        build=4.1.0-260247
        updated=2
        bootstate=0

Optionally you can run the following two commands:

        tar -C / -czf vcl.tgz .ssh
        BootModuleConfig.sh --add=vcl.tgz --verbose

## VM Host Profile Parameters

[Here](VM-Host-Profiles.html) is a page on the VM Host Profile Parameters so VCL know hows to manage the host profile.