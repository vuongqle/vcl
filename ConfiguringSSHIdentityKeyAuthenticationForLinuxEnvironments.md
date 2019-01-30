---
title: Configuring SSH Identity Key Authentication For Linux Environments
last_updated: Jan 28, 2018
sidebar: mydoc_sidebar
permalink: Configuring-SSH-Identity-Key-Authentication-For-Linux-Environments.html
---

VCL provides the ability for end users to authenticate to Linux environments using SSH identity keys. The user must first create an SSH key pair (private and public) before proceeding with the steps below. Search online for a tutorial on creating and using SSH key pairs, here are a few.

[PuTTygen Instructions](http://the.earth.li/~sgtatham/putty/0.63/htmldoc/Chapter8.html#pubkey-puttygen)

[ssh-keygen nstructions from Oracle](http://docs.oracle.com/cd/E19253-01/816-4557/sshuser-33/index.html)

Once SSH key pairs are created, perform the following steps to enable this feature for your VCL account.

1. Go to your VCL User Preferences
2. Select General Preferences
3. Click the Enabled radio button under Use public key authentication for SSH logins
4. Paste the contents of your public key file in the Public keys box

    <img src="images/sshkeys.png" width="400" border="1">
5. Click the Submit General Preferences button.

On your next VCL reservation, your public key will be inserted onto the assigned machine.

***NOTE: This change will not be applied to existing reservations.***

To test this change, make a new reservation. When connecting to your reservation, specify the private key that matches the public key you used in the previous steps when you attempt to make an SSH connection with the  VCL image.

If you are using a Linux or Mac machine to connect with the VCL image  you can use the following SSH syntax in a terminal window.

          ssh -i <path_to_private_ssh_key> <username>@<ipaddress>

Replace <path_to_private_ssh_key> with the actual directory path to  your private ssh key (e.g. .ssh/id_rsa). Replace <username> with  your actual username. Replace <ipaddress> with the IP address of  the VCL image listed in the Current reservation page.

If you are using a local Windows machine, you'll probably be using Putty for  your SSH connection. Make sure you specify the "private key file for  authentication" in the "Options controlling SSH authentication"  section of Putty. You can find this section by navigating through the  options on the left side of Putty...

  Connection >> SSH >> Auth

  **WARNING:** The ssh identity key will not work for images where your home directory  resides on a network file system that cannot be accessed until you  authenticate to the remote computer, such as OpenAFS.
