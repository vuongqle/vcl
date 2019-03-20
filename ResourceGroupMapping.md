---
title: Resource Groups and Mapping
last_updated: March 19, 2019
permalink: Resource-Groups-and-Mapping.html
---

Resources are often acted on within VCL via resource groups rather than doing thing directly with the resources themselves. All resources have a method by which they are placed into groups. This method is very similar for all types of resources.

Additionally, resources often need to be related to other resources. VCL refers to this as resource mapping. Not all resource types can be mapped to all other resource types. Methods for mapping resources together are only provided for mapping types of resources together that are needed. There are a few special cases where individual resources are related directly together rather than being related through resource group mapping. The following diagram illustrates how current resources and groups are related together.

<img src="images/Screen Shot 2019-03-20 at 1.47.31 PM.png" width="500" border="1">


In order for VCL to know which computers can be used to fulfill a reservation for a given image, it must have information about which computers the image can be loaded on. This is what image group to computer group mapping provides.

In order for VCL to know which management node can be assigned to fulfill deploying an image to a given computer, it must have information about which management nodes can handle the computer. This is what management node group to computer group mapping provides.

It was mentioned earlier that there are a few exceptions to relating resources together. Image to AD domain relation and computer to schedule relation are the exceptions. Computers can only have a single schedule. Therefore, that assignment is done as a property of computers. Images can only be part of a single AD domain. Therefore, that assignment is done as a property of images.
