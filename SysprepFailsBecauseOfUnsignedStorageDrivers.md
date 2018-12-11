---
title: Sysprep Fails Because of Unsigned Storage Drivers
last_updated: Nov 15, 2018
sidebar: mydoc_sidebar
permalink: Sysprep-Fails-Because-Of-Unsigned-Storage-Drivers.html
---


Sysprep may fail if mass storage drivers are not signed.  This problem occurs even if the sysprep.inf file is configured with DriverSigningPolicy=Ignore.  This is a known problem with some versions of LSI SAS drivers (1.30.02.00) used on IBM blades.  The driver available from LSI's website is not correctly signed.  The same version is available from IBM's website and is correctly signed.  It can be downloaded from:

[(Critical update) IBM and LSI Basic or Integrated RAID SAS driver v1.30.02.00 for Microsoft Windows Server 2008 and Windows Server 2003 - IBM BladeCenter and System x](https://www.ibm.com/it-infrastructure)

### How to Tell if this Problem Occurred

Open C:\Windows\setupapi.log.  Look for lines containing "An unsigned or incorrectly signed file":

        [2009/10/21 10:52:43 1912.7 Driver Install]
        #-019 Searching for hardware ID(s): pci\ven_1000&dev_0622
        #-199 Executing "C:\Sysprep\sysprep.exe" with command line: C:/Sysprep/sysprep.exe /quiet /reseal /mini
        #I022 Found "PCI\VEN_1000&DEV_0622" in c:\cygwin\home\root\vcl\drivers\storage\lsi-sas\symmpi.inf; Device: "LSI Adapter, 2Gb FC, models 44929, G2 with 929"; Driver: "LSI Adapter, 2Gb FC, models 44929, G2 with 929"; Provider: "LSI Corporation"; Mfg: "LSI Corporation"; Section name: "SYMMPI_Inst".
        #I087 Driver node not trusted, rank changed from 0x00000000 to 0x0000c000.
        #I023 Actual install section: [SYMMPI_Inst]. Rank: 0x0000c000. Effective driver date: 01/30/2009.
        #-166 Device install function: DIF_SELECTBESTCOMPATDRV.
        #I063 Selected driver installs from section [SYMMPI_Inst] in "c:\cygwin\home\root\vcl\drivers\storage\lsi-sas\symmpi.inf".
        #I320 Class GUID of device remains: {4D36E97B-E325-11CE-BFC1-08002BE10318}
        #I060 Set selected driver.
        #I058 Selected best compatible driver.
        #-124 Doing copy-only install of "ROOT\SYSPREP_TEMPORARY\0000".
        #E358 An unsigned or incorrectly signed file "c:\cygwin\home\root\vcl\drivers\storage\lsi-sas\mpixp32.cat" for driver "LSI Adapter, 2Gb FC, models 44929, G2 with 929" blocked (server install). Error 0x800b0003: The form specified for the subject is not one supported or known by the specified trust provider.
        #W187 Install failed, attempting to restore original files.


### How to Resolve this Problem
The preferred method is to obtain a signed driver.  If a signed driver cannot be located, try configuring the local computer policy to allow unsigned drivers:

* Run: gpedit.msc
* Navigate to: Computer Configuration > Windows Settings > Security Options
* Edit: Devices: Unsigned driver installation behavior = Silently succeed
* Run: gpupdate /force

Restart the image capture.
