---
title: Install and Configure Cygwin SSHD
last_updated: Nov 30, 2018
sidebar: mydoc_sidebar
permalink: Install-and-Configure-Cygwin-SSHD.html
---
The Cygwin SSHD service must be installed on a Windows base image. It allows the management node to login to the computer in order to configure it and to perform periodic checks.

### Install Cygwin

#### Apache VCL current version

Log on to the Windows computer being used for the VCL image as root

    * The scripts included with the Apache VCL source code which configure the Cygwin sshd service will likely fail if you install Cygwin while logged in as a user other than root.

Download the Cygwin installer to the desktop:

    * 32 bit http://cygwin.com/setup-x86.exe

        setup-x86.exe or setup-x86_64.exe

If running a newer version of Windows which includes User Account Control (UAC), be sure to either disable UAC and reboot before installing Cygwin or right-click setup.exe and select Run as Administrator

Configure as follows:
Select Install from Internet
Root Directory: C:\cygwin
Install For: All Users
Local Package Directory: browse to root's desktop
Internet Connection: Direct Connection
Download Site: choose a site close to your location (.edu FTP sites seem the fastest)
If presented with a "Setup Alert - This is the first time you've installed Cygwin 1.7.x" window, click OK
Select Packages (expand the tree and click Skip):
Editors : vim
Net: openssh
Web: wget
Utils: dos2unix
Select required packages: checked
Create icon on Desktop: Yes
Add icon to Start Menu: No
Click Finish
Apache VCL 2.2 and Earlier
Log on to the Windows computer being used for the VCL image as root

The scripts included with the Apache VCL source code which configure the Cygwin sshd service will likely fail if you install Cygwin while logged in as a user other than root.

Download the Cygwin 1.5.x installer to the desktop: http://cygwin.com/setup-legacy.exe

WARNING: Do not download and install Cygwin 1.7 unless you are running Apache VCL 2.2.1 or later. Changes have been made to Cygwin 1.7 which will cause VCL reservations to fail.

Run the Cygwin installer:

setup-legacy.exe

If running a newer version of Windows which includes User Account Control (UAC), be sure to either disable UAC and reboot before installing Cygwin or right-click setup.exe and select Run as Administrator

Click OK if presented with a legacy version warning
Configure as follows:
Install from Internet
Root Directory: C:\cygwin
Install For: Just Me
Default Text File Type: DOS/text
Local Package Directory: browse to root's Desktop
Internet Connection: Direct Connection
Download Site: choose one (.edu FTP sites seem the fastest)
Select Packages (expand the tree and click Skip):
Editors : vim
Net: openssh
Web: wget
Create icon on Desktop: No
Add icon to Start Menu: No
Verify that Cygwin was successfully installed
There is a bug in the Cygwin 1.5 installer which causes the installation to fail.  You may see a Postinstall script errors panel after the installation has finished.  When Cygwin fails to install properly, most of its executable files are not properly copied to the C:\cygwin\bin directory.
Open Windows Explorer and navigate to C:\cygwin\bin
Check the number of files listed in this directory
There should be many files listed in this directory -- usually over 300.  If you only see a few files then Cygwin did not install properly. For some reason, Cygwin usually installs correctly the 2nd time the the installation is done. Do the following if you are presented with the Postinstall script errors panel at the end of the Cywin installation or if the C:\cygwin\bin directory is incomplete:
Close the Cygwin installer
Delete C:\cygwin
Repeat the installation steps listed above
Verify that Cygwin was installed properly again
Configure the Cygwin Desktop Shortcut
It is useful to have a Cygwin shortcut on root's desktop. The Cygwin installer creates the desktop icon in the shared desktop folder for all users meaning it will appear on the desktop when users make reservations for the image. This is not recommended. Move the shortcut from the shared desktop folder to root's desktop folder:

Newer versions of Windows (Windows 7, Windows Server 2008):
C:\Users\Public\Desktop\Cygwin.lnk > C:\Users\root\Desktop\Cygwin.lnk
Older versions of Windows (Windows XP, Windows Server 2003):
C:\Documents and Settings\All Users\Desktop\Cygwin.lnk > C:\Documents and Settings\root\Desktop\Cygwin.lnk
Delete the Installation Files
After Cywgin installation is complete, delete the installation files from the desktop:

Installer: setup-legacy.exe
Local package directory: C:\ftp%...cygwin...
Configure the Cygwin SSHD Service
Launch the Cygwin shortcut on the desktop

If running a newer version of Windows which includes User Account Control (UAC), be sure to either disable UAC and reboot before launching the Cygwin shortcut or right-click the Cygwin shortcut and select Run as Administrator

Download cygwin-sshd-config.sh using wget to root's Cygwin home directory on the Windows computer:
C:\Cygwin\home\root\cygwin-sshd-config.sh

wget https://raw.githubusercontent.com/apache/vcl/master/managementnode/bin/cygwin-sshd-config.sh

Set the script to be executable:

chmod +x cygwin-sshd-config.sh

Fix Cygwin 1.7 issue:

sed -i -e 's/^ssh-host-config.*/ssh-host-config -y -c "nodosfilewarning ntsec" -w "$PASSWORD"/' cygwin-sshd-config.sh

Run the script and specify the root account password as an argument, enclose the password in single quotes in case special characters are used in the password:

./cygwin-sshd-config.sh 'PASSWORD'

If asked to enter a new user name, enter root
If asked to enter a password, enter the password set for the Windows root user. This is the same password entered as the cygwin-sshd-config.sh argument.
You should see something similar to the following towards then end of the script output:

The CYGWIN sshd service was started successfully.

Configure SSH Identity Key Access from the Management Node
Log in as root on the management node
Download gen-node-key.sh using wget to the management node:

wget http://svn.apache.org/repos/asf/vcl/trunk/managementnode/bin/gen-node-key.sh

Set the script to executable:

chmod +x gen-node-key.sh

Determine the IP address of the Windows computer by running ipconfig
Run the script on the managment node and specify the Windows computer's IP address or hostname as the 1st argument. A second argument specifying the private SSH key path can be specified. If the 2nd argument isn't specified, /etc/vcl/vcl.key will be used.

./gen-node-key.sh 10.10.18.179

Enter the Windows root account password during script execution when asked
Attempt to connect from the management node to the Windows computer via SSH using the command displayed at the end of the gen-node-key.sh output
