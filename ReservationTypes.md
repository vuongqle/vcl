---
title: Reservation Types
tags: [reservations, types]
keywords:
last_updated: Oct 31, 2018
summary:
sidebar: mydoc_sidebar
permalink: reservation-types.html
---

Reservations are at the core of VCL. End user's make use of VCL by making reservations for Images for some selected duration. The image selected by the user is then deployed to a node and dedicated to the user for the duration of the reservation. VCL is aware of how many slots it has available at any given time. If there are no resources available for a time selected by a user, a list of suggested times is presented to the user to allow the user to schedule a time in the future for using the image.

VCL has three types of reservations that can be made:

* Basic Reservations
* Imaging Reservations
* Server Reservations


### Basic Reservations

Most users will use VCL to make basic reservations. These typically only have a duration of several hours and can only be accessed by the person that made the reservation. Cluster reservations are included in basic reservations for images that have subimages assigned to them. Some users can have extended access that allows them to make basic reservations for up to 59 days. With default settings, basic reservations that are less than 24 hours are timed out due inactivity when a user is disconnected for 15 minutes. This prevents users from making a several hour reservation, using it for only a small portion of that time, and then tying up the resource for the remaining amount of time when the resource is not actually being used. Users that have access to create new images can select to create a new image from a basic reservation that is not a cluster. Basic reservations can also be timed out if the user makes the reservation and never clicks the Connect button, or does click the Connect button but never actually connects to it.

### Imaging Reservations

Users with access to create images can do so from basic reservations in most cases, but there are some benefits to selecting to make an imaging reservation when creating new images. Imaging reservations allow users to select 12 hours for the duration of the reservation even if they do not have access to make 12 hour basic reservations. Imaging reservations do not get timed out due to a user being disconnected. At the end of an imaging reservation, if the user has not selected to create the image, an image is automatically captured from the reservation. This prevents the work done to create the image from being lost. Finally, when making an imaging reservation for an image that has subimages assigned to it, the user only gets a reservation for the parent image. This is the only way to create and update parent images from clusters since images cannot be captured from cluster reservations. As with basic reservations, imaging reservations can be timed out if the user makes the reservation and never clicks the Connect button, or does click the Connect button but never actually connects to it.

### Server Reservations

Server reservations are intended to last a long time. The end of the reservation is either specified as an end date and time, or specified to be indefinite. Server reservations are never timed out. Once the user creates the reservation, it will exist until the end time is reached or the user deletes it. Server reservations have some additional features not available to the other types of reservations:

* Keep Reservation & Create Image
    * Keep Reservation & Create Image allows the user to capture a new image or revision of the image and to keep the reservation afterword. The node will be taken offline during this process and all of the things that are normally done during image capture will be done. However, after that process the image will be redeployed to the node as if the user had just made a new reservation for it. Note that things like temporary files and user home directories will be cleaned out during this process as they are during normal image captures. This option is useful for keeping the reservation on the same node and retaining the same MAC and IP addresses.
* Admin user group
    * Admin user group can be selected for the reservation. Users in the admin user group will also see the reservation on their Reservations page in VCL, and will see Connect, Delete Reservation, and More Options buttons for the reservation. Admin users that click the Delete Reservation button will be warned that they do not own the reservation before it is deleted. Users in the admin user group will have administrative access within the reservation just as the owner does unless the image has been configured not to allow administrative access.
* Access user group
    * Access user group can be selected for the reservation. Users in the access user group will also see the reservation on their Reservations Page in VCL. However, unlike users in the admin user group, these users will only see a Connect button. Users in the access user group will not have administrative access within the reservation.
* Reservation naming
    * Reservation naming allows the reservation to be given a name that is displayed on the Reservations page instead of the name of the image. This is useful for keeping track of reservations of the same image that are being used for different purposes.
* Specified IP address
    * Server reservations can have a Specified IP address. The address is assigned on the node on the public interface instead of using DHCP or a static address from the computer's profile. This option requires that the specified IP address falls into a range that has been specified in the Available Public Networks field for a management node that can control a node on which the image can be deployed.
