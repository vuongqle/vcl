---
title: Affiliations
last_updated: Feb 07, 2019
permalink: affiliations.html
---
## What Is an Affiliation and How Is It Used

An affiliation represents the entity or organization that a VCL users belongs to.  Each VCL user belongs to a single affiliation and VCL determines how to authenticate a user to the VCL website based on the user's affiliation.  This is the main purpose of using different affiliations – to allow users from multiple organizations with different external authentication mechanisms to use a single VCL deployment.

For example, a single VCL deployment could be shared by several different schools.  Each school has its own set of users and its own existing LDAP or Shibboleth-based authentication infrastructure.

VCL can be configured so that when a user from school A logs in, school A's Active Directory-based LDAP directory is used to authenticate the user's credentials.  When a user from school B logs in, school B's Shibboleth Identity Provider is used.

## Built-In Affiliations

Two built-in affiliations are included in a stock installation of VCL, **Local** and **Global**.

### Local Affiliation

The **Local** affiliation represents the entity to which all of the the local user accounts stored in the VCL database belong.  By default, Local is the only affiliation that may be used to authenticate to the VCL website.  You'll notice this when expanding the **Please select an authentication method to use** drop-down menu on a stock installation of VCL:

<img src="images/image2017-2-20 15_53_30.png" width="300" border="1">

### Global Affiliation

**Global** is a special affiliation.  A user account would *never* belong to the Global affiliation.  The Global affiliation is used to determine the default value to use for certain settings when a value has not been specifically set for a user's actual affiliation.  For example, the reservation timeout thresholds are configured uniformly by default for every VCL user on the Site Configuration page:
