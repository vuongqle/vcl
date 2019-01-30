---
title: How to Provision Linux and Unix Lab Computers
last_updated: Jan 25, 2018
sidebar: mydoc_sidebar
permalink: How-to-Provision-Linux-and-Unix-Lab-Computers.md
---

The Lab.pm provisioning module is used to broker access to standalone pre-installed Linux or Solaris machines. These machines could be in an existing walk-in computer lab or racked in a server room.

There are four main parts needed to setup a standalone machine to use with the Lab.pm module.

1. a non-root account called vclstaff on the target machines
2. ssh idenitity key for vclstaff account, this key is used by the vcld process on the management node
3. ssh service running on port 24 of the target machines
4. vclclientd running on the target machines, vclclientd in the bin directory of the vcld release

For distribution to a large set of machines, an rpm or package could be created to distribute vclclientd and related files.

### How it Works

The Lab.pm module confirms an assigned node or lab machine is accessible using the ssh identity key on port 24. If this succeeds, then a small configuration file with the state, user's id and the users' remote IP address is sent to the node along with a flag to trigger the vclclientd process to either open or close the remote access port. Currently this module only supports Linux and Solaris lab machines.

### How to setup:

All commands are run as root.

1. Create the non-root vclstaff account on target machine

On Linux;

        useradd -d /home/vclstaff -m vclstaff

2. Generate ssh identity keys for vclstaff account. Do not enter a passphrase for the key, just hit enter when prompted.

        su - vclstaff
        ssh-keygen -t rsa
        Generating public/private rsa key pair.
        Enter file in which to save the key (/home/vclstaff/.ssh/id_rsa):
        Created directory '/home/vclstaff/.ssh'.
        Enter passphrase (empty for no passphrase):
        Enter same passphrase again:
        Your identification has been saved in /home/vclstaff/.ssh/id_rsa.
        Your public key has been saved in /home/vclstaff/.ssh/id_rsa.pub.
        The key fingerprint is:

At this point we have created a private key /home/vclstaff/.ssh/id_rsa and the public key /home/vclstaff/.ssh/id_rsa.pub.

Copy the public key to /home/vclstaff/.ssh/authorized_keys file

        cat /home/vclstaff/.ssh/id_rsa.pub > /home/vclstaff/.ssh/authorized_keys

Copy the private key to the management node. This can be stored in /etc/vcl/lab.key. This private key is used by vcld to remotely log into the the lab machine.

        Edit /etc/vcld.conf
        Set the variables IDENTITY_linux_lab and IDENTITY_solaris_lab to use this new key.
        It should look like:
        IDENTITY_solaris_lab=/etc/vcl/lab.key
        IDENTITY_linux_lab=/etc/vcl/lab.key

Test out the newly created key from the vcl management node:

        ssh -i /etc/vcl/lab.key vclstaff@target_lab_machine

3. Set ssh server on target machine to listen on port 24. Edit /etc/ssh/sshd_config on target lab machine(s).

        echo "Port 24" >> /etc/ssh/sshd_config

For advanced ssh configurations one may need to also add vclstaff to the AllowUsers directive or some other group which would work with ones existing campus ssh login restrictions, if any.

        Restart sshd: /etc/init.d/sshd restart

Retest to make sure sshd is accessible on port 24

        ssh -p24 -i /etc/vcl/lab.key vclstaff@target_lab_machine
