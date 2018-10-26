---
title: Granting Two Sets of Users Access to Two Different Sets of Images
tags:
keywords:
last_updated: Oct 26, 2018
summary:
sidebar: mydoc_sidebar
permalink: Two-Sets-of-users-access-to-two-different-images.html
---

This page explains how to grant one groups of users access to one set of images, and another set of users access to a separate set of images.

### 1. Create a User Group for Each Set of Users

First, you need to create two user groups - one for each set of users.

1. Click **Manage Groups**
2. Under the User Groups section (at the top):
    * Enter a name for the group.
    * (Optionally, if asked) Select an affiliation for the group.
    * Enter an owner for the group - most likely yourself.
    * Select a group allowed to edit the user group.
    * Other fields can be left as defaults.
3. Click **Add**
4. Repeat the steps for the second group.

From here on, the two groups will be called **faculty** and **student**.

### 2. Add Users to Each Group

You need to add users to each of the groups.

1. Make sure you're on the manage groups page.
2. Click edit next to the **faculty** group
3. Enter a userID in the box next to the Add Button.(NOTE: if you are not using LDAP authentication, the users' accounts will already need to exist in VCL)
4. Click Add
5. Repeat for all users you wish to add to the faculty group.
6. Repeat steps for users within the student group.

### 3. Create an Image Group for Each Set of Images.

Next, you need to create an image group for each set of images.

1. Click Manage Groups
2. Under the Resource Groups section (further down the page):
    * Select Image as the type
    * Enter a name for the group (it can contain spaces)
    * Select a user group that will own the resource group (this user group will have access to manage some aspects of the resource group)
3. Click Add
4. Repeat the steps for the second group


From here on, the groups will be called **faculty images** and **student images**


### 4. Add Images to Each Group

Now, you need to add the desired images to each group.

1. Click Manage Images
2. Select the Edit Image Grouping radio button
3. Click Submit
4. Click the By Group tab
5. Select faculty images
6. Click Get Images
7. Select any images you want to be available to the faculty user group in the list on the right (Ctrl+click to select multiple images)
8. Click the <-Add button
9. Repeat steps for the student images group

### 5. Map the Image Groups to Computer Groups

In order for VCL to know on which computers the images can run, you must map the image groups to computer groups. I'll assume you already have one or more computer groups that contain computers.

1. Click Manage Images
2. Select the Edit Image Mapping radio button
3. Click Submit
4. Select the faculty images group
5. Click Get Computer Groups
6. Select at least one computer group to map it to from the list on the right
7. Click the <-Add button
8. Repeat for the student images group (NOTE: It is okay to map both image groups to the same computer group. That will not affect what images the users have access to.)

### 6. Create a Two Privilege Nodes

Now, you need to create one node for each user group to separate their access.

1. Click Privileges
2. In the tree at the top of the page, click a node under which you'll create the two new nodes
3. Click the Add Child button
4. Enter a name for the new node (spaces are allowed)
5. Click Create Child
6. Repeat for the second node for the other user group
7. I'll refer to these nodes as faculty access and student access


### 7. Assign Rights at Each Node

Finally, you need to give each user group the imageCheckOut privilege at their respective nodes, and give each image group the available attribute at their respective nodes.

1. Click on the faculty access node
2. Under User Groups, click Add Group
3. Select the faculty group
4. Select the checkbox for the imageCheckOut privilege
5. Click Submit New User Group
6. Scroll down to the Resources section and click Add Resource Group
7. Select the image/faculty images group
8. Select the checkbox for the available attribute
9. Click Submit New Resource Group
10. Click on the student access node
11. Under User Groups, click Add Group
12. Select the student group
13. Select the checkbox for the imageCheckOut privilege
14. Click Submit New User Group
15. Scroll down to the Resources section and click Add Resource Group
16. Select the image/student images group
17. Select the checkbox for the available attribute
18. Click Submit New Resource Group


### Summary

Now, users in the faculty user group will have access to check out images in the faculty images image group, and users in the student user group will have access to check out images in the student images image group.
