---
title: Configuring Reservation Timeout Thresholds
last_updated: March 19, 2019
permalink: Configuring-Reservation-Timeout-Thresholds.html
---
## Overview

Timeouts are a natural part of how VCL reservations are processed. Timeouts are used to control the various interactions or VCL processing of a reservation. All timeout thresholds are configured on the Site Configuration page of the VCL portal. The timeouts allow for a single global setting or you can set the timeouts for individual affiliations if you are supporting multiple institutions on your VCL system. Each of the various timeout variables are discussed below.


## Acknowledge Reservation Timeout

Once the user's reservation is ready, the user will need to click on the Connect button on the reservations page before continuing. The Acknowledge Reservation Timeout is related to the amount of time VCL system should wait for user interaction before automatically ending the reservation request. If the timeout is reached and the user has not acknowledged, the reservation is set to the appropriate state that begins the cleanup process of removing the user's account. This setting does not apply to Server reservations. A single VCL system can have multiple Acknowledge Reservation Timeouts to support different affiliations.

<img src="images/Screen Shot 2017-03-15 at 4.05.53 PM.png" width="500" border="1">

## Connect to Reservation Timeout

After a user clicks the Connect button to acknowledge the reservation, user's will have a set time period to connect to the remote machine set by the Connect To Reservation Timeout variable. If the user does not connect to the remote machine within the provided time, the reservation is set to the appropriate state that begins the cleanup process of removing the user's account, reseting the firewall, and other related tasks.

<img src="images/Screen Shot 2017-03-15 at 4.10.08 PM.png" width="500" border="1">

## In-Use Reservation Check

Defines the frequency to check for user connections to a user's VCL machine during an active general reservation.

<img src="images/Screen Shot 2017-03-15 at 4.32.07 PM.png" width="500" border="1">

##In-Use Reservation Check (servers)

 Defines the frequency to check for user connections during a server reservation. Server reservations are considered to be active for a long duration, it is recommended this value be high such as 30 minutes or an hour. Because this is a server reservation if the user is not actively connected to the machine no action is taken until the scheduled end of the reservation.

 <img src="images/Screen Shot 2017-03-16 at 1.56.52 PM.png" width="500" border="1">

## In-Use Reservation Check (clusters)

Defines the frequency to check for user connections during an server reservation. Cluster reservations are considered to be active for a longer duration, it is recommended this value be high such as 30 minutes or an hour. Because this is a cluster reservation if the user is not actively connected to the machine no action is taken until the scheduled end of the reservation.

<img src="images/Screen Shot 2017-03-16 at 1.57.47 PM.png" width="500" border="1">

## Connected User Check Threshold

The Connected User Check Threshold instructs the VCL system to not perform active user checks for reservations that have durations over the defined time period. For instance if you a server or long-term reservation that might last for months there is no need for the System to confirm the user is connected or not. This reduces the number of VCL tasks that have to occur for given reservations.

<img src="images/Screen Shot 2017-03-15 at 4.08.48 PM.png" width="500" border="1">

## Reconnect To Reservation Timeout

The Reconnect To Reservation Timeout variable determines the amount of time (in minutes) to wait for a user to reconnect to their reservation or reserved machine. If the user does not reconnect within the defined time period the VCL will reclaim the machine. In many cases when users are done with a VCL image, they simply logout or disconnect a leave do not release the reservation. This setting is useful to reclaim machines that have been abandoned. It is not recommended to set this value too low as will affect the user experience. Customers have found that 15-20 minutes is a reasonable value for a large user base.

<img src="images/Screen Shot 2017-03-15 at 4.31.01 PM.png" width="500" border="1">
