---
title: Special Resource Groups
last_updated: Feb 25, 2019
permalink: Special-Resource-Groups.html
---
## Overview
There are a few special resource groups of which VCL admins need to be aware that are related to creation and deployment of new images. When a new image is created, VCL needs a way to know where that image can be deployed. There are 2 sets of parallel image groups and computer groups - one for bare metal images and one for virtual images. There is no longer a technical need for having separate groups for bare metal vs virtual, but they both still exist for historical reasons. They will likely be combined in a future release of VCL.

## Computer Groups
There are two special computer groups: newimages and newvmimages. VCL admins should assign computers to these groups that can be used for testing out newly created images. There does not need to be anything special about the computers, but computers that do have something special about them (i.e. set aside for a special project) should not be included in these groups. These computer groups must be mapped to a management node group that contains a management node that is able to control the computers in the groups. The special user group manageNewImages is the owning user group of these computer groups. Though possible, it is best to leave that unchanged.

## Image Groups
The image groups are a little different. These groups are created as needed whenever users create their first image. The groups include the user's username and ID from the database user table in the name of the group. For example, the names of the image groups for the default admin account would be newimages-admin-1 and newvmimages-admin-1. Whenever someone creates a new image, the appropriate image group (bare or virtual) is created if it does not exist, the image is added to the group, the group is mapped to the appropriate computer group (newimages or newvmimages), and a privilege node is created granting the user admin access to the image group.

As with the special computer groups, the special user group manageNewImages is the owning user group of these image groups.

It is safe to delete these image groups to clean things up as long as any images assigned to them have either had the ownership changed to a different user or the images have been assigned to an image group that gives someone else administrative control of the image. If one of the image groups were to be deleted while containing an image no other user had access to control, no one would have any access to use or manage the image - this could only fixed by directly modifying the database. By default, the manageNewImages user group is granted privileges in the Privilege tree that give it admin access to all of the images in these special new images groups. Therefore, membership of the manageNewImages group should be carefully controlled.

## Special Considerations
Images That Should Only Be Run on Certain Computers
As explained above, any new images are mapped to be run on the computers in the newimages and newvmimages computer groups. If there is an image that should only be run on a specific set of computers that are not part of the newimage or newvmimages computer groups, the image needs to be added to an image group that is mapped to the desired set of computers and removed from the image creator's new images group so that it is no longer mapped to run on the new images computers. Only users in the manageNewImages user group have access to remove images from a user's new images image group. Note, it is important first add the image to another image group before removing it from the image creator's image group if the user doing the steps is not the owner so that access to control the grouping of the image is not lost.

If it is desired for a specific user to be able to remove his own images from his new images groups for some reason, the user can be granted manageMapping for that user's new images groups at the user's privilege node to grant access to see the group and able to remove images from it. For example, if a user has a special set of computers on which her images should always be run, she would always want to remove her newly created images from her special new images group and assign them to an image group mapped to the special set of computers. Then, a higher level admin would not need to be involved in the process.
