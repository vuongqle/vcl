---
title: Troubleshooting Error Encountered; "Please try again later"
last_updated: Jan 03, 2019
sidebar: mydoc_sidebar
permalink: Troubleshooting-Error-Encountered-Please-Try-Again-Later.html
---

VCL 2.4 introduced a notification to the user when an AJAX error occurs. Previously, no notification was given to the user. In most cases, it just appeared that nothing happened. However, in either case the error is displayed in the browser JavaScript console. Here is how to open the JavaScript console in a few browsers:

### Firefox:
1. Click the menu button in the top right
2. Select Developer
4. Click Web Console
5. Click the down arrow next to Net (on the left side of the new part of the window)
6. Make sure Log and Log Request and Response Bodies are checked

### Chrome:
1. Click the menu button in the top right
2. Select More tools
3. Click Developer Tools
4. Click the Network tab in the new part of the window
5. Make sure the circular icon (all the way on the left of the new part of the window, just under the search icon) is red meaning it is recording AJAX calls

### IE:
1. Click the gear icon in the top right
2. Select F12 Developer Tools
3. Select the Network icon from the column of icons on the left under "F12"; it is the one that looks like a wireless router and is under the bug with the not symbol over it - you may have to scroll down with the down arrow at the bottom of the column to see it
4. Click the green arrow icon (play button style arrow) to start recording AJAX calls. It is to the left of the work Network that is next to F12. If it is already recording, it will be a red square icon (stop style icon).

### Safari:
1. Open the **Safari** menu at the top of the screen
2. Select **Preferences**
3. Click the **Advanced** (gear) icon
4. Check the **show Develop menu in menu bar** checkbox at the bottom
5. Close the **Preferences** window
6. Select the newly added **Develop** menu at the top
7. Select **Show Error Console**
8. Make sure the **Resources** tab in the new part of the window is selected (the icon to the left of Resources will be blue if it is selected)

Now that you have the console open to record the AJAX calls, you need to repeat whatever you did that gave the error. The response to the AJAX call will be recorded this time. You need to view the response of the AJAX call to see what the web server returned. It will be much more helpful if you are logged in to VCL with a user that is a member of a user group having the **View Debug Information** additional user group permission set under Privileges->Additional User Permissions. Again, it is browser specific to view the contents of the response.

### Firefox:
1. Click the line closest to the bottom that is a POST to https://.../index.php
2. A new window pops up with information about the POST
3. Scroll down to Response Body
If there is no Response Body section, it means the site didn't send anything back. You'll need to enable php error logging on your web server and look at the php error logs.

### Chrome:
1. Click the line closest to the bottom that is index.php with POST in the Method column
2. Information about the POST is displayed to the right
3. Click the Response tab to view the what was received from the web server

### IE:
1. Double click the last /.../index.php line with POST in the Method column
2. Click the Response body tab to view the what was received from the web server

### Safari:
1. On the left, you'll see a tree type hierarchy, find index.php->XHRs
2. Click the index.php at the bottom of the list of XHRs
3. On the right, what was received from the web server will be displayed

Once you have found what was returned by the web server, you can start figuring out what happened. Ask on the user@vcl.apache.org list if you need help.
