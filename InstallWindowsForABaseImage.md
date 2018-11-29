---
title: Install Windows for a Base Image
last_updated: Nov 15, 2018
sidebar: mydoc_sidebar
permalink: Install-Windows-for-a-Base-Image.html
---

This page describes how to mount the Windows installation media and install Windows for a base image.

### Mount the Installation Media

The Windows installation media needs to be mounted as a drive on the computer. The method to do this varies widely based on the provisioning engine being used and resources available. The following lists some ways to mount the installation media:

#### VMware - Configure the VM to mount the ISO image as a CD-ROM drive

Note: these instructions assume a VM has already been created

1. Copy the Windows installation ISO file to the VMware host server
2. Add a CD-ROM drive which mounts the Windows installation ISO image by editing the virtual machine settings:
    1. Connection: Use ISO image:
    2. Browse to path of Windows installation ISO image
    3. Save the VM configuration

#### xCAT using IBM Advanced Management Module

1. Copy the Windows installation ISO file to the management node
2. Determine the IP address or hostname of the IBM Advanced Management Module (AMM) for the BladeCenter chassis which contains the blade you are installing
3. Open a web browser and enter the AMM's address
4. Log in to the AMM
5. Select Inactive session timeout value: no timeout
6. Click Start New Session
7. Click Remote Control
8. Click Start Remote Control
9. Set the Media Tray and KVM dropdown menus to the blade you are installing
10. Click Select Image and click the arrow button to the right of it
11. Navigate to the Windows installation ISO file which was saved to the management node and click Open
12. Click Mount All

### Boot to the Windows Installation CD or DVD
1. Power on the computer
2. Press the key to display the boot menu as soon as the computer's POST screen is displayed (usually F12 for bare metal blades or ESC for VMware)
3. Boot from the CD-ROM drive
4. Press a key to boot from the CD (this may be displayed at bottom of screen as soon as the computer begins to boot)


### Install Windows

The Windows installation sequence varies by version. The next 2 sections describe the recommended answers for Windows XP and Windows 7.

#### Windows XP Installation

1. Press Enter to setup up Windows XP now
2. Press F8 to agree to the license agreement
3. Configure the Windows partition
    1. Press Enter to set up Windows XP on the selected item (should be called "Unpartitioned space")
    2. Format the partition using the NTFS file system (Quick)
