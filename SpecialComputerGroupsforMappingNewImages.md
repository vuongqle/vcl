---
title: Special Computer Groups for Mapping New Images
last_updated: Feb 14, 2019
permalink: Special-Computer-Groups-for-Mapping-New-Images.html
---

There are two special computer groups: **newvmimages** and **newimages.** Eventually, these will be reduced to a single group, but for historical reasons, there are still two separate groups. When a  new image is created, VCL needs a set of computers to which that image can be mapped so that it can be deployed. Therefore, these two computer groups exist. **It is up to VCL site administrators to determine which computers they think should be used for testing out new images**. These computers should be added to the newvmimages and newimages groups. The newvmimages group is for VM computers; the newimages group is for bare metal computers.

When new images are created, new image groups are automatically created (one for each image owner) and mapped to the appropriate newvmimages or newimages computer group.
