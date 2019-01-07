---
title: KVM Configuration
last_updated: Jan 07, 2019
sidebar: mydoc_sidebar
permalink: KVM-Configuration.html
---

### Install Packages

The following commands will install the Linux packages required for VCL to manage a KVM host:

    yum install libvirt virt-manager dejavu-lgc-sans-fonts bridge-utils libguestfs-tools -y
    /sbin/chkconfig libvirtd on

    echo "Starting the libvirtd service..."
    /sbin/chkconfig libvirtd on
    /sbin/service libvirtd start


The virt-manager package is optional.  It is a graphical utility which is used to manage KVM and other hypervisors controlled via libvirt.  The dejavu-lgc-sans-fonts package is usually necessary in order for virt-manager to render fonts correctly.


### Configure Networking

The following commands will configure networking to allow KVM guests to communicate.  It configures a bridge named br0 on eth0, and br1 on eth1.  When configured this way, the network names in the VM host profile should be set to br0 and br1.

    echo "Stopping the NetworkManager service..."
    chkconfig NetworkManager off 2>/dev/null
    service NetworkManager stop 2>/dev/null
    yum erase NetworkManager -y

    cat > /etc/sysconfig/network-scripts/ifcfg-eth0 <<EOF
    DEVICE=eth0
    ONBOOT=yes
    BRIDGE=br0
    NM_CONTROLLED=no
    EOF
    echo "Configured ifcfg-eth0:"
    cat /etc/sysconfig/network-scripts/ifcfg-eth0

    cat > /etc/sysconfig/network-scripts/ifcfg-br0 <<EOF
    DEVICE=br0
    TYPE=Bridge
    BOOTPROTO=dhcp
    ONBOOT=yes
    DELAY=0
    NM_CONTROLLED=no
    EOF
    echo "Configured ifcfg-br0:"
    cat /etc/sysconfig/network-scripts/ifcfg-br0

    cat > /etc/sysconfig/network-scripts/ifcfg-eth1 <<EOF
    DEVICE=eth1
    ONBOOT=yes
    BRIDGE=br1
    NM_CONTROLLED=no
    EOF
    echo "Configured ifcfg-eth1:"
    cat /etc/sysconfig/network-scripts/ifcfg-eth1

    cat > /etc/sysconfig/network-scripts/ifcfg-br1 <<EOF
    DEVICE=br1
    TYPE=Bridge
    BOOTPROTO=dhcp
    ONBOOT=yes
    DELAY=0
    NM_CONTROLLED=no
    EOF
    echo "Configured ifcfg-br1:"
    cat /etc/sysconfig/network-scripts/ifcfg-br1

    echo "Configuring eth0 bridge..."
    ifdown br0 2>/dev/null
    brctl delbr br0 2>/dev/null
    brctl addbr br0
    brctl addif br0 eth0

    echo "Configuring eth1 bridge..."
    ifdown br1 2>/dev/null
    brctl delbr br1 2>/dev/null
    brctl addbr br1
    brctl addif br1 eth1

    /sbin/chkconfig network on
    /sbin/service network restart

### Add a Network Storage Pool

The following commands will add an NFS storage pool named images to the KVM host.  The /images directory is exported via NFS from host 10.10.10.1.  This directory is mounted as /mnt/kvm1 on the KVM host.  An entry is added to /etc/fstab to ensure the directory is mounted if the KVM host is rebooted.

### Add a Local Storage Pool

The following commands will define a storage pool named local-vms pointing to the /vms directory on the local disk:

    echo "Adding the local-vms pool..."
    virsh pool-destroy local-vms 2>/dev/null
    virsh pool-undefine local-vms 2>/dev/null
    mkdir /vms 2>/dev/null
    chmod -R 755 /vms
    virsh pool-define-as --name local-vms --type dir --target /vms
    virsh pool-autostart --pool local-vms
    virsh pool-start local-vms