4. Region and Language Options - click Next
5. Name: VCL
6. Organization: Apache.org
7. Enter your Windows XP product key
8. Computer name: (doesn't matter)
9. Administrator password: (doesn't matter, but it's recommended that password should match the WINDOWS_ROOT_PASSWORD setting in /etc/vcl/vcld.conf)
10. Select the timezone
11. Networking settings: Typical
12. Member of a domain: No, leave default workgroup settings
13. Automatic updates: Not right now
14. Connect to Internet: Skip
15. Register: no
16. User name: root

    *Windows XP setup should finish and the root account created during installation should automatically log on*

17.  Once the desktop appears, set root's password via the Windows GUI or by executing the following command from a command prompt:

            net user root <password>

### Windows 7 Installation

1. Enter the regional information:
    1. Language to install: English
    2. Time and currency format: English (United States)
    3. Keyboard or input method: US
2. Click Next
3. Click Install now

    *Setup is starting...*

4. Click the checkbox next to "I accept the license terms"
5. Click Next
6. Click Custom (advanced)
7. On the "Where do you want to install Windows?" page, delete all existing partitions and create a new partition using all of the available space:
    1. Click Drive options (advanced)
    2. Click Delete, then click OK to confirm
    3. Click New
    4. Click Apply (the size should be set to the maximum amount available

        *To ensure that all Windows features work correctly, Windows might create additional partitions for system files.*

    5. Click OK
8. Click Next'

    *Installing Windows...
    Windows restarts
    Starting Windows
    Setup is updating registry settings*

9. A screen titled "Set Up Windows" appears:
    * Type a user name: root
    * Type a computer name: it's best to name the computer after the OS (Example: win7sp1-ent)
10. Enter a password, password hint, and click Next
11. Help protect your computer and improve Windows automatically: Ask me later
12. Select a time zone, set the correct time, and click Next

    *Windows is finalizing your settings
    Preparing your desktop
    Desktop appears*

13. if asked to set a network location, choose Work network.

#### Windows Server 2008

1. Select the language and click Next
2. Click Install Now
3. Select the version of Windows you want to install from the list and click Next. (Windows Server 2008 R2 Datacenter (Full Installation) was selected when creating these instructions.)
4. Click the checkbox next to I accept the license terms and click Next
5. Click Custom (advanced)
6. Configure the disk partitions and click Next.  Unless you have reason not to, it's best to delete all existing partitions and then select Unallocated Space. This causes the disk to be repartitioned using all of the available space.
7. Click OK and set a password for the Administrator account, click OK

The root user account is not created during the installation of Windows Server 2008. It must be created after Windows is installed. Do this using the GUI or run the following commands in a command window:


1. Click Start > Administrative Tools > Server Manager
2. Open Configuration > Local Users and Groups > Users
3. Open the Action menu > New User
    1. User name: root
    2. Enter a password twice
    3. User must change password at next logon: no
    4. Password never expires: yes
    5. Click Create
    6. Click Close
4. Double-click the root user
5. Select the Member Of tab
6. Click Add
    1. Enter the object names to select: Administrators
7. Click OK twice

The Disk Cleanup utility (cleanmgr.exe) is not available on Windows Server 2008 unless the the Desktop Experience feature is installed. VCL runs cleanmgr.exe before an image is captured to reduce the amount of space the image consumes. Image captures will not fail if cleanmgr.exe is not installed but it is recommended to install the Desktop Experience feature so that it is available:

1. Open the Control Panel
2. Click Turn Windows features on or off
3. Click Features
4. Click Add Features
5. Click the checkbox next to Desktop Experience
6. Click Add Required Features
7. Click Next
8. Click Install
9. Click Close
10. Click No to not reboot the computer
11. Restart the computer after the installation is complete


### Optional Windows Configuration Tasks

#### Enable Remote Desktop

The remaining configuration tasks will be easier if you are able to connect to the Windows computer via RDP rather than using the VMware or BladeCenter Management Module console. This step is optional. The VCL image capture process will configure RDP on the Windows computer during image capture and load processes.

##### Windows XP & Windows Server 2003:

1. Open Control Panel > System > Remote tab
2. Click the checkbox next to Allow users to connect remotely to this computer
3. Click OK

##### Windows Vista, Windows 7, & Windows Server 2008:

1. Open Control Panel > System and Security > System
2. Click Remote settings
3. Select Allow connections from computers running any version of Remote Desktop (less secure)
4. Click OK

Use an RDP client to connect to the Windows computer using either its public or private IP address as appropriate. If the public address is not available for some reason, you can attempt to connect to the private IP address by installing rdesktop on the management node:

        yum install rdesktop -y

        rdesktop -g 1024x768 <IP address> &

#### Disable Internet Explorer Enhanced Security Configuration

Internet Explorer Enhanced Security Configuration (IE ESC) prevents you from being able to access websites unless you add them to the Trusted sites zone.

1. Open Administrative Tools > Server Manager
2. Click Configure IE ESC (on the right side under Security Information)
3. Select Off for Administrators and Users
4. Click OK

#### Set the Computer Name

The computer may have been assigned a random computer name. This name will be saved in the captured image. If Sysprep is disabled, this computer name will also be assigned to other computers loaded with the image. It's helpful to name the computer something descriptive of the image so that you can tell what the image is when you connect to it via SSH.

1. Open the Control Panel
2. Click System and Security > Set the name of this computer
3. Click Change settings
4. Click Change
    1. Enter a Computer name and Workgroup
    2. Click OK 3 times
5. Click Close
6. Click Restart Later

#### Disable User Account Control

User Account Control (UAC) is the mechanism that causes may of the pop-up windows to appear when you attempt to run programs on Windows 7 and Windows Server 2008. VCL will disable it when the image is captured but you can disable it while configuring the base image to make things a little easier.

1. Open the Control Panel
2. Click System and Security > Change User Account Control settings (Under Action Center)
3. Move the slider to the bottom: Never notify
4. Click OK
5. Reboot the computer

#### Configure Windows Boot Options

It can be helpful to configure the Windows boot options as follows in order to be able to troubleshoot boot problems.


1. Run msconfig.exe
2. Select the Boot tab
3. Click the checkboxes next to:
    1. No GUI boot - Does not display the Windows Welcome screen when starting
    2. Boot log - Stores all information from the startup process in the file %SystemRoot%Ntbtlog.txt
    3. OS boot information - Shows driver names as drivers are being loaded during the startup process
4. Make all boot settings permanent
5. Click OK
6. Click Yes
7. Restart the computer

### Verify Network Connectivity

The computer must be able to connect to the public and private networks.

1. If DHCP is not being used, configure the IP addresses manually
2. Verify that the computer has IP addresses for both the public and private network adapters:
    1. Open a command prompt:

            cmd.exe

    2. Check the network configuration:

            ipconfig /all

    3. Verify Internet access by opening Internet Explorer and browsing to a public website

Some Windows versions (especially Windows Vista) have trouble properly routing outward network traffic if there are multiple network interfaces. If you can not get to the Internet, set the private network interface to ignore default routes which causes all outward traffic not destined for the private network to be sent through the public interface:

1. Open a command prompt (this must be done as Administrator under Windows 6.x):
Start > All Programs > Accessories > right-click Command prompt > Run as Administrator
2. Determine the name of the private interface from the ipconfig output
(should be either "Local Area Connection" or "Local Area Connection 2")
3. Execute the command using the private interface name from step 2:

        netsh.exe interface ip set interface "Local Area Connection" ignoredefaultroutes=enabled

    * The command should display Ok.
4. Attempt to access the Internet again

### Install Windows Updates

1. Open Internet Explorer
2. Run Windows Update
    1. Install all recommended updates, reboot if necessary
3. Run Windows Update again to check for additional updates

### Install Drivers

Open up the Device Manager: Control Panel > System > Hardware tab > Device Manager

If any devices are unknown or missing drivers, you will need to locate and download the appropriate driver and install it.

Save a copy of the drivers you had to install in the appropriate Drivers directory on the management node:

        /usr/local/vcl/tools/Windows.../Drivers

There are multiple Windows... directories under /usr/local/vcl/tools. The names create a hierarchy so that files which can be used by multiple versions of Windows only need to be stored in a single location on the management node.  There are 3 levels that make up the hierarchy:

1. The directory named Windows should contain files that work on all versions of Windows:

        /usr/local/vcl/tools/Windows/Drivers

2. The directories named Windows_Version_x should contain files that only work on a particular major version of Windows.

    *The Windows version number can be obtained by executing ver from a command prompt*

    * Windows_Version_5 should contain files that work on versions of Windows numbered 5.x (Windows XP and Windows Server 2003).

            /usr/local/vcl/tools/Windows_Version_5/Drivers

    * Windows_Version_6 should contain files that work on versions of Windows numbered 6.x (Windows Vista, Windows Server 2008, and Windows 7).

            /usr/local/vcl/tools/Windows_Version_5/Drivers

3. The directories named after a specific version (Windows_XP, Windows_Server_2008, etc.) should contain files that only work on that version.  For example, if a driver only works under XP save it under:

        /usr/local/vcl/tools/Windows_XP/Drivers

During the image capture process, the Windows* directories that pertain to the OS being captured are copied to C:\cygwin\home\root\VCL on the Windows computer.  Each Windows* directory is overlayed into the same VCL directory.  They are copied in the order listed above, from most general to most specific. For example, if a Windows Server 2008 image is being captured the directories copied are:

\v
