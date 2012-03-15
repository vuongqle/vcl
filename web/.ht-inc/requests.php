<?php
/*
  Licensed to the Apache Software Foundation (ASF) under one or more
  contributor license agreements.  See the NOTICE file distributed with
  this work for additional information regarding copyright ownership.
  The ASF licenses this file to You under the Apache License, Version 2.0
  (the "License"); you may not use this file except in compliance with
  the License.  You may obtain a copy of the License at

      http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
*/

/**
 * \file
 */
/// signifies an error with the submitted start date
define("STARTDAYERR", 1);
/// signifies an error with the submitted start hour
define("STARTHOURERR", 1 << 1);
/// signifies an error with the submitted start minute
define("STARTMINUTEERR", 1 << 2);
/// signifies an error with the submitted end date
//define("ENDDAYERR", 1 << 2);
/// signifies an error with the submitted end hour
//define("ENDHOURERR", 1 << 3);
/// signifies an error with the submitted endding date and time
define("ENDDATEERR", 1 << 4);
/// signifies an error with the submitted image id
define("IMAGEIDERR", 1 << 5);

////////////////////////////////////////////////////////////////////////////////
///
/// \fn newReservation()
///
/// \brief prints form for submitting a new reservation
///
////////////////////////////////////////////////////////////////////////////////
function newReservation() {
	global $submitErr, $user, $mode, $skin;
	$timestamp = processInputVar("stamp", ARG_NUMERIC);
	$imageid = processInputVar("imageid", ARG_STRING, getUsersLastImage($user['id']));
	$length = processInputVar("length", ARG_NUMERIC);
	$day = processInputVar("day", ARG_STRING);
	$hour = processInputVar("hour", ARG_NUMERIC);
	$minute = processInputVar("minute", ARG_NUMERIC);
	$meridian = processInputVar("meridian", ARG_STRING);
	$imaging = getContinuationVar('imaging', processInputVar('imaging', ARG_NUMERIC, 0));

	if(! $submitErr) {
		if($imaging)
			print "<H2>Create / Update an Image</H2>\n";
		else
			print "<H2>New Reservation</H2><br>\n";
	}

	if($imaging) {
		$resources = getUserResources(array("imageAdmin"));
		if(empty($resources['image'])) {
			print "You don't have access to any base images from which to create ";
			print "new images.<br>\n";
			return;
		}
		if($length == '')
			$length = 480;
	}
	else {
		$resources = getUserResources(array("imageAdmin", "imageCheckOut"));
		$resources["image"] = removeNoCheckout($resources["image"]);
	}

	if((! in_array("imageCheckOut", $user["privileges"]) &&
	   ! in_array("imageAdmin", $user["privileges"])) ||
	   empty($resources['image'])) {
		print "You don't have access to any environments and, therefore, cannot ";
		print "make any reservations.<br>\n";
		return;
	}
	if($imaging) {
		print "Please select the environment you will be updating or using as a ";
		print "base for a new image:<br>\n";
	}
	else
		print "Please select the environment you want to use from the list:<br>\n";

	$images = getImages();
	$maxTimes = getUserMaxTimes();
	if(! $imaging) {
		print "<script language=javascript>\n";
		print "var defaultMaxTime = {$maxTimes['initial']};\n";
		print "var maxTimes = {\n";
		foreach(array_keys($resources['image']) as $imgid) {
			if(array_key_exists($imgid, $images))
				print "   $imgid: {$images[$imgid]['maxinitialtime']},\n";
		}
		print "   0: 0\n"; // this is because IE doesn't like the last item having a ',' after it
		print "};\n";
		print "</script>\n";
	}

	print "<FORM action=\"" . BASEURL . SCRIPT . "\" method=post>\n";
	// list of images
	uasort($resources["image"], "sortKeepIndex");
	printSubmitErr(IMAGEIDERR);
	if($submitErr & IMAGEIDERR)
		print "<br>\n";
	if($imaging) {
		if(USEFILTERINGSELECT && count($resources['image']) < FILTERINGSELECTTHRESHOLD) {
			print "      <select dojoType=\"dijit.form.FilteringSelect\" id=imagesel ";
			print "onChange=\"updateWaitTime(1);\" tabIndex=1 style=\"width: 400px\" ";
			print "queryExpr=\"*\${0}*\" highlightMatch=\"all\" autoComplete=\"false\" ";
			print "name=imageid>\n";
			foreach($resources['image'] as $id => $image) {
				if($image == 'No Image')
					continue;
				if($id == $imageid)
					print "        <option value=\"$id\" selected>$image</option>\n";
				else
					print "        <option value=\"$id\">$image</option>\n";
			}
			print "      </select>\n";
		}
		else
			printSelectInput('imageid', $resources['image'], $imageid, 1, 0, 'imagesel', "onChange=\"updateWaitTime(1);\"");
	}
	else {
		if(USEFILTERINGSELECT && count($resources['image']) < FILTERINGSELECTTHRESHOLD) {
			print "      <select dojoType=\"dijit.form.FilteringSelect\" id=imagesel ";
			print "onChange=\"selectEnvironment();\" tabIndex=1 style=\"width: 400px\" ";
			print "queryExpr=\"*\${0}*\" highlightMatch=\"all\" autoComplete=\"false\" ";
			print "name=imageid>\n";
			foreach($resources['image'] as $id => $image)
				if($id == $imageid)
					print "        <option value=\"$id\" selected>$image</option>\n";
				else
					print "        <option value=\"$id\">$image</option>\n";
			print "      </select>\n";
		}
		else
			printSelectInput('imageid', $resources['image'], $imageid, 1, 0, 'imagesel', "onChange=\"selectEnvironment();\"");
	}
	print "<br><br>\n";

	$imagenotes = getImageNotes($imageid);
	$desc = '';
	if(preg_match('/\w/', $imagenotes['description'])) {
		$desc = preg_replace("/\n/", '<br>', $imagenotes['description']);
		$desc = preg_replace("/\r/", '', $desc);
		$desc = "<strong>Image Description</strong>:<br>\n$desc<br><br>\n";
	}
	print "<div id=imgdesc>$desc</div>\n";

	print "<fieldset id=whenuse class=whenusefieldset>\n";
	if($imaging)
		print "<legend>When would you like to start the imaging process?</legend>\n";
	else
		print "<legend>When would you like to use the application?</legend>\n";
	print "&nbsp;&nbsp;&nbsp;<INPUT type=radio name=time id=timenow ";
	print "onclick='updateWaitTime(0);' value=now checked>";
	print "<label for=\"timenow\">Now</label><br>\n";
	print "&nbsp;&nbsp;&nbsp;<INPUT type=radio name=time value=future ";
	print "onclick='updateWaitTime(0);' id=\"laterradio\">";
	print "<label for=\"laterradio\">Later:</label>\n";
	if(array_key_exists($imageid, $images))
		$maxlen = $images[$imageid]['maxinitialtime'];
	else
		$maxlen = 0;
	if($submitErr) {
		$hour24 = $hour;
		if($hour24 == 12) {
			if($meridian == "am") {
				$hour24 = 0;
			}
		}
		elseif($meridian == "pm") {
			$hour24 += 12;
		}
		list($month, $day, $year) = explode('/', $day);
		$stamp = datetimeToUnix("$year-$month-$day $hour24:$minute:00");
		$day = date('l', $stamp);
		printReserveItems(1, $imaging, $length, $maxlen, $day, $hour, $minute, $meridian);
	}
	else {
		if(empty($timestamp))
			$timestamp = unixFloor15(time() + 4500);
		$timeArr = explode(',', date('l,g,i,a', $timestamp));
		printReserveItems(1, $imaging, $length, $maxlen, $timeArr[0], $timeArr[1], $timeArr[2], $timeArr[3]);
	}
	print "</fieldset>\n";

	print "<div id=waittime class=hidden></div><br>\n";
	$cont = addContinuationsEntry('submitRequest', array('imaging' => $imaging), SECINDAY, 1, 0);
	print "<INPUT type=hidden name=continuation value=\"$cont\">\n";
	if($imaging)
		print "<INPUT id=newsubmit type=submit value=\"Create Imaging Reservation\" ";
	else
		print "<INPUT id=newsubmit type=submit value=\"Create Reservation\" ";
	print "onClick=\"return checkValidImage();\">\n";
	print "<INPUT type=hidden id=testjavascript value=0>\n";
	print "</FORM>\n";
	$cont = addContinuationsEntry('AJupdateWaitTime', array('imaging' => $imaging));
	print "<INPUT type=hidden name=waitcontinuation id=waitcontinuation value=\"$cont\">\n";

	print "<div dojoType=dijit.Dialog\n";
	print "      id=\"suggestedTimes\"\n";
	print "      title=\"Available Times\"\n";
	print "      duration=250\n";
	print "      draggable=true>\n";
	print "   <div id=\"suggestloading\" style=\"text-align: center\">";
	print "<img src=\"themes/$skin/css/dojo/images/loading.gif\" ";
	print "style=\"vertical-align: middle;\"> Loading...</div>\n";
	print "   <div id=\"suggestContent\"></div>\n";
	print "   <input type=\"hidden\" id=\"suggestcont\">\n";
	print "   <input type=\"hidden\" id=\"selectedslot\">\n";
	print "   <div align=\"center\">\n";
	print "   <button id=\"suggestDlgBtn\" dojoType=\"dijit.form.Button\" disabled>\n";
	print "     Use Selected Time\n";
	print "	   <script type=\"dojo/method\" event=\"onClick\">\n";
	print "       useSuggestedSlot();\n";
	print "     </script>\n";
	print "   </button>\n";
	print "   <button id=\"suggestDlgCancelBtn\" dojoType=\"dijit.form.Button\">\n";
	print "     Cancel\n";
	print "	   <script type=\"dojo/method\" event=\"onClick\">\n";
	print "       dijit.byId('suggestDlgBtn').set('disabled', true);\n";
	print "       showDijitButton('suggestDlgBtn');\n";
	print "       dijit.byId('suggestDlgCancelBtn').set('label', 'Cancel');\n";
	print "       dijit.byId('suggestedTimes').hide();\n";
	print "       dojo.byId('suggestContent').innerHTML = '';\n";
	print "     </script>\n";
	print "   </button>\n";
	print "   </div>\n";
	print "</div>\n";
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn AJupdateWaitTime()
///
/// \brief generates html update for ajax call to display estimated wait time
/// for current selection on new reservation page
///
////////////////////////////////////////////////////////////////////////////////
function AJupdateWaitTime() {
	global $user, $requestInfo;
	# proccess length
	$length = processInputVar('length', ARG_NUMERIC);
	$times = getUserMaxTimes();
	$imaging = getContinuationVar('imaging');
	if(empty($length) ||
	   ($length > $times['initial'] && ! $imaging ) ||
		($length > $times['initial'] && $imaging && $length > 720)) {
		return;
	}
	# process imageid
	$imageid = processInputVar('imageid', ARG_NUMERIC);
	$resources = getUserResources(array("imageAdmin", "imageCheckOut"));
	$validImageids = array_keys($resources['image']);
	if(! in_array($imageid, $validImageids))
		return;

	$desconly = processInputVar('desconly', ARG_NUMERIC, 1);

	$imagenotes = getImageNotes($imageid);
	if(preg_match('/\w/', $imagenotes['description'])) {
		$desc = preg_replace("/\n/", '<br>', $imagenotes['description']);
		$desc = preg_replace("/\r/", '', $desc);
		$desc = preg_replace("/'/", '&#39;', $desc);
		print "dojo.byId('imgdesc').innerHTML = '<strong>Image Description</strong>:<br>";
		print "$desc<br><br>'; ";
	}

	if($desconly) {
		if($imaging)
			print "if(dojo.byId('newsubmit')) dojo.byId('newsubmit').value = 'Create Imaging Reservation';";
		else
			print "if(dojo.byId('newsubmit')) dojo.byId('newsubmit').value = 'Create Reservation';";
		return;
	}

	$images = getImages();
	$now = time();
	$start = unixFloor15($now);
	$end = $start + $length * 60;
	if($start < $now)
		$end += 15 * 60;
	$imagerevisionid = getProductionRevisionid($imageid);
	$rc = isAvailable($images, $imageid, $imagerevisionid, $start, $end);
	semUnlock();
	if($rc < 1) {
		$cdata = array('now' => 1,
		               'start' => $start, 
		               'end' => $end,
		               'server' => 0,
		               'imageid' => $imageid);
		$cont = addContinuationsEntry('AJshowRequestSuggestedTimes', $cdata);
		if(array_key_exists('subimages', $images[$imageid]) &&
		   count($images[$imageid]['subimages']))
			print "dojo.byId('suggestcont').value = 'cluster';";
		else
			print "dojo.byId('suggestcont').value = '$cont';";
		print "if(dojo.byId('newsubmit')) dojo.byId('newsubmit').value = 'View Available Times';";
	}
	print "dojo.byId('waittime').innerHTML = ";
	if($rc == -2)
		print "'<font color=red>Selection not currently available due to scheduled system downtime for maintenance</font>'; ";
	elseif($rc < 1) {
		print "'<font color=red>Selection not currently available</font>'; ";
		print "showSuggestedTimes(); ";
	}
	elseif(array_key_exists(0, $requestInfo['loaded']) &&
		   $requestInfo['loaded'][0]) {
			print "'Estimated load time: &lt; 1 minute';";
	}
	else {
		$loadtime = (int)(getImageLoadEstimate($imageid) / 60);
		if($loadtime == 0)
			print "'Estimated load time: &lt; {$images[$imageid]['reloadtime']} minutes';";
		else
			printf("'Estimated load time: &lt; %2.0f minutes';", $loadtime + 1);
	}
	if($rc > 0) {
		if($imaging)
			print "if(dojo.byId('newsubmit')) dojo.byId('newsubmit').value = 'Create Imaging Reservation';";
		else
			print "if(dojo.byId('newsubmit')) dojo.byId('newsubmit').value = 'Create Reservation';";
	}
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn AJshowRequestSuggestedTimes()
///
/// \brief builds html to display list of available times the selected image
/// can be used
///
////////////////////////////////////////////////////////////////////////////////
function AJshowRequestSuggestedTimes() {
	global $user;
	$data = array();
	$start = getContinuationVar('start');
	$end = getContinuationVar('end');
	$imageid = getContinuationVar('imageid');
	$now = getContinuationVar('now');
	$server = getContinuationVar('server');
	$ip = getContinuationVar('ip', '');
	$mac = getContinuationVar('mac', '');
	$requestid = getContinuationVar('requestid', '');
	$extendonly = getContinuationVar('extendonly', 0);
	if($now && $start < time()) {
		# $start sould have been decreased by 15 minutes
		$start = $start + 900;
	}
	if($server)
		$slots = findAvailableTimes($start, $end, $imageid, $user['id'], 0,
		                            $requestid, $extendonly, $ip, $mac);
	else
		$slots = findAvailableTimes($start, $end, $imageid, $user['id'], 1,
		                            $requestid, $extendonly);
	$data['status'] = 'success';
	if($requestid != '') {
		$reqdata = getRequestInfo($requestid, 0);
		if(is_null($reqdata)) {
			$data['status'] = 'resgone';
			sendJSON($data);
			return;
		}
	}
	if(empty($slots)) {
		$data['html'] = "There are no available times that<br>the selected image can be used.<br><br>";
		$data['status'] = 'error';
		sendJSON($data);
		return;
	}

	$data['data'] = $slots;
	$html = '';
	$html .= "<table summary=\"available time slots\" class=\"collapsetable\">";
	if($extendonly) {
		$slot = array_pop($slots);
		$maxextend = $slot['duration'] - (datetimeToUnix($reqdata['end']) - datetimeToUnix($reqdata['start']));
		if($maxextend < 900) {
			$data['html'] = 'This reservation can no longer be extended due to<br>'
			              . 'a reservation immediately following yours.<br><br>';
			$data['status'] = 'noextend';
			sendJSON($data);
			return;
		}
		$html .= "<tr>";
		$html .= "<td></td>";
		$html .= "<th>End Time</th>";
		$html .= "<th>Extend By</th>";
		$html .= "</tr>";
		$cnt = 0;
		$e = datetimeToUnix($reqdata['end']);
		$slots = array();
		for($cnt = 0, $amount = 900, $e = datetimeToUnix($reqdata['end']) + 900;
		    $cnt < 15 && $amount <= $maxextend && $amount < 7200;
		    $cnt++, $amount += 900, $e += 900) {
			# locale specific
			$end = date('n/j/y g:i a', $e);
			$extenstion = getReservationExtenstion($amount / 60);
			if($cnt % 2)
				$html .= "<tr class=\"tablerow0\">";
			else
				$html .= "<tr class=\"tablerow1\">";
			$html .= "<td><input type=\"radio\" name=\"slot\" value=\"$e\" ";
			$html .= "id=\"slot$amount\" onChange=\"setSuggestSlot('$e');\"></td>";
			$html .= "<td><label for=\"slot$amount\">$end</label></td>";
			$html .= "<td style=\"padding-left: 8px;\">";
			$html .= "<label for=\"slot$amount\">$extenstion</label></td></tr>";
			$slots[$e] = array('duration' => $amount,
			                   'startts' => $slot['startts']);
		}
		for(; $cnt < 15 && $amount <= $maxextend;
		    $cnt++, $amount += 3600, $e += 3600) {
			# locale specific
			$end = date('n/j/y g:i a', $e);
			$extenstion = getReservationExtenstion($amount / 60);
			if($cnt % 2)
				$html .= "<tr class=\"tablerow0\">";
			else
				$html .= "<tr class=\"tablerow1\">";
			$html .= "<td><input type=\"radio\" name=\"slot\" value=\"$e\" ";
			$html .= "id=\"slot$amount\" onChange=\"setSuggestSlot('$e');\"></td>";
			$html .= "<td><label for=\"slot$amount\">$end</label></td>";
			$html .= "<td style=\"padding-left: 8px;\">";
			$html .= "<label for=\"slot$amount\">$extenstion</label></td></tr>";
			$slots[$e] = array('duration' => $amount,
			                   'startts' => $slot['startts']);
		}
		$data['data'] = $slots;
	}
	else {
		$html .= "<tr>";
		$html .= "<td></td>";
		$html .= "<th>Start Time</th>";
		$html .= "<th>Duration</th>";
		if(checkUserHasPerm('View Debug Information'))
			$html .= "<th>Comp. ID</th>";
		$html .= "</tr>";
		$cnt = 0;
		foreach($slots as $key => $slot) {
			$cnt++;
			# locale specific
			$start = date('n/j/y g:i a', $slot['startts']);
			if(($slot['startts'] - time()) + $slot['startts'] + $slot['duration'] >= 2114402400)
				# end time >= 2037-01-01 00:00:00
				$duration = 'indefinite';
			else
				$duration = getReservationLength($slot['duration'] / 60);
			if($cnt % 2)
				$html .= "<tr class=\"tablerow0\">";
			else
				$html .= "<tr class=\"tablerow1\">";
			$html .= "<td><input type=\"radio\" name=\"slot\" value=\"$key\" id=\"slot$key\" ";
			$html .= "onChange=\"setSuggestSlot('{$slot['startts']}');\"></td>";
			$html .= "<td><label for=\"slot$key\">$start</label></td>";
			$html .= "<td style=\"padding-left: 8px;\">";
			$html .= "<label for=\"slot$key\">$duration</label></td>";
			if(checkUserHasPerm('View Debug Information'))
				$html .= "<td style=\"padding-left: 8px;\">{$slot['compid']}</td>";
			$html .= "</tr>";
			if($cnt >= 15)
				break;
		}
	}
	$html .= "</table>";
	$data['html'] = $html;
	$cdata = array('slots' => $slots);
	sendJSON($data);
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn submitRequest()
///
/// \brief checks to see if the request can fit in the schedule; adds it if
/// it fits; notifies the user either way
///
////////////////////////////////////////////////////////////////////////////////
function submitRequest() {
	global $submitErr, $user, $HTMLheader, $mode, $printedHTMLheader;

	if($mode == 'submitTestProd') {
		$data = getContinuationVar();
		$data["revisionid"] = $_POST['revisionid'];
		# TODO check for valid revisionids for each image
		if(! empty($data["revisionid"])) {
			foreach($data['revisionid'] as $val) {
				foreach($val as $val2) {
					if(! is_numeric($val2) || $val2 < 0) {
						unset($data['revisionid']);
						break 2; // TODO make sure this breaks as far as needed
					}
				}
			}
		}
	}
	else {
		$data = processRequestInput(1);
	}
	$imaging = $data['imaging'];
	if($submitErr) {
		$printedHTMLheader = 1;
		print $HTMLheader;
		if($imaging)
			print "<H2>Create / Update an Image</H2>\n";
		else
			print "<H2>New Reservation</H2>\n";
		newReservation();
		print getFooter();
		return;
	}
	// if user attempts to make a reservation for an image he does not have
	//   access to, just make it for the first one he does have access to
	$resources = getUserResources(array("imageAdmin", "imageCheckOut"));
	$validImageids = array_keys($resources['image']);
	if(! in_array($data['imageid'], $validImageids))
		$data['imageid'] = array_shift($validImageids);

	$showrevisions = 0;
	$subimages = 0;
	$images = getImages();
	$revcount = count($images[$data['imageid']]['imagerevision']);
	if($revcount > 1)
		$showrevisions = 1;
	if($images[$data['imageid']]['imagemetaid'] != NULL &&
	   count($images[$data['imageid']]['subimages'])) {
		$subimages = 1;
		foreach($images[$data['imageid']]['subimages'] as $subimage) {
			$revcount = count($images[$subimage]['imagerevision']);
			if($revcount > 1)
				$showrevisions = 1;
		}
	}

	if($data["time"] == "now") {
		$nowArr = getdate();
		if($nowArr["minutes"] == 0) {
			$subtract = 0;
			$add = 0;
		}
		elseif($nowArr["minutes"] < 15) {
			$subtract = $nowArr["minutes"] * 60;
			$add = 900;
		}
		elseif($nowArr["minutes"] < 30) {
			$subtract = ($nowArr["minutes"] - 15) * 60;
			$add = 900;
		}
		elseif($nowArr["minutes"] < 45) {
			$subtract = ($nowArr["minutes"] - 30) * 60;
			$add = 900;
		}
		elseif($nowArr["minutes"] < 60) {
			$subtract = ($nowArr["minutes"] - 45) * 60;
			$add = 900;
		}
		$start = time() - $subtract;
		$start -= $start % 60;
		$nowfuture = "now";
	}
	else {
		$add = 0;
		$hour = $data["hour"];
		if($data["hour"] == 12) {
			if($data["meridian"] == "am") {
				$hour = 0;
			}
		}
		elseif($data["meridian"] == "pm") {
			$hour = $data["hour"] + 12;
		}

		$tmp = explode('/', $data["day"]);
		$start = mktime($hour, $data["minute"], "0", $tmp[0], $tmp[1], $tmp[2]);
		if($start < time()) {
			$printedHTMLheader = 1;
			print $HTMLheader;
			if($imaging)
				print "<H2>Create / Update an Image</H2>\n";
			else
				print "<H2>New Reservation</H2>\n";
			print "<font color=\"#ff0000\">The time you requested is in the past.";
			print " Please select \"Now\" or use a time in the future.</font><br>\n";
			$submitErr = 5000;
			newReservation();
			print getFooter();
			return;
		}
		$nowfuture = "future";
	}
	if($data["ending"] == "length")
		$end = $start + $data["length"] * 60 + $add;
	else {
		$end = datetimeToUnix($data["enddate"]);
		if($end % (15 * 60))
			$end = unixFloor15($end) + (15 * 60);
	}

	// get semaphore lock
	if(! semLock())
		abort(3);

	if(array_key_exists('revisionid', $data) &&
	   array_key_exists($data['imageid'], $data['revisionid']) &&
	   array_key_exists(0, $data['revisionid'][$data['imageid']])) {
		$revisionid = $data['revisionid'][$data['imageid']][0];
	}
	else
		$revisionid = getProductionRevisionid($data['imageid']);
	$availablerc = isAvailable($images, $data["imageid"], $revisionid, $start,
	                           $end, 0, 0, 0, $imaging);

	$max = getMaxOverlap($user['id']);
	if($availablerc != 0 && checkOverlap($start, $end, $max)) {
		$printedHTMLheader = 1;
		print $HTMLheader;
		if($imaging)
			print "<H2>Create / Update an Image</H2>\n";
		else
			print "<H2>New Reservation</H2>\n";
		if($max == 0) {
			print "<font color=\"#ff0000\">The time you requested overlaps with ";
			print "another reservation you currently have.  You are only allowed ";
			print "to have a single reservation at any given time. Please select ";
			print "another time to use the application. If you are finished with ";
			print "an active reservation, click \"Current Reservations\", ";
			print "then click the \"End\" button of your active reservation.";
			print "</font><br><br>\n";
		}
		else {
			print "<font color=\"#ff0000\">The time you requested overlaps with ";
			print "another reservation you currently have.  You are allowed ";
			print "to have $max overlapping reservations at any given time. ";
			print "Please select another time to use the application. If you are ";
			print "finished with an active reservation, click \"Current ";
			print "Reservations\", then click the \"End\" button of your active ";
			print "reservation.</font><br><br>\n";
		}
		$submitErr = 5000;
		newReservation();
		print getFooter();
		return;
	}
	// if user is owner of the image and there is a test version of the image
	#   available, ask user if production or test image desired
	if($mode != "submitTestProd" && $showrevisions &&
	   ($images[$data["imageid"]]["ownerid"] == $user["id"] || checkUserHasPerm('View Debug Information'))) {
		#unset($data["testprod"]);
		$printedHTMLheader = 1;
		print $HTMLheader;
		if($imaging)
			print "<H2>Create / Update an Image</H2>\n";
		else
			print "<H2>New Reservation</H2>\n";
		if($subimages) {
			print "This is a cluster environment. At least one image in the ";
			print "cluster has more than one version available. Please select ";
			print "the version you desire for each image listed below:<br>\n";
		}
		else {
			print "There are multiple versions of this environment available.  Please ";
			print "select the version you would like to check out:<br>\n";
		}
		print "<FORM action=\"" . BASEURL . SCRIPT . "\" method=post><br>\n";
		if(! array_key_exists('subimages', $images[$data['imageid']]))
			$images[$data['imageid']]['subimages'] = array();
		array_unshift($images[$data['imageid']]['subimages'], $data['imageid']);
		$cnt = 0;
		foreach($images[$data['imageid']]['subimages'] as $subimage) {
			print "{$images[$subimage]['prettyname']}:<br>\n";
			print "<table summary=\"lists versions of the selected environment, one must be selected to continue\">\n";
			print "  <TR>\n";
			print "    <TD></TD>\n";
			print "    <TH>Version</TH>\n";
			print "    <TH>Creator</TH>\n";
			print "    <TH>Created</TH>\n";
			print "    <TH>Currently in Production</TH>\n";
			print "  </TR>\n";
			foreach($images[$subimage]['imagerevision'] as $revision) {
				print "  <TR>\n";
				// if revision was selected or it wasn't selected but it is the production revision, show checked
				if((array_key_exists('revisionid', $data) &&
				   array_key_exists($subimage, $data['revisionid']) &&
					array_key_exists($cnt, $data['revisionid'][$subimage]) &&
				   $data['revisionid'][$subimage][$cnt] == $revisionid['id']) ||
				   $revision['production'])
					print "    <TD align=center><INPUT type=radio name=revisionid[$subimage][$cnt] value={$revision['id']} checked></TD>\n";
				else
					print "    <TD align=center><INPUT type=radio name=revisionid[$subimage][$cnt] value={$revision['id']}></TD>\n";
				print "    <TD align=center>{$revision['revision']}</TD>\n";
				print "    <TD align=center>{$revision['user']}</TD>\n";
				print "    <TD align=center>{$revision['prettydate']}</TD>\n";
				if($revision['production'])
					print "    <TD align=center>Yes</TD>\n";
				else
					print "    <TD align=center>No</TD>\n";
				print "  </TR>\n";
			}
			print "</table>\n";
			$cnt++;
		}
		$cont = addContinuationsEntry('submitTestProd', $data);
		print "<br><INPUT type=hidden name=continuation value=\"$cont\">\n";
		if($imaging)
			print "<INPUT type=submit value=\"Create Imaging Reservation\">\n";
		else
			print "<INPUT type=submit value=\"Create Reservation\">\n";
		print "</FORM>\n";
		print getFooter();
		return;
	}
	if($availablerc == -1) {
		$printedHTMLheader = 1;
		print $HTMLheader;
		if($imaging)
			print "<H2>Create / Update an Image</H2>\n";
		else
			print "<H2>New Reservation</H2>\n";
		print "You have requested an environment that is limited in the number ";
		print "of concurrent reservations that can be made. No further ";
		print "reservations for the environment can be made for the time you ";
		print "have selected. Please select another time to use the ";
		print "environment.<br>";
		addLogEntry($nowfuture, unixToDatetime($start), 
		            unixToDatetime($end), 0, $data["imageid"]);
		print getFooter();
	}
	elseif($availablerc > 0) {
		$requestid = addRequest($imaging, $data["revisionid"]);
		if($data["time"] == "now") {
			$cdata = array('lengthchanged' => $data['lengthchanged']);
			$cont = addContinuationsEntry('viewRequests', $cdata);
			header("Location: " . BASEURL . SCRIPT . "?continuation=$cont");
			return;
		}
		else {
			if($data["minute"] == 0)
				$data["minute"] = "00";
			$printedHTMLheader = 1;
			print $HTMLheader;
			if($imaging)
				print "<H2>Create / Update an Image</H2>\n";
			else
				print "<H2>New Reservation</H2>\n";
			if($data["ending"] == "length") {
				$time = prettyLength($data["length"]);
				if($data['testjavascript'] == 0 && $data['lengthchanged']) {
					print "<font color=red>NOTE: The maximum allowed reservation ";
					print "length for this environment is $time, and the length of ";
					print "this reservation has been adjusted accordingly.</font>\n";
					print "<br><br>\n";
				}
				print "Your request to use <b>" . $images[$data["imageid"]]["prettyname"];
				print "</b> on " . prettyDatetime($start) . " for $time has been ";
				print "accepted.<br><br>\n";
			}
			else {
				print "Your request to use <b>" . $images[$data["imageid"]]["prettyname"];
				print "</b> starting " . prettyDatetime($start) . " and ending ";
				print prettyDatetime($end) . " has been accepted.<br><br>\n";
			}
			print "When your reservation time has been reached, the <strong>";
			print "Current Reservations</strong> page will have further ";
			print "instructions on connecting to the reserved computer.  If you ";
			print "would like to modify your reservation, you can do that from ";
			print "the <b>Current Reservations</b> page as well.<br>\n";
			print getFooter();
		}
	}
	else {
		$cdata = array('imageid' => $data['imageid'],
		               'length' => $data['length'],
		               'showmessage' => 1,
		               'imaging' => $imaging);
		$cont = addContinuationsEntry('selectTimeTable', $cdata);
		addLogEntry($nowfuture, unixToDatetime($start), 
		            unixToDatetime($end), 0, $data["imageid"]);
		header("Location: " . BASEURL . SCRIPT . "?continuation=$cont");
		/*print "<H2>New Reservation</H2>\n";
		print "The reservation you have requested is not available. You may ";
		print "<a href=\"" . BASEURL . SCRIPT . "?continuation=$cont\">";
		print "view a timetable</a> of free and reserved times to find ";
		print "a time that will work for you.<br>\n";*/
	}
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn viewRequests
///
/// \brief prints user's reservations
///
////////////////////////////////////////////////////////////////////////////////
function viewRequests() {
	global $user, $inContinuation, $mode, $skin;
	if($inContinuation)
		$lengthchanged = getContinuationVar('lengthchanged', 0);
	else
		$lengthchanged = processInputVar('lengthchanged', ARG_NUMERIC, 0);
	$incPaneDetails = processInputVar('incdetails', ARG_NUMERIC, 0);
	$refreqid = processInputVar('reqid', ARG_NUMERIC, 0);
	$requests = getUserRequests("all");
	$images = getImages();
	$computers = getComputers();
	$resources = getUserResources(array("imageAdmin"));

	if(count($requests) == 0) {
		if($mode == 'AJviewRequests')
			print "document.body.style.cursor = 'default';";
		$text  = "<H2>Current Reservations</H2>";
		$text .= "You have no current reservations.<br>";
		if($mode == 'AJviewRequests')
			print(setAttribute('subcontent', 'innerHTML', $text));
		else
			print $text;
		return;
	}
	if($mode != 'AJviewRequests')
		print "<div id=subcontent>\n";

	$refresh = 0;
	$connect = 0;
	$failed = 0;

	$normal = '';
	$imaging = '';
	$long = '';
	$server = '';
	$reqids = array();
	if(checkUserHasPerm('View Debug Information'))
		$nodes = getManagementNodes();
	if($count = count($requests)) {
		# TODO display admin and login groups somewhere
		$now = time();
		for($i = 0, $failed = 0, $timedout = 0, $text = '', $showcreateimage = 0, $cluster = 0;
		   $i < $count;
		   $i++, $failed = 0, $timedout = 0, $text = '', $cluster = 0) {
			if($requests[$i]['forcheckout'] == 0 &&
			   $requests[$i]['forimaging'] == 0)
				continue;
			if(count($requests[$i]['reservations']))
				$cluster = 1;
			$cdata = array('requestid' => $requests[$i]['id']);
			$reqids[] = $requests[$i]['id'];
			$imageid = $requests[$i]["imageid"];
			$text .= "  <TR valign=top id=reqrow{$requests[$i]['id']}>\n";
			# TODO probably should display current status somewhere if checkpointing, rebooting, or reinstalling
			if(requestIsReady($requests[$i]) && $requests[$i]['useraccountready']) {
				$connect = 1;
				# request is ready, print Connect! and End buttons
				$text .= "    <TD>\n";
				$text .= "      <FORM action=\"" . BASEURL . SCRIPT . "\" method=post>\n";
				$cont = addContinuationsEntry('connectRequest', $cdata, SECINDAY);
				$text .= "      <INPUT type=hidden name=continuation value=\"$cont\">\n";
				$text .= "      <button type=submit dojoType=\"dijit.form.Button\">\n";
				$text .= "      Connect!\n";
				$text .= "      </button>\n";
				$text .= "      </FORM>\n";
				$text .= "    </TD>\n";
				if($requests[$i]['serveradmin']) {
					$text .= "    <TD>\n";
					$cont = addContinuationsEntry('AJconfirmDeleteRequest', $cdata, SECINDAY);
					$text .= "      <button dojoType=\"dijit.form.Button\">\n";
					$text .= "        Delete\n";
					$text .= "	      <script type=\"dojo/method\" event=\"onClick\">\n";
					$text .= "          endReservation('$cont');\n";
					$text .= "        </script>\n";
					$text .= "      </button>\n";
					$text .= "    </TD>\n";
				}
				else
					$text .= "    <TD></TD>\n";
				$startstamp = datetimeToUnix($requests[$i]["start"]);
			}
			elseif($requests[$i]["currstateid"] == 5) {
				# request has failed
				$text .= "    <TD nowrap>\n";
				$text .= "      <span class=scriptonly>\n";
				$text .= "      <span class=compstatelink>";
				$text .= "<a onClick=\"showResStatusPane({$requests[$i]['id']}); ";
				$text .= "return false;\" href=\"#\">Reservation failed</a></span>\n";
				$text .= "      </span>\n";
				$text .= "      <noscript>\n";
				$text .= "      <span class=scriptoff>\n";
				$text .= "      <span class=compstatelink>";
				$text .= "Reservation failed</span>\n";
				$text .= "      </span>\n";
				$text .= "      </noscript>\n";
				$text .= "    </TD>\n";
				if($requests[$i]['serveradmin']) {
					$text .= "    <TD>\n";
					$cont = addContinuationsEntry('AJconfirmRemoveRequest', $cdata, SECINDAY);
					$text .= "      <button dojoType=\"dijit.form.Button\">\n";
					$text .= "        Remove\n";
					$text .= "	      <script type=\"dojo/method\" event=\"onClick\">\n";
					$text .= "          removeReservation('$cont');\n";
					$text .= "        </script>\n";
					$text .= "      </button>\n";
					$text .= "    </TD>\n";
				}
				else
					$text .= "    <TD></TD>\n";
				$failed = 1;
			}
			elseif(datetimeToUnix($requests[$i]["start"]) < $now) {
				# other cases where the reservation start time has been reached
				if(($requests[$i]["currstateid"] == 12 &&
				   $requests[$i]['laststateid'] == 11) ||
					$requests[$i]["currstateid"] == 11 ||
					($requests[$i]["currstateid"] == 14 &&
					$requests[$i]["laststateid"] == 11)) {
					# request has timed out
					if($requests[$i]['forimaging'])
						$text .= "    <TD colspan=2>\n";
					else
						$text .= "    <TD>\n";
					$text .= "      <span class=compstatelink>Reservation has ";
					$text .= "timed out</span>\n";
					$timedout = 1;
					$text .= "    </TD>\n";
					if($requests[$i]['serveradmin']) {
						$text .= "    <TD>\n";
						$cont = addContinuationsEntry('AJconfirmRemoveRequest', $cdata, SECINDAY);
						$text .= "      <button dojoType=\"dijit.form.Button\">\n";
						$text .= "        Remove\n";
						$text .= "	      <script type=\"dojo/method\" event=\"onClick\">\n";
						$text .= "          removeReservation('$cont');\n";
						$text .= "        </script>\n";
						$text .= "      </button>\n";
						$text .= "    </TD>\n";
					}
					else
						$text .= "    <TD></TD>\n";
				}
				else {
					# computer is loading, print Pending... and Delete button
					# TODO figure out a different way to estimate for reboot and reinstall states
					# TODO if user account not ready, print accurate information in details
					$remaining = 1;
					if(isComputerLoading($requests[$i], $computers)) {
						if(datetimeToUnix($requests[$i]["daterequested"]) >=
						   datetimeToUnix($requests[$i]["start"])) {
							$startload = datetimeToUnix($requests[$i]["daterequested"]);
						}
						else {
							$startload = datetimeToUnix($requests[$i]["start"]);
						}
						$imgLoadTime = getImageLoadEstimate($imageid);
						if($imgLoadTime == 0)
							$imgLoadTime = $images[$imageid]['reloadtime'] * 60;
						$tmp = ($imgLoadTime - ($now - $startload)) / 60;
						$remaining = sprintf("%d", $tmp) + 1;
						if($remaining < 1) {
							$remaining = 1;
						}
					}
					$text .= "    <TD>\n";
					$text .= "      <span class=scriptonly>\n";
					$text .= "      <span class=compstatelink><i>";
					$text .= "<a onClick=\"showResStatusPane({$requests[$i]['id']}); ";
					$text .= "return false;\" href=\"#\">Pending...</a></i></span>\n";
					$text .= "      </span>\n";
					$text .= "      <noscript>\n";
					$text .= "      <span class=scriptoff>\n";
					$text .= "      <span class=compstatelink>";
					$text .= "<i>Pending...</i></span>\n";
					$text .= "      </span>\n";
					$text .= "      </noscript>\n";
					if($requests[$i]['currstateid'] != 26 &&
					   $requests[$i]['currstateid'] != 27 &&
					   $requests[$i]['currstateid'] != 28 &&
					   ($requests[$i]["currstateid"] != 14 ||
					   ($requests[$i]['laststateid'] != 26 &&
					    $requests[$i]['laststateid'] != 27 &&
					    $requests[$i]['laststateid'] != 28)))
						$text .= "<br>Est:&nbsp;$remaining&nbsp;min remaining\n";
					$refresh = 1;
					$text .= "    </TD>\n";
					if($requests[$i]['serveradmin']) {
						$text .= "    <TD>\n";
						$cont = addContinuationsEntry('AJconfirmDeleteRequest', $cdata, SECINDAY);
						$text .= "      <button dojoType=\"dijit.form.Button\">\n";
						$text .= "        Delete\n";
						$text .= "	      <script type=\"dojo/method\" event=\"onClick\">\n";
						$text .= "          endReservation('$cont');\n";
						$text .= "        </script>\n";
						$text .= "      </button>\n";
						$text .= "    </TD>\n";
					}
					else
						$text .= "    <TD></TD>\n";
				}
			}
			else {
				# reservation is in the future
				$text .= "    <TD></TD>\n";
				if($requests[$i]['serveradmin']) {
					$text .= "    <TD>\n";
					$cont = addContinuationsEntry('AJconfirmDeleteRequest', $cdata, SECINDAY);
					$text .= "      <button dojoType=\"dijit.form.Button\">\n";
					$text .= "        Delete\n";
					$text .= "	      <script type=\"dojo/method\" event=\"onClick\">\n";
					$text .= "          endReservation('$cont');\n";
					$text .= "        </script>\n";
					$text .= "      </button>\n";
					$text .= "    </TD>\n";
				}
				else
					$text .= "    <TD></TD>\n";
			}
			if(! $failed && ! $timedout) {
				# print edit button
				$editcont = addContinuationsEntry('AJeditRequest', $cdata, SECINDAY);
				$imgcont = addContinuationsEntry('startImage', $cdata, SECINDAY);
				$imgurl = BASEURL . SCRIPT . "?continuation=$imgcont";
				if($requests[$i]['serveradmin']) {
					$text .= "    <TD align=right>\n";
					$text .= "      <div dojoType=\"dijit.form.DropDownButton\">\n";
					$text .= "        <span>More Options...</span>\n";
					$text .= "        <div dojoType=\"dijit.Menu\">\n";
					$text .= "          <div dojoType=\"dijit.MenuItem\"\n";
					$text .= "               iconClass=\"noicon\"\n";
					$text .= "               label=\"Edit\"\n";
					$text .= "               onClick=\"editReservation('$editcont');\">\n";
					$text .= "          </div>\n";
					if(array_key_exists($imageid, $resources['image']) && ! $cluster &&            # imageAdmin access, not a cluster,
					   ($requests[$i]['currstateid'] == 8 || $requests[$i]['laststateid'] == 8)) { # reservation has been in inuse state
						$text .= "          <div dojoType=\"dijit.MenuItem\"\n";
						$text .= "               iconClass=\"noicon\"\n";
						$text .= "               label=\"End Reservation & Create Image\"\n";
						if($mode != 'AJviewRequests')
							$text .= "               onClick=\"window.location.href='$imgurl';\">\n";
						else
							$text .= "               onClick=\"window.location.href=\'$imgurl\';\">\n";
						$text .= "          </div>\n";
					}
					/*else {
						$text .= "          <div dojoType=\"dijit.MenuItem\"\n";
						$text .= "               iconClass=\"noicon\"\n";
						$text .= "               label=\"End Reservation & Create Image\" disabled>\n";
						$text .= "          </div>\n";
					}*/
					// todo uncomment the following when live imaging works
					// todo add a check to ensure it is a VM
					/*if($requests[$i]['server'] && ($requests[$i]['currstateid'] == 8 ||
						($requests[$i]['currstateid'] == 14 && $requests[$i]['laststateid'] == 8))) {
						$cont = addContinuationsEntry('startCheckpoint', $cdata, SECINDAY);
						$url = BASEURL . SCRIPT . "?continuation=$cont";
						$text .= "          <div dojoType=\"dijit.MenuItem\"\n";
						$text .= "               iconClass=\"noicon\"\n";
						$text .= "               label=\"Create Image\"\n";
						if($mode != 'AJviewRequests')
							$text .= "               onClick=\"window.location.href='$url';\">\n";
						else
							$text .= "               onClick=\"window.location.href=\'$url\';\">\n";
						$text .= "          </div>\n";
					}
					elseif($requests[$i]['server'] && $requests[$i]['currstateid'] == 24) {
						$text .= "          <div dojoType=\"dijit.MenuItem\"\n";
						$text .= "               iconClass=\"noicon\"\n";
						$text .= "               label=\"Create Image\" disabled>\n";
						$text .= "          </div>\n";
					}*/
					if(! $cluster &&
					   $requests[$i]['OSinstalltype'] != 'none' &&
					   $requests[$i]['currstateid'] != 13 &&
					   $requests[$i]['laststateid'] != 13 &&
					   $requests[$i]['currstateid'] != 24 &&
					   $requests[$i]['laststateid'] != 24 &&
					   $requests[$i]['currstateid'] != 16 &&
					   $requests[$i]['laststateid'] != 16 &&
					   /*$requests[$i]['currstateid'] != 26 && # TODO do we allow reboots again if already in reboot state?
					   $requests[$i]['laststateid'] != 26 &&
					   $requests[$i]['currstateid'] != 28 &&
						$requests[$i]['laststateid'] != 28 &&*/
					   $requests[$i]['currstateid'] != 27 &&
					   $requests[$i]['laststateid'] != 27) {
						$cont = addContinuationsEntry('AJrebootRequest', $cdata, SECINDAY);
						$text .= "          <div dojoType=\"dijit.MenuItem\"\n";
						$text .= "               iconClass=\"noicon\"\n";
						$text .= "               label=\"Reboot\">\n";
						$text .= "	          <script type=\"dojo/method\" event=\"onClick\">\n";
						$text .= "              rebootRequest('$cont');\n";
						$text .= "            </script>\n";
						$text .= "          </div>\n";
						$cont = addContinuationsEntry('AJreinstallRequest', $cdata, SECINDAY);
						$text .= "          <div dojoType=\"dijit.MenuItem\"\n";
						$text .= "               iconClass=\"noicon\"\n";
						$text .= "               label=\"Reinstall\">\n";
						$text .= "	          <script type=\"dojo/method\" event=\"onClick\">\n";
						$text .= "              reinstallRequest('$cont');\n";
						$text .= "            </script>\n";
						$text .= "          </div>\n";
					}
					else {
						$text .= "          <div dojoType=\"dijit.MenuItem\"\n";
						$text .= "               iconClass=\"noicon\"\n";
						$text .= "               label=\"Reboot\" disabled>\n";
						$text .= "          </div>\n";
						$text .= "          <div dojoType=\"dijit.MenuItem\"\n";
						$text .= "               iconClass=\"noicon\"\n";
						$text .= "               label=\"Reinstall\" disabled>\n";
						$text .= "          </div>\n";
					}
					$text .= "       </div>\n";
					$text .= "     </div>\n";
					$text .= "    </TD>\n";
				}
				else
					$text .= "    <TD></TD>\n";
			}
			else
				$text .= "    <TD></TD>\n";

			# print name of image, add (Testing) if it is the test version of an image
			$text .= "    <TD>" . str_replace("'", "&#39;", $requests[$i]["prettyimage"]);
			if($requests[$i]["test"])
				$text .= " (Testing)";
			$text .= "</TD>\n";

			# print start time
			if(datetimeToUnix($requests[$i]["start"]) < 
			   datetimeToUnix($requests[$i]["daterequested"])) {
				$text .= "    <TD>" . prettyDatetime($requests[$i]["daterequested"], 1) . "</TD>\n";
			}
			else {
				$text .= "    <TD>" . prettyDatetime($requests[$i]["start"], 1) . "</TD>\n";
			}

			# print end time
			if($requests[$i]['server'] && $requests[$i]['end'] == '2038-01-01 00:00:00')
				$text .= "    <TD>(none)</TD>\n";
			else
				$text .= "    <TD>" . prettyDatetime($requests[$i]["end"], 1) . "</TD>\n";

			# print date requested
			$text .= "    <TD>" . prettyDatetime($requests[$i]["daterequested"], 1) . "</TD>\n";

			if(checkUserHasPerm('View Debug Information')) {
				if(! is_null($requests[$i]['vmhostid'])) {
					$query = "SELECT c.hostname "
					       . "FROM computer c, " 
					       .      "vmhost v "
					       . "WHERE v.id = {$requests[$i]['vmhostid']} AND "
					       .       "v.computerid = c.id";
					$qh = doQuery($query, 101);
					$row = mysql_fetch_assoc($qh);
					$vmhost = $row['hostname'];
				}
				$text .= "    <TD align=center><span id=\"req{$requests[$i]['id']}\">";
				$text .= "{$requests[$i]["id"]}</span>\n";
				$text .= "<div dojoType=\"vcldojo.HoverTooltip\" connectId=\"req{$requests[$i]['id']}\">";
				$text .= "Mgmt node: {$nodes[$requests[$i]["managementnodeid"]]['hostname']}<br>\n";
				$text .= "Computer ID: {$requests[$i]['computerid']}<br>\n";
				$text .= "Comp hostname: {$computers[$requests[$i]["computerid"]]["hostname"]}<br>\n";
				$text .= "Comp IP: {$requests[$i]["IPaddress"]}<br>\n";
				$text .= "Comp State ID: {$computers[$requests[$i]["computerid"]]["stateid"]}<br>\n";
				$text .= "Comp Type: {$requests[$i]['comptype']}<br>\n";
				if(! is_null($requests[$i]['vmhostid']))
					$text .= "VM Host: $vmhost<br>\n";
				$text .= "Current State ID: {$requests[$i]["currstateid"]}<br>\n";
				$text .= "Last State ID: {$requests[$i]["laststateid"]}<br>\n";
				$text .= "</div></TD>\n";
			}
			$text .= "  </TR>\n";
			if($requests[$i]['server'])
				$server .= $text;
			elseif($requests[$i]['forimaging'])
				$imaging .= $text;
			elseif($requests[$i]['longterm'])
				$long .= $text;
			else
				$normal .= $text;
		}
	}

	$text = "<H2>Current Reservations</H2>\n";
	if(! empty($normal)) {
		if(! empty($imaging) || ! empty($long))
			$text .= "You currently have the following <strong>normal</strong> reservations:<br>\n";
		else
			$text .= "You currently have the following normal reservations:<br>\n";
		if($lengthchanged) {
			$text .= "<font color=red>NOTE: The maximum allowed reservation ";
			$text .= "length for one of these reservations was less than the ";
			$text .= "length you submitted, and the length of that reservation ";
			$text .= "has been adjusted accordingly.</font>\n";
		}
		$text .= "<table id=reslisttable summary=\"lists reservations you currently have\" cellpadding=5>\n";
		$text .= "  <TR>\n";
		$text .= "    <TD colspan=3></TD>\n";
		$text .= "    <TH>Environment</TH>\n";
		$text .= "    <TH>Starting</TH>\n";
		$text .= "    <TH>Ending</TH>\n";
		$text .= "    <TH>Initially requested</TH>\n";
		if(checkUserHasPerm('View Debug Information'))
			$text .= "    <TH>Req ID</TH>\n";
		$text .= "  </TR>\n";
		$text .= $normal;
		$text .= "</table>\n";
	}
	if(! empty($imaging)) {
		if(! empty($normal))
			$text .= "<hr>\n";
		$text .= "You currently have the following <strong>imaging</strong> reservations:<br>\n";
		$text .= "<table id=imgreslisttable summary=\"lists imaging reservations you currently have\" cellpadding=5>\n";
		$text .= "  <TR>\n";
		$text .= "    <TD colspan=3></TD>\n";
		$text .= "    <TH>Environment</TH>\n";
		$text .= "    <TH>Starting</TH>\n";
		$text .= "    <TH>Ending</TH>\n";
		$text .= "    <TH>Initially requested</TH>\n";
		$computers = getComputers();
		if(checkUserHasPerm('View Debug Information'))
			$text .= "    <TH>Req ID</TH>\n";
		$text .= "  </TR>\n";
		$text .= $imaging;
		$text .= "</table>\n";
	}
	if(! empty($long)) {
		if(! empty($normal) || ! empty($imaging))
			$text .= "<hr>\n";
		$text .= "You currently have the following <strong>long term</strong> reservations:<br>\n";
		$text .= "<table id=\"longreslisttable\" summary=\"lists long term reservations you currently have\" cellpadding=5>\n";
		$text .= "  <TR>\n";
		$text .= "    <TD colspan=3></TD>\n";
		$text .= "    <TH>Environment</TH>\n";
		$text .= "    <TH>Starting</TH>\n";
		$text .= "    <TH>Ending</TH>\n";
		$text .= "    <TH>Initially requested</TH>\n";
		$computers = getComputers();
		if(checkUserHasPerm('View Debug Information'))
			$text .= "    <TH>Req ID</TH>\n";
		$text .= "  </TR>\n";
		$text .= $long;
		$text .= "</table>\n";
	}
	if(! empty($server)) {
		if(! empty($normal) || ! empty($imaging) || ! empty($long))
			$text .= "<hr>\n";
		$text .= "You currently have the following <strong>server</strong> reservations:<br>\n";
		$text .= "<table id=\"longreslisttable\" summary=\"lists server reservations you currently have\" cellpadding=5>\n";
		$text .= "  <TR>\n";
		$text .= "    <TD colspan=3></TD>\n";
		$text .= "    <TH>Environment</TH>\n";
		$text .= "    <TH>Starting</TH>\n";
		$text .= "    <TH>Ending</TH>\n";
		$text .= "    <TH>Initially requested</TH>\n";
		$computers = getComputers();
		if(checkUserHasPerm('View Debug Information'))
			$text .= "    <TH>Req ID</TH>\n";
		$text .= "  </TR>\n";
		$text .= $server;
		$text .= "</table>\n";
	}

	# connect div
	if($connect) {
		$text .= "<br><br>Click the <strong>";
		$text .= "Connect!</strong> button to get further ";
		$text .= "information about connecting to the reserved system. You must ";
		$text .= "click the button from a web browser running on the same computer ";
		$text .= "from which you will be connecting to the remote computer; ";
		$text .= "otherwise, you may be denied access to the machine.\n";
	}

	if($refresh) {
		$text .= "<br><br>This page will automatically update ";
		$text .= "every 20 seconds until the <font color=red><i>Pending...</i>";
		$text .= "</font> reservation is ready.\n";
	}

	if($failed) {
		$text .= "<br><br>An error has occurred that has kept one of your reservations ";
		$text .= "from being processed. We apologize for any inconvenience ";
		$text .= "this may have caused.\n";
	}

	$cont = addContinuationsEntry('AJviewRequests', array(), SECINDAY);
	$text .= "<INPUT type=hidden id=resRefreshCont value=\"$cont\">\n";

	$text .= "</div>\n";
	if($mode != 'AJviewRequests') {
		$text .= "<div dojoType=dojox.layout.FloatingPane\n";
		$text .= "      id=resStatusPane\n";
		$text .= "      resizable=true\n";
		$text .= "      closable=true\n";
		$text .= "      title=\"Detailed Reservation Status\"\n";
		$text .= "      style=\"width: 350px; ";
		$text .=               "height: 280px; ";
		$text .=               "position: absolute; ";
		$text .=               "left: 0px; ";
		$text .=               "top: 0px; ";
		$text .=               "visibility: hidden; ";
		$text .=               "border: solid 1px #7EABCD;\"\n";
		$text .= ">\n";
		$text .= "<script type=\"dojo/method\" event=minimize>\n";
		$text .= "  this.hide();\n";
		$text .= "</script>\n";
		$text .= "<script type=\"dojo/method\" event=close>\n";
		$text .= "  this.hide();\n";
		$text .= "  return false;\n";
		$text .= "</script>\n";
		$text .= "<div id=resStatusText></div>\n";
		$text .= "<input type=hidden id=detailreqid value=0>\n";
		$text .= "</div>\n";

		$text .= "<div dojoType=dijit.Dialog\n";
		$text .= "      id=\"endResDlg\"\n";
		$text .= "      title=\"Delete Reservation\"\n";
		$text .= "      duration=250\n";
		$text .= "      draggable=true>\n";
		#$text .= "	  <script type=\"dojo/connect\" event=onCancel>\n";
		#$text .= "      endResDlgHide();\n";
		#$text .= "    </script>\n";
		$text .= "   <div id=\"endResDlgContent\"></div>\n";
		$text .= "   <input type=\"hidden\" id=\"endrescont\">\n";
		$text .= "   <input type=\"hidden\" id=\"endresid\">\n";
		$text .= "   <div align=\"center\">\n";
		$text .= "   <button id=\"endResDlgBtn\" dojoType=\"dijit.form.Button\">\n";
		$text .= "     Delete Reservation\n";
		$text .= "	   <script type=\"dojo/method\" event=\"onClick\">\n";
		$text .= "       submitDeleteReservation();\n";
		$text .= "     </script>\n";
		$text .= "   </button>\n";
		$text .= "   <button dojoType=\"dijit.form.Button\">\n";
		$text .= "     Cancel\n";
		$text .= "	   <script type=\"dojo/method\" event=\"onClick\">\n";
		$text .= "       dijit.byId('endResDlg').hide();\n";
		$text .= "       dojo.byId('endResDlgContent').innerHTML = '';\n";
		$text .= "     </script>\n";
		$text .= "   </button>\n";
		$text .= "   </div>\n";
		$text .= "</div>\n";

		$text .= "<div dojoType=dijit.Dialog\n";
		$text .= "      id=\"remResDlg\"\n";
		$text .= "      title=\"Remove Reservation\"\n";
		$text .= "      duration=250\n";
		$text .= "      draggable=true>\n";
		$text .= "   <div id=\"remResDlgContent\"></div>\n";
		$text .= "   <input type=\"hidden\" id=\"remrescont\">\n";
		$text .= "   <div align=\"center\">\n";
		$text .= "   <button id=\"remResDlgBtn\" dojoType=\"dijit.form.Button\">\n";
		$text .= "     Remove Reservation\n";
		$text .= "	   <script type=\"dojo/method\" event=\"onClick\">\n";
		$text .= "       submitRemoveReservation();\n";
		$text .= "     </script>\n";
		$text .= "   </button>\n";
		$text .= "   <button dojoType=\"dijit.form.Button\">\n";
		$text .= "     Cancel\n";
		$text .= "	   <script type=\"dojo/method\" event=\"onClick\">\n";
		$text .= "       dijit.byId('remResDlg').hide();\n";
		$text .= "       dojo.byId('remResDlgContent').innerHTML = '';\n";
		$text .= "     </script>\n";
		$text .= "   </button>\n";
		$text .= "   </div>\n";
		$text .= "</div>\n";

		$text .= "<div dojoType=dijit.Dialog\n";
		$text .= "      id=\"editResDlg\"\n";
		$text .= "      title=\"Modify Reservation\"\n";
		$text .= "      duration=250\n";
		$text .= "      draggable=true>\n";
		$text .= "	  <script type=\"dojo/connect\" event=onHide>\n";
		$text .= "      hideEditResDlg();\n";
		$text .= "    </script>\n";
		$text .= "   <div id=\"editResDlgContent\"></div>\n";
		$text .= "   <input type=\"hidden\" id=\"editrescont\">\n";
		$text .= "   <input type=\"hidden\" id=\"editresid\">\n";
		$text .= "   <div id=\"editResDlgErrMsg\" class=\"rederrormsg\"></div>\n";
		$text .= "   <div align=\"center\">\n";
		$text .= "   <button id=\"editResDlgBtn\" dojoType=\"dijit.form.Button\">\n";
		$text .= "     Modify Reservation\n";
		$text .= "	   <script type=\"dojo/method\" event=\"onClick\">\n";
		$text .= "       submitEditReservation();\n";
		$text .= "     </script>\n";
		$text .= "   </button>\n";
		$text .= "   <button dojoType=\"dijit.form.Button\" id=\"editResCancelBtn\">\n";
		$text .= "     Cancel\n";
		$text .= "	   <script type=\"dojo/method\" event=\"onClick\">\n";
		$text .= "       dijit.byId('editResDlg').hide();\n";
		$text .= "     </script>\n";
		$text .= "   </button>\n";
		$text .= "   </div>\n";
		$text .= "</div>\n";

		$text .= "<div dojoType=dijit.Dialog\n";
		$text .= "      id=\"rebootreinstalldlg\"\n";
		$text .= "      title=\"Reboot Reservation\"\n";
		$text .= "      duration=250\n";
		$text .= "      draggable=true>\n";
		$text .= "	  <script type=\"dojo/connect\" event=onHide>\n";
		$text .= "      hideRebReinstResDlg();\n";
		$text .= "    </script>\n";
		$text .= "   <div id=\"rebreinstResDlgContent\"></div>\n";
		$text .= "   <div id=\"rebootRadios\" style=\"margin-left: 90px;\">\n";
		$text .= "   <input type=\"radio\" name=\"reboottype\" id=\"softreboot\" checked>\n";
		$text .= "   <label for=\"softreboot\">Soft Reboot</label><br>\n";
		$text .= "   <input type=\"radio\" name=\"reboottype\" id=\"hardreboot\">\n";
		$text .= "   <label for=\"hardreboot\">Hard Reboot</label><br><br>\n";
		$text .= "   </div>\n";
		$text .= "   <input type=\"hidden\" id=\"rebreinstrescont\">\n";
		#$text .= "   <input type=\"hidden\" id=\"rebreinstresid\">\n";
		$text .= "   <div id=\"rebreinstResDlgErrMsg\" class=\"rederrormsg\"></div>\n";
		$text .= "   <div align=\"center\">\n";
		$text .= "   <button id=\"rebreinstResDlgBtn\" dojoType=\"dijit.form.Button\">\n";
		$text .= "     Reboot Reservation\n";
		$text .= "	   <script type=\"dojo/method\" event=\"onClick\">\n";
		$text .= "       submitRebReinstReservation();\n";
		$text .= "     </script>\n";
		$text .= "   </button>\n";
		$text .= "   <button dojoType=\"dijit.form.Button\" id=\"rebreinstResCancelBtn\">\n";
		$text .= "     Cancel\n";
		$text .= "	   <script type=\"dojo/method\" event=\"onClick\">\n";
		$text .= "       dijit.byId('rebootreinstalldlg').hide();\n";
		$text .= "     </script>\n";
		$text .= "   </button>\n";
		$text .= "   </div>\n";
		$text .= "</div>\n";

		$text .= "<div dojoType=dijit.Dialog\n";
		$text .= "      id=\"suggestedTimes\"\n";
		$text .= "      title=\"Available Times\"\n";
		$text .= "      duration=250\n";
		$text .= "      draggable=true>\n";
		$text .= "   <div id=\"suggestloading\" style=\"text-align: center\">";
		$text .= "<img src=\"themes/$skin/css/dojo/images/loading.gif\" ";
		$text .= "style=\"vertical-align: middle;\"> Loading...</div>\n";
		$text .= "   <div id=\"suggestContent\"></div>\n";
		$text .= "   <input type=\"hidden\" id=\"suggestcont\">\n";
		$text .= "   <input type=\"hidden\" id=\"selectedslot\">\n";
		$text .= "   <div align=\"center\">\n";
		$text .= "   <button id=\"suggestDlgBtn\" dojoType=\"dijit.form.Button\" disabled>\n";
		$text .= "     Use Selected Time\n";
		$text .= "	   <script type=\"dojo/method\" event=\"onClick\">\n";
		$text .= "       useSuggestedEditSlot();\n";
		$text .= "     </script>\n";
		$text .= "   </button>\n";
		$text .= "   <button id=\"suggestDlgCancelBtn\" dojoType=\"dijit.form.Button\">\n";
		$text .= "     Cancel\n";
		$text .= "	   <script type=\"dojo/method\" event=\"onClick\">\n";
		$text .= "       dijit.byId('suggestDlgBtn').set('disabled', true);\n";
		$text .= "       dojo.removeClass('suggestDlgBtn', 'hidden');\n";
		$text .= "       showDijitButton('suggestDlgBtn');\n";
		$text .= "       dijit.byId('suggestDlgCancelBtn').set('label', 'Cancel');\n";
		$text .= "       dijit.byId('suggestedTimes').hide();\n";
		$text .= "       dojo.byId('suggestContent').innerHTML = '';\n";
		$text .= "     </script>\n";
		$text .= "   </button>\n";
		$text .= "   </div>\n";
		$text .= "</div>\n";

		print $text;
	}
	else {
		$text = str_replace("\n", ' ', $text);
		$text = str_replace("('", "(\'", $text);
		$text = str_replace("')", "\')", $text);
		print "document.body.style.cursor = 'default';";
		if($refresh)
			print "refresh_timer = setTimeout(resRefresh, 20000);\n";
		print(setAttribute('subcontent', 'innerHTML', $text));
		print "AJdojoCreate('subcontent');";
		if($incPaneDetails) {
			$text = detailStatusHTML($refreqid);
			print(setAttribute('resStatusText', 'innerHTML', $text));
		}
		print "checkResGone(" . json_encode($reqids) . ");";
		return;
	}
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn detailStatusHTML($reqid)
///
/// \param $reqid - a request id
///
/// \return html text showing detailed status from computerloadlog for specified
/// request
///
/// \brief gathers information about the state flow for $reqid and formats it
/// nicely for a user to view
///
////////////////////////////////////////////////////////////////////////////////
function detailStatusHTML($reqid) {
	$requests = getUserRequests("all");
	$found = 0;
	foreach($requests as $request) {
		if($request['id'] == $reqid) {
			$found = 1;
			break;
		}
	}
	if(! $found) {
		$text  = "The selected reservation is no longer available.  Go to ";
		$text .= "<a href=" . BASEURL . SCRIPT . "?mode=newRequest>New ";
		$text .= "Reservations</a> to request a new reservation or ";
		$text .= "select another one that is available.";
		return $text;
	}
	if($request['imageid'] == $request['compimageid'])
		$nowreq = 1;
	else
		$nowreq = 0;
	$flow = getCompStateFlow($request['computerid']);

	# cluster reservations not supported here yet
	# info on reboots/reinstalls not available yet
	if(empty($flow) ||
		count($request['reservations']) > 0 ||
		($request['currstateid'] == 14 && $request['laststateid'] == 26) ||
		/*($request['currstateid'] == 14 && $request['laststateid'] == 27) ||*/
		($request['currstateid'] == 14 && $request['laststateid'] == 28)) {
		$noinfo =  "No detailed loading information is available for this ";
		$noinfo .= "reservation.";
		return $noinfo;
	}

	$logdata = getCompLoadLog($request['resid']);

	# determine an estimated load time for the image
	$imgLoadTime = getImageLoadEstimate($request['imageid']);
	if($imgLoadTime == 0) {
		$images = getImages(0, $request['imageid']);
		$imgLoadTime = $images[$request['imageid']]['reloadtime'] * 60;
	}
	$time = 0;
	$now = time();
	$text = "<table summary=\"displays a list of states the reservation must "
	      . "go through to become ready and how long each state will take or "
			. "has already taken\" id=resStatusTable>";
	$text .= "<colgroup>";
	$text .= "<col class=resStatusColState />";
	$text .= "<col class=resStatusColEst />";
	$text .= "<col class=resStatusColTotal />";
	$text .= "</colgroup>";
	$text .= "<tr>";
	$text .= "<th align=right><br>State</th>";
	$text .= "<th>Est/Act<br>Time</th>";
	$text .= "<th>Total<br>Time</th>";
	$text .= "</tr>";

	$slash = "<font color=black>/</font>";
	$total = 0;
	$id = "";
	$last = array();
	$logstateids = array();
	$skippedstates = array();
	# loop through all states in the log data
	foreach($logdata as $data) {
		# keep track of the states in the log data
		array_push($logstateids, $data['loadstateid']);
		# keep track of any skipped states
		if(! empty($last) &&
			$last['loadstateid'] != $flow['repeatid'] &&
		   $data['loadstateid'] != $flow['data'][$last['loadstateid']]['nextstateid']) {
			array_push($skippedstates, $flow['data'][$last['loadstateid']]['nextstateid']);
		}
		// if we reach a repeat state, include a message about having to go back
		if($data['loadstateid'] == $flow['repeatid']) {
			if(empty($id))
				return $noinfo;
			$text .= "<tr>";
			$text .= "<td colspan=3><hr>problem at state ";
			$text .= "\"{$flow['data'][$id]['nextstate']}\"";
			$query = "SELECT additionalinfo "
			       . "FROM computerloadlog "
			       . "WHERE loadstateid = {$flow['repeatid']} AND "
			       .       "reservationid = {$request['resid']} AND "
			       .       "timestamp = '" . unixToDatetime($data['ts']) . "'";
			$qh = doQuery($query, 101);
			if($row = mysql_fetch_assoc($qh)) {
				$reason = $row['additionalinfo'];
				$text .= "<br>retrying at state \"$reason\"";
			}
			$text .= "<hr></td></tr>";
			$total += $data['time'];
			$last = $data;
			continue;
		}
		$id = $data['loadstateid'];
		// if in post config state, compute estimated time for the state
		if($flow['data'][$id]['statename'] == 'loadimagecomplete') {
			$addtime = 0;
			foreach($skippedstates as $stateid)
				$addtime += $flow['data'][$stateid]['statetime'];
			# this state's time is (avg image load time - all other states time +
			#                       state time for any skipped states)
			$tmp = $imgLoadTime - $flow['totaltime'] + $addtime;
			if($tmp < 0)
				$flow['data'][$id]['statetime'] = 0;
			else
				$flow['data'][$id]['statetime'] = $tmp;
		}
		$total += $data['time'];
		$text .= "<tr>";
		$text .= "<td nowrap align=right><font color=green>";
		$text .= "{$flow['data'][$id]['state']}($id)</font></td>";
		$text .= "<td nowrap align=center><font color=green>";
		$text .= secToMinSec($flow['data'][$id]['statetime']) . $slash;
		$text .= secToMinSec($data['time']) . "</font></td>";
		$text .= "<td nowrap align=center><font color=green>";
		$text .= secToMinSec($total) . "</font></td>";
		$text .= "</tr>";
		$last = $data;
	}
	# $id will be set if there was log data, use the first state in the flow
	//    if it isn't set
	if(! empty($id))
		$id = $flow['nextstates'][$id];
	else
		$id = $flow['stateids'][0];

	# determine any skipped states
	$matchingstates = array();
	foreach($flow['stateids'] as $stateid) {
		if($stateid == $id)
			break;
		array_push($matchingstates, $stateid);
	}
	$skippedstates = array_diff($matchingstates, $logstateids);
	$addtime = 0;
	foreach($skippedstates as $stateid)
		$addtime += $flow['data'][$stateid]['statetime'];

	$first = 1;
	$count = 0;
	# loop through the states in the flow that haven't been reached yet
	# $count is included to protect against an infinite loop
	while(! is_null($id) && $count < 100) {
		$count++;
		// if in post config state, compute estimated time for the state
		if($flow['data'][$id]['statename'] == 'loadimagecomplete') {
			# this state's time is (avg image load time - all other states time +
			#                       state time for any skipped states)
			$tmp = $imgLoadTime - $flow['totaltime'] + $addtime;
			if($tmp < 0)
				$flow['data'][$id]['statetime'] = 0;
			else
				$flow['data'][$id]['statetime'] = $tmp;
		}
		// if first time through this loop, this is the current state
		if($first) {
			// if request has failed, it was during this state, get reason
			if($request['currstateid'] == 5) {
				$query = "SELECT additionalInfo, "
				       .        "UNIX_TIMESTAMP(timestamp) AS ts "
				       . "FROM computerloadlog "
				       . "WHERE loadstateid = (SELECT id "
				       .                      "FROM computerloadstate "
				       .                      "WHERE loadstatename = 'failed') AND "
				       . "reservationid = {$request['resid']} "
				       . "ORDER BY id "
				       . "LIMIT 1";
				$qh = doQuery($query, 101);
				if($row = mysql_fetch_assoc($qh)) {
					$reason = $row['additionalInfo'];
					if(! empty($data))
						$currtime = $row['ts'] - $data['ts'];
					else
						$currtime = $row['ts'] -
						            datetimeToUnix($request['daterequested']);
				}
				else {
					$text  = "No detailed information is available for this ";
					$text .= "reservation.";
					return $text;
				}
				$text .= "<tr>";
				$text .= "<td nowrap align=right><font color=red>";
				$text .= "{$flow['data'][$id]['state']}($id)</font></td>";
				$text .= "<td nowrap align=center><font color=red>";
				$text .= secToMinSec($flow['data'][$id]['statetime']);
				$text .= $slash . secToMinSec($currtime) . "</font></td>";
				$text .= "<td nowrap align=center><font color=red>";
				$text .= secToMinSec($total + $currtime) . "</font></td>";
				$text .= "</tr>";
				$text .= "</table>";
				if(strlen($reason))
					$text .= "<br><font color=red>failed: $reason</font>";
				return $text;
			}
			# otherwise add text about current state
			else {
				if(! empty($data))
					$currtime = $now - $data['ts'];
				else
					$currtime = $now - datetimeToUnix($request['daterequested']);
				$text .= "<td nowrap align=right><font color=#CC8500>";
				$text .= "{$flow['data'][$id]['state']}($id)</font></td>";
				$text .= "<td nowrap align=center><font color=#CC8500>";
				$text .= secToMinSec($flow['data'][$id]['statetime']);
				$text .= $slash . secToMinSec($currtime) . "</font></td>";
				$text .= "<td nowrap align=center><font color=#CC8500>";
				$text .= secToMinSec($total + $currtime) . "</font></td>";
				$text .= "</tr>";
				$first = 0;
			}
		}
		# add text about future states
		else {
			$text .= "<td nowrap align=right>{$flow['data'][$id]['state']}($id)";
			$text .= "</td>";
			$text .= "<td nowrap align=center>";
			$text .= secToMinSec($flow['data'][$id]['statetime']) . "</td>";
			$text .= "<td></td>";
			$text .= "</tr>";
		}
		$id = $flow['nextstates'][$id];
	}
	$text .= "</table>";
	return $text;
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn viewRequestInfo()
///
/// \brief prints a page with information about a specific request
///
////////////////////////////////////////////////////////////////////////////////
function viewRequestInfo() {
	$requestid = getContinuationVar('requestid');
	$request = getRequestInfo($requestid);
	if($request['forimaging'] || $request['stateid'] == 18 || $request['laststateid'] == 18)
		$reservation = $request['reservations'][0];
	else {
		foreach($request["reservations"] as $res) {
			if($res["forcheckout"]) {
				$reservation = $res;
				break;
			}
		}
	}
	$states = getStates();
	$userinfo = getUserInfo($request["userid"], 1, 1);
	print "<DIV align=center>\n";
	print "<H2>View Reservation</H2>\n";
	print "<table summary=\"\">\n";
	print "  <TR>\n";
	print "    <TH align=right>User:</TH>\n";
	print "    <TD>" . $userinfo["unityid"] . "</TD>\n";
	print "  </TR>\n";
	print "  <TR>\n";
	print "    <TH align=right>Start&nbsp;Time:</TH>\n";
	if(datetimeToUnix($request["start"]) < 
	   datetimeToUnix($request["daterequested"])) {
		print "    <TD>" . prettyDatetime($request["daterequested"]) . "</TD>\n";
	}
	else {
		print "    <TD>" . prettyDatetime($request["start"]) . "</TD>\n";
	}
	print "  </TR>\n";
	print "  <TR>\n";
	print "    <TH align=right>End&nbsp;Time:</TH>\n";
	print "    <TD>" . prettyDatetime($request["end"]) . "</TD>\n";
	print "  </TR>\n";
	print "  <TR>\n";
	print "    <TH align=right>Request&nbsp;Time:</TH>\n";
	print "    <TD>" . prettyDatetime($request["daterequested"]) . "</TD>\n";
	print "  </TR>\n";
	print "  <TR>\n";
	print "    <TH align=right>Last&nbsp;Modified:</TH>\n";
	if(! empty($request["datemodified"])) {
		print "    <TD>" . prettyDatetime($request["datemodified"]) . "</TD>\n";
	}
	else {
		print "    <TD>Never Modified</TD>\n";
	}
	print "  </TR>\n";
	print "  <TR>\n";
	print "    <TH align=right>Current&nbsp;State:</TH>\n";
	print "    <TD>" . $states[$request["stateid"]] . "</TD>\n";
	print "  </TR>\n";
	print "  <TR>\n";
	print "    <TH align=right>Last&nbsp;State:</TH>\n";
	print "    <TD>";
	if($request["laststateid"]) {
		print $states[$request["laststateid"]];
	}
	else {
		print "None";
	}
	print "</TD>\n";
	print "  </TR>\n";
	print "  <TR>\n";
	print "    <TH align=right>Image:</TH>\n";
	print "    <TD>{$reservation['prettyimage']}</TD>\n";
	print "  </TR>\n";
	print "  <TR>\n";
	print "    <TH align=right>Hostname:</TH>\n";
	print "    <TD>{$request['reservations'][0]["hostname"]}</TD>\n";
	print "  </TR>\n";
	print "  <TR>\n";
	print "    <TH align=right>IP&nbsp;Address:</TH>\n";
	print "    <TD>{$request['reservations'][0]["reservedIP"]}</TD>\n";
	print "  </TR>\n";
	print "</table>\n";
	if(count($request['reservations'] > 1)) {
		array_shift($request['reservations']);
		print "Subimages:<br>\n";
		print "<table summary=\"\">\n";
		foreach($request["reservations"] as $res) {
			print "  <TR>\n";
			print "    <TH align=right>Image:</TH>\n";
			print "    <TD>{$res["prettyimage"]}</TD>\n";
			print "  </TR>\n";
			print "  <TR>\n";
			print "    <TH align=right>Hostname:</TH>\n";
			print "    <TD>{$res["hostname"]}</TD>\n";
			print "  </TR>\n";
			print "  <TR>\n";
			print "    <TH align=right>IP&nbsp;Address:</TH>\n";
			print "    <TD>{$res["reservedIP"]}</TD>\n";
			print "  </TR>\n";
		}
		print "</table>\n";
	}
	print "<table summary=\"\">\n";
	print "  <TR>\n";
	/*print "    <TD>\n";
	print "      <FORM action=\"" . BASEURL . SCRIPT . "\" method=post>\n";
	print "      <INPUT type=hidden name=mode value=adminEditRequest>\n";
	print "      <INPUT type=hidden name=requestid value=$requestid>\n";
	print "      <INPUT type=submit value=Modify>\n";
	print "      </FORM>\n";
	print "    </TD>\n";*/
	print "    <TD>\n";
	$cdata = array('requestid' => $requestid,
	               'notbyowner' => 1,
	               'ttdata' => getContinuationVar('ttdata'),
	               'fromtimetable' => 1);
	$cont = addContinuationsEntry('AJconfirmDeleteRequest', $cdata, SECINDAY);
	print "      <button dojoType=\"dijit.form.Button\">\n";
	print "        Delete Reservation\n";
	print "	      <script type=\"dojo/method\" event=\"onClick\">\n";
	print "          endReservation('$cont');\n";
	print "        </script>\n";
	print "      </button>\n";
	print "    </TD>\n";
	print "  </TR>\n";
	print "</table>\n";
	print "</DIV>\n";

	print "<div dojoType=dijit.Dialog\n";
	print "      id=\"endResDlg\"\n";
	print "      title=\"Delete Reservation\"\n";
	print "      duration=250\n";
	print "      draggable=true>\n";
	print "   <div id=\"endResDlgContent\"></div>\n";
	print "   <input type=\"hidden\" id=\"endrescont\">\n";
	print "   <input type=\"hidden\" id=\"endresid\">\n";
	print "   <div align=\"center\">\n";
	print "   <button id=\"endResDlgBtn\" dojoType=\"dijit.form.Button\">\n";
	print "     Delete Reservation\n";
	print "	   <script type=\"dojo/method\" event=\"onClick\">\n";
	print "       submitDeleteReservation();\n";
	print "     </script>\n";
	print "   </button>\n";
	print "   <button dojoType=\"dijit.form.Button\">\n";
	print "     Cancel\n";
	print "	   <script type=\"dojo/method\" event=\"onClick\">\n";
	print "       dijit.byId('endResDlg').hide();\n";
	print "       dojo.byId('endResDlgContent').innerHTML = '';\n";
	print "     </script>\n";
	print "   </button>\n";
	print "   </div>\n";
	print "</div>\n";
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn AJeditRequest()
///
/// \brief prints a page for a user to edit a previous request
///
////////////////////////////////////////////////////////////////////////////////
function AJeditRequest() {
	global $submitErr, $user;
	$requestid = getContinuationVar('requestid', 0);
	$request = getRequestInfo($requestid, 1);
	# check to see if reservation exists
	if(is_null($request)) {
		sendJSON(array('status' => 'resgone'));
		return;
	}
	$unixstart = datetimeToUnix($request["start"]);
	$unixend = datetimeToUnix($request["end"]);
	$duration = $unixend - $unixstart;
	$now = time();
	$maxtimes = getUserMaxTimes();
	$groupid = getUserGroupID('Specify End Time', 1);
	$members = getUserGroupMembers($groupid);
	if(array_key_exists($user['id'], $members) || $request['serverrequest'])
		$openend = 1;
	else
		$openend = 0;
	$h = '';

	# determine the current total length of the reservation
	$reslen = ($unixend - unixFloor15($unixstart)) / 60;
	$timeval = getdate($unixstart);
	if(($timeval["minutes"] % 15) != 0)
		$reslen -= 15;
	$cdata = array('requestid' => $requestid,
	               'openend' => $openend,
	               'modifystart' => 0,
	               'allowindefiniteend' => 0);
	if($request['serverrequest']) {
		if($user['showallgroups'])
			$groups = getUserGroups(1);
		else
			$groups = getUserGroups(1, $user['affiliationid']);
		$h .= "Admin User Group: ";
		if(USEFILTERINGSELECT && count($groups) < FILTERINGSELECTTHRESHOLD) {
			$h .= "<select dojoType=\"dijit.form.FilteringSelect\" id=\"admingrpsel\" ";
			$h .= "highlightMatch=\"all\" autoComplete=\"false\">";
		}
		else
			$h .= "<select id=\"admingrpsel\">";
		$h .= "<option value=\"0\">None</option>\n";
		foreach($groups as $id => $group) {
			if($id == $request['admingroupid'])
				$h .= "<option value=\"$id\" selected>{$group['name']}</option>";
			else
				$h .= "<option value=\"$id\">{$group['name']}</option>";
		}
		$h .= "</select><br>";
		$h .= "Access User Group: ";
		if(USEFILTERINGSELECT && count($groups) < FILTERINGSELECTTHRESHOLD) {
			$h .= "<select dojoType=\"dijit.form.FilteringSelect\" id=\"logingrpsel\" ";
			$h .= "highlightMatch=\"all\" autoComplete=\"false\">";
		}
		else
			$h .= "<select id=\"logingrpsel\">";
		$h .= "<option value=\"0\">None</option>\n";
		foreach($groups as $id => $group) {
			if($id == $request['logingroupid'])
				$h .= "<option value=\"$id\" selected>{$group['name']}</option>";
			else
				$h .= "<option value=\"$id\">{$group['name']}</option>";
		}
		$h .= "</select><br><br>";
	}
	// if future, allow start to be modified
	if($unixstart > $now) {
		$cdata['modifystart'] = 1;
		$txt  = "Modify reservation for <b>{$request['reservations'][0]['prettyimage']}</b> "; 
		$txt .= "starting " . prettyDatetime($request["start"]) . ": <br>";
		$h .= preg_replace("/(.{1,60}[ \n])/", '\1<br>', $txt);
		$days = array();
		$startday = date('l', $unixstart);
		for($cur = time(), $end = $cur + DAYSAHEAD * SECINDAY; 
		    $cur < $end; 
		    $cur += SECINDAY) {
			$index = date('Ymd', $cur);
			$days[$index] = date('l', $cur);
		}
		$cdata['startdays'] = array_keys($days);
		$h .= "Start: <select dojoType=\"dijit.form.Select\" id=\"day\" ";
		$h .= "onChange=\"resetEditResBtn();\">";
		foreach($days as $id => $name) {
			if($name == $startday)
				$h .= "<option value=\"$id\" selected=\"selected\">$name</option>";
			else
				$h .= "<option value=\"$id\">$name</option>";
		}
		$h .= "</select>";
		$h .= "&nbsp;At&nbsp;";
		$tmp = explode(' ' , $request['start']);
		$stime = $tmp[1];
		$h .= "<div type=\"text\" dojoType=\"dijit.form.TimeTextBox\" ";
		$h .= "id=\"editstarttime\" style=\"width: 78px\" value=\"T$stime\" ";
		$h .= "onChange=\"resetEditResBtn();\"></div>";
		$h .= "<small>(" . date('T') . ")</small><br><br>";
		$durationmatch = 0;
		if($request['serverrequest']) {
			$cdata['allowindefiniteend'] = 1;
			if($request['end'] == '2038-01-01 00:00:00') {
				$h .= "<INPUT type=\"radio\" name=\"ending\" id=\"indefiniteradio\" ";
				$h .= "checked onChange=\"resetEditResBtn();\">";
			}
			else {
				$h .= "<INPUT type=\"radio\" name=\"ending\" id=\"indefiniteradio\" ";
				$h .= "onChange=\"resetEditResBtn();\">";
			}
			$h .= "<label for=\"indefiniteradio\">Indefinite Ending</label>";
		}
		else {
			$durationmin = $duration / 60;
			if($request['forimaging'] && $maxtimes['initial'] < 720) # make sure at least 12 hours available for imaging reservations
				$maxtimes['initial'] = 720;
			$imgdata = getImages(1, $request['reservations'][0]['imageid']);
			$maxlen = $imgdata[$request['reservations'][0]['imageid']]['maxinitialtime'];
			if($maxlen > 0 && $maxlen < $maxtimes['initial'])
				$maxtimes['initial'] = $maxlen;
			$lengths = array();
			if($maxtimes["initial"] >= 30) {
				$lengths["30"] = "30 minutes";
				if($durationmin == 30)
					$durationmatch = 1;
			}
			if($maxtimes["initial"] >= 45) {
				$lengths["45"] = "45 minutes";
				if($durationmin == 45)
					$durationmatch = 1;
			}
			if($maxtimes["initial"] >= 60) {
				$lengths["60"] = "1 hour";
				if($durationmin == 60)
					$durationmatch = 1;
			}
			for($i = 120; $i <= $maxtimes["initial"] && $i < 2880; $i += 120) {
				$lengths[$i] = $i / 60 . " hours";
				if($durationmin == $i)
					$durationmatch = 1;
			}
			for($i = 2880; $i <= $maxtimes["initial"]; $i += 1440) {
				$lengths[$i] = $i / 1440 . " days";
				if($durationmin == $i)
					$durationmatch = 1;
			}
			if($openend) {
				if($durationmatch) {
					$h .= "<INPUT type=\"radio\" name=\"ending\" id=\"lengthradio\" ";
					$h .= "onChange=\"resetEditResBtn();\" checked>";
				}
				else {
					$h .= "<INPUT type=\"radio\" name=\"ending\" id=\"lengthradio\" ";
					$h .= "onChange=\"resetEditResBtn();\">";
				}
				$h .= "<label for=\"lengthradio\">";
			}
			$h .= "Duration:";
			if($openend)
				$h .= "</label>";
			$h .= "<select dojoType=\"dijit.form.Select\" id=\"length\" ";
			$h .= "onChange=\"selectLength();\">";
			$cdata['lengths'] = array_keys($lengths);
			foreach($lengths as $id => $name) {
				if($id == $duration / 60)
					$h .= "<option value=\"$id\" selected=\"selected\">$name</option>";
				else
					$h .= "<option value=\"$id\">$name</option>";
			}
			$h .= "</select>";
		}
		if($openend) {
			if($request['serverrequest'] && $request['end'] == '2038-01-01 00:00:00') {
				$h .= "<br><INPUT type=\"radio\" name=\"ending\" id=\"dateradio\" ";
				$h .= "onChange=\"resetEditResBtn();\">";
				$edate = '';
				$etime = '';
			}
			else {
				if(! $request['serverrequest'] && $durationmatch) {
					$h .= "<br><INPUT type=\"radio\" name=\"ending\" id=\"dateradio\" ";
					$h .= "onChange=\"resetEditResBtn();\">";
				}
				else {
					$h .= "<br><INPUT type=\"radio\" name=\"ending\" id=\"dateradio\" ";
					$h .= "checked onChange=\"resetEditResBtn();\">";
				}
				$tmp = explode(' ', $request['end']);
				$edate = $tmp[0];
				$etime = $tmp[1];
			}
			$h .= "<label for=\"dateradio\">";
			$h .= "End:";
			$h .= "</label>";
			$h .= "<div type=\"text\" dojoType=\"dijit.form.DateTextBox\" ";
			$h .= "id=\"openenddate\" style=\"width: 78px\" value=\"$edate\" ";
			$h .= "onChange=\"selectEnding();\"></div>";
			$h .= "<div type=\"text\" dojoType=\"dijit.form.TimeTextBox\" ";
			$h .= "id=\"openendtime\" style=\"width: 78px\" value=\"T$etime\" ";
			$h .= "onChange=\"selectEnding();\"></div>";
			$h .= "<small>(" . date('T') . ")</small>";
		}
		$h .= "<br><br>";
		$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
		$data = array('status' => 'modify',
		              'html' => $h,
		              'requestid' => $requestid,
		              'cont' => $cont);
		sendJSON($data);
		return;
	}
	# check for max time being reached
	if($request['forimaging'] && $maxtimes['total'] < 720)
		$maxcheck = 720;
	else
		$maxcheck = $maxtimes['total'];
	if(! $openend && ($reslen >= $maxcheck)) {
		$h .= "You are only allowed to extend your reservation such that it ";
		$h .= "has a total length of " . minToHourMin($maxcheck);
		$h .= ". This reservation<br>already meets that length. Therefore, ";
		$h .= "you are not allowed to extend your reservation any further.<br><br>";
		sendJSON(array('status' => 'nomodify', 'html' => $h));
		return;
	}
	// if started, only allow end to be modified
	# check for following reservations
	$timeToNext = timeToNextReservation($request);
	# check for 30 minutes because need 15 minute buffer and min can 
	# extend by is 15 min
	if($timeToNext < 30) {
		$movedall = 1;
		foreach($request["reservations"] as $res) {
			if(! moveReservationsOffComputer($res["computerid"], 1)) {
				$movedall = 0;
				break;
			}
		}
		if(! $movedall) {
			$h .= "The computer you are using has another reservation<br>";
			$h .= "immediately following yours. Therefore, you cannot<br>";
			$h .= "extend your reservation because it would overlap<br>";
			$h .= "with the next one.<br>";
			sendJSON(array('status' => 'nomodify', 'html' => $h));
			return;
		}
		$timeToNext = timeToNextReservation($request);
	}
	if($timeToNext >= 15)
		$timeToNext -= 15;
	//if have time left to extend it, create an array of lengths based on maxextend that has a cap
	# so we don't run into another reservation and we can't extend past the totalmax
	$lengths = array();
	if($request['forimaging'] && $maxtimes['total'] < 720) # make sure at least 12 hours available for imaging reservations
		$maxtimes['total'] = 720;
	if($timeToNext == -1) {
		// there is no following reservation
		if((($reslen + 15) <= $maxtimes["total"]) && (15 <= $maxtimes["extend"]))
			$lengths["15"] = "15 minutes";
		if((($reslen + 30) <= $maxtimes["total"]) && (30 <= $maxtimes["extend"]))
			$lengths["30"] = "30 minutes";
		if((($reslen + 45) <= $maxtimes["total"]) && (45 <= $maxtimes["extend"]))
			$lengths["45"] = "45 minutes";
		if((($reslen + 60) <= $maxtimes["total"]) && (60 <= $maxtimes["extend"]))
			$lengths["60"] = "1 hour";
		for($i = 120; (($reslen + $i) <= $maxtimes["total"]) && ($i <= $maxtimes["extend"]) && $i < 2880; $i += 120)
			$lengths[$i] = $i / 60 . " hours";
		for($i = 2880; (($reslen + $i) <= $maxtimes["total"]) && ($i <= $maxtimes["extend"]); $i += 1440)
			$lengths[$i] = $i / 1440 . " days";
	}
	else {
		if($timeToNext >= 15 && (($reslen + 15) <= $maxtimes["total"]) && (15 <= $maxtimes["extend"]))
			$lengths["15"] = "15 minutes";
		if($timeToNext >= 30 && (($reslen + 30) <= $maxtimes["total"]) && (30 <= $maxtimes["extend"]))
			$lengths["30"] = "30 minutes";
		if($timeToNext >= 45 && (($reslen + 45) <= $maxtimes["total"]) && (45 <= $maxtimes["extend"]))
			$lengths["45"] = "45 minutes";
		if($timeToNext >= 60 && (($reslen + 60) <= $maxtimes["total"]) && (60 <= $maxtimes["extend"]))
			$lengths["60"] = "1 hour";
		for($i = 120; ($i <= $timeToNext) && (($reslen + $i) <= $maxtimes["total"]) && ($i <= $maxtimes["extend"]) && $i < 2880; $i += 120)
			$lengths[$i] = $i / 60 . " hours";
		for($i = 2880; ($i <= $timeToNext) && (($reslen + $i) <= $maxtimes["total"]) && ($i <= $maxtimes["extend"]); $i += 1440)
			$lengths[$i] = $i / 1440 . " days";
	}
	$cdata['lengths'] = array_keys($lengths);
	if($timeToNext == -1 || $timeToNext >= $maxtimes['total']) {
		if($openend) {
			if(! empty($lenghts)) {
				$h .= "You can extend this reservation by a selected amount or<br>";
				$h .= "change the end time to a specified date and time.<br><br>";
			}
			else
				$h .= "Modify the end time for this reservation:<br><br>";
		}
		else {
			if($request['forimaging'] && $maxtimes['total'] < 720)
				$maxcheck = 720;
			else
				$maxcheck = $maxtimes['total'];
			$h .= "You can extend this reservation by up to ";
			$h	.= minToHourMin($maxtimes["extend"]) . ", but not<br>exceeding ";
			$h	.= minToHourMin($maxcheck) . " for your total reservation ";
			$h .= "time.<br><br>";
		}
	}
	else {
		$t  = "The computer you are using has another reservation following ";
		$t .= "yours. Therefore, you can only extend this reservation for ";
		$t .= "another " . prettyLength($timeToNext) . ". <br>";
		$h .= preg_replace("/(.{1,60}[ ])/", '\1<br>', $t);
	}
	# extend by drop down
	# extend by specifying end time if $openend
	if($openend) {
		if($request['serverrequest']) {
			$cdata['allowindefiniteend'] = 1;
			$endchecked = 0;
			if($request['end'] == '2038-01-01 00:00:00') {
				$h .= "<INPUT type=\"radio\" name=\"ending\" id=\"indefiniteradio\" ";
				$h .= "checked onChange=\"resetEditResBtn();\">";
				$h .= "<label for=\"indefiniteradio\">Indefinite Ending</label>";
				$h .= "<br><INPUT type=\"radio\" name=\"ending\" id=\"dateradio\" ";
				$h .= "onChange=\"resetEditResBtn();\">";
			}
			else {
				$h .= "<INPUT type=\"radio\" name=\"ending\" id=\"indefiniteradio\" ";
				$h .= "onChange=\"resetEditResBtn();\">";
				$h .= "<label for=\"indefiniteradio\">Indefinite Ending</label>";
				$h .= "<br><INPUT type=\"radio\" name=\"ending\" id=\"dateradio\" ";
				$h .= "checked onChange=\"resetEditResBtn();\">";
				$endchecked = 1;
			}
			$h .= "<label for=\"dateradio\">";
		}
		elseif(! empty($lengths)) {
			$h .= "<INPUT type=\"radio\" name=\"ending\" id=\"lengthradio\" ";
			$h .= "checked onChange=\"resetEditResBtn();\">";
			$h .= "<label for=\"lengthradio\">Extend reservation by:</label>";
			$h .= "<select dojoType=\"dijit.form.Select\" id=\"length\" ";
			$h .= "onChange=\"selectLength();\">";
			foreach($lengths as $id => $name)
				$h .= "<option value=\"$id\">$name</option>";
			$h .= "</select>";
			$h .= "<br><INPUT type=\"radio\" name=\"ending\" id=\"dateradio\" ";
			$h .= "onChange=\"resetEditResBtn();\">";
			$h .= "<label for=\"dateradio\">";
		}
		if($request['serverrequest']) {
			$h .= "End:";
			if($endchecked) {
				$tmp = explode(' ', $request['end']);
				$edate = $tmp[0];
				$etime = $tmp[1];
			}
			else {
				$edate = '';
				$etime = '';
			}
		}
		else {
			$h .= "Change ending to:";
			$tmp = explode(' ', $request['end']);
			$edate = $tmp[0];
			$etime = $tmp[1];
		}
		if(! empty($lengths) || $request['serverrequest'])
			$h .= "</label>";
		$h .= "<div type=\"text\" dojoType=\"dijit.form.DateTextBox\" ";
		$h .= "id=\"openenddate\" style=\"width: 78px\" value=\"$edate\" ";
		$h .= "onChange=\"selectEnding();\"></div>";
		$h .= "<div type=\"text\" dojoType=\"dijit.form.TimeTextBox\" ";
		$h .= "id=\"openendtime\" style=\"width: 78px\" value=\"T$etime\" ";
		$h .= "onChange=\"selectEnding();\"></div>";
		$h .= "<small>(" . date('T') . ")</small>";
		$h .= "<INPUT type=\"hidden\" name=\"enddate\" id=\"enddate\">";
		if($timeToNext > -1) {
			$extend = $unixend + ($timeToNext * 60);
			$extend = date('m/d/Y g:i A', $extend);
			$h .= "<br><font color=red><strong>NOTE:</strong> Due to an upcoming ";
			$h .= "reservation on the same computer,<br>";
			$h .= "you can only extend this reservation until $extend.</font>";
			$cdata['maxextend'] = $extend;
		}
	}
	else {
		$h .= "Extend reservation by:";
		$h .= "<select dojoType=\"dijit.form.Select\" id=\"length\">";
		foreach($lengths as $id => $name)
			$h .= "<option value=\"$id\">$name</option>";
		$h .= "</select>";
	}
	$h .= "<br><br>";
	$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
	$data = array('status' => 'modify',
	              'html' => $h,
	              'requestid' => $requestid,
	              'cont' => $cont);
	sendJSON($data);
	return;
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn AJsubmitEditRequest()
///
/// \brief submits changes to a request and prints that it has been changed
///
////////////////////////////////////////////////////////////////////////////////
function AJsubmitEditRequest() {
	global $user;
	$requestid = getContinuationVar('requestid');
	$openend = getContinuationVar('openend');
	$modifystart = getContinuationVar('modifystart');
	$startdays = getContinuationVar('startdays');
	$lengths = getContinuationVar('lengths');
	$maxextend = getContinuationVar('maxextend');
	$allowindefiniteend = getContinuationVar('allowindefiniteend');

	$request = getRequestInfo($requestid, 1);
	if(is_null($request)) {
		$cdata = getContinuationVar();
		$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
		sendJSON(array('status' => 'norequest',
		               'html' => 'The selected reservation no longer exists.<br><br>',
		               'cont' => $cont));
		return;
	}

	if($modifystart) {
		$day = processInputVar('day', ARG_NUMERIC, 0);
		if(! in_array($day, $startdays)) {
			$cdata = getContinuationVar();
			$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
			sendJSON(array('status' => 'error',
			               'errmsg' => 'Invalid start day submitted',
			               'cont' => $cont));
			return;
		}
		$starttime = processInputVar('starttime', ARG_STRING);
		if(! preg_match('/^(([01][0-9])|(2[0-3]))([0-5][0-9])$/', $starttime, $matches)) {
			$cdata = getContinuationVar();
			$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
			sendJSON(array('status' => 'error',
			               'errmsg' => "Invalid start time submitted",
			               'cont' => $cont));
			return;
		}
		preg_match('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', $day, $tmp);
		$startdt = "{$tmp[1]}-{$tmp[2]}-{$tmp[3]} {$matches[1]}:{$matches[4]}:00";
		$startts = datetimeToUnix($startdt);
	}
	else {
		$startdt = $request['start'];
		$startts = datetimeToUnix($startdt);
	}
	$endmode = processInputVar('endmode', ARG_STRING);
	if($endmode == 'length') {
		$length = processInputVar('length', ARG_NUMERIC);
		if(! in_array($length, $lengths)) {
			$cdata = getContinuationVar();
			$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
			sendJSON(array('status' => 'error',
			               'errmsg' => "Invalid duration submitted",
			               'cont' => $cont));
			return;
		}
		if($modifystart)
			$endts = $startts + ($length * 60);
		else {
			$tmp = datetimeToUnix($request['end']);
			$endts = $tmp + ($length * 60);
		}
		$enddt = unixToDatetime($endts);
	}
	elseif($endmode == 'ending') {
		$ending = processInputVar('ending', ARG_NUMERIC);
		if(! preg_match('/^([0-9]{4})([0-9]{2})([0-9]{2})(([01][0-9])|(2[0-3]))([0-5][0-9])$/', $ending, $tmp) ||
		   ! checkdate($tmp[2], $tmp[3], $tmp[1])) {
			$cdata = getContinuationVar();
			$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
			sendJSON(array('status' => 'error',
			               'errmsg' => "Invalid end date/time submitted",
			               'cont' => $cont));
			return;
		}
		$enddt = "{$tmp[1]}-{$tmp[2]}-{$tmp[3]} {$tmp[4]}:{$tmp[7]}:00";
		$endts = datetimeToUnix($enddt);
	}
	elseif($allowindefiniteend && $endmode == 'indefinite') {
		$endts = datetimeToUnix('2038-01-01 00:00:00');
		$enddt = unixToDatetime($endts);
	}
	else {
		$cdata = getContinuationVar();
		$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
		sendJSON(array('status' => 'error',
		               'errmsg' => "Invalid data submitted",
		               'cont' => $cont));
		return;
	}
	$updategroups = 0;
	if($request['serverrequest']) {
		if($user['showallgroups'])
			$groups = getUserGroups(1);
		else
			$groups = getUserGroups(1, $user['affiliationid']);
		$admingroupid = processInputVar('admingroupid', ARG_NUMERIC);
		$logingroupid = processInputVar('logingroupid', ARG_NUMERIC);
		if(($admingroupid != 0 && ! array_key_exists($admingroupid, $groups)) ||
			($logingroupid != 0 && ! array_key_exists($logingroupid, $groups))) {
			$cdata = getContinuationVar();
			$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
			sendJSON(array('status' => 'error',
			               'errmsg' => "Invalid user group submitted",
			               'cont' => $cont));
			return;
		}
		if($admingroupid != $request['admingroupid'] ||
			$logingroupid != $request['logingroupid'])
			$updategroups = 1;
	}

	// get semaphore lock
	if(! semLock())
		abort(3);

	$h = '';
	$max = getMaxOverlap($user['id']);
	if(checkOverlap($startts, $endts, $max, $requestid)) {
		if($max == 0) {
			$h .= "The time you requested overlaps with another reservation<br>";
			$h .= "you currently have. You are only allowed to have a single<br>";
			$h .= "reservation at any given time. Please select another time<br>";
			$h .= "for the reservation.<br><br>";
		}
		else {
			$h .= "The time you requested overlaps with another reservation<br>";
			$h .= "you currently have. You are allowed to have $max overlapping<br>";
			$h .= "reservations at any given time. Please select another time<br>";
			$h .= "for the reservation.<br><br>";
		}
		$cdata = getContinuationVar();
		$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
		sendJSON(array('status' => 'error', 'errmsg' => $h, 'cont' => $cont));
		semUnlock();
		return;
	}

	if($request['serverrequest'] &&
		(! empty($request['fixedIP']) || ! empty($request['fixedMAC']))) {
		$ip = $request['fixedIP'];
		$mac = $request['fixedMAC'];
	}
	else {
		$ip = '';
		$mac = '';
	}
	$imageid = $request['reservations'][0]['imageid'];
	$images = getImages();
	$rc = isAvailable($images, $imageid,
	                  $request['reservations'][0]['imagerevisionid'], $startts,
	                  $endts, $requestid, 0, 0, 0, $ip, $mac);
	$data = array();
	if($rc < 1) { 
		$cdata = array('now' => 0,
		               'start' => $startts, 
		               'end' => $endts,
		               'server' => $allowindefiniteend,
		               'imageid' => $imageid,
		               'requestid' => $requestid);
		if(! $modifystart)
			$cdata['extendonly'] = 1;
		$sugcont = addContinuationsEntry('AJshowRequestSuggestedTimes', $cdata);
		if(array_key_exists('subimages', $images[$imageid]) &&
		   count($images[$imageid]['subimages']))
			$data['sugcont'] = 'cluster';
		else
			$data['sugcont'] = $sugcont;
		addChangeLogEntry($request["logid"], NULL, $enddt, $startdt, NULL, NULL, 0);
	}
	if($rc == -3) {
		$msgip = '';
		$msgmac = '';
		if(! empty($ip))
			$msgip = " ($ip)";
		if(! empty($mac))
			$msgmac = " ($mac)";
		$h .= "The reserved IP$msgip or MAC address$msgmac conflicts with another ";
		$h .= "reservation using the same IP or MAC address. Please ";
		$h .= "select a different time to use the image. ";
		$h = preg_replace("/(.{1,60}[ \n])/", '\1<br>', $h);
		$cdata = getContinuationVar();
		$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
		$data['status'] = 'conflict';
		$data['errmsg'] = $h;
		$data['cont'] = $cont;
		sendJSON($data);
		semUnlock();
		return;
	}
	elseif($rc == -2) {
		$h .= "The time you requested overlaps with a maintenance window.<br>";
		$h .= "Please select a different time to use the image.<br>";
		$cdata = getContinuationVar();
		$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
		$data['status'] = 'conflict';
		$data['errmsg'] = $h;
		$data['cont'] = $cont;
		sendJSON($data);
		semUnlock();
		return;
	}
	elseif($rc == -1) {
		$h .= "You have requested an environment that is limited in the<br>";
		$h .= "number of concurrent reservations that can be made. No further<br>";
		$h .= "reservations for the environment can be made for the time you<br>";
		$h .= "have selected. Please select another time for the reservation.<br><br>";
		$cdata = getContinuationVar();
		$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
		$data['status'] = 'conflict';
		$data['errmsg'] = $h;
		$data['cont'] = $cont;
		sendJSON($data);
		semUnlock();
		return;
	}
	elseif($rc > 0) {
		updateRequest($requestid);
		if($updategroups) {
			$query = "UPDATE serverrequest "
			       . "SET admingroupid = $admingroupid, "
			       .     "logingroupid = $logingroupid "
			       . "WHERE requestid = $requestid";
			doQuery($query, 101);
			$query = "UPDATE request "
			       . "SET stateid = 29 "
			       . "WHERE id = $requestid";
			doQuery($query, 101);
		}
		sendJSON(array('status' => 'success'));
		semUnlock();
		return;
	}
	else {
		$h .= "The time period you have requested is not available.<br>";
		$h .= "Please select a different time.";
		$cdata = getContinuationVar();
		$cont = addContinuationsEntry('AJsubmitEditRequest', $cdata, SECINDAY, 1, 0);
		$data['status'] = 'conflict';
		$data['errmsg'] = $h;
		$data['cont'] = $cont;
		sendJSON($data);
	}
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn AJconfirmDeleteRequest()
///
/// \brief prints a confirmation page about deleting a request
///
////////////////////////////////////////////////////////////////////////////////
function AJconfirmDeleteRequest() {
	$requestid = getContinuationVar('requestid', 0);
	$notbyowner = getContinuationVar('notbyowner', 0);
	$fromtimetable = getContinuationVar('fromtimetable', 0);
	$request = getRequestInfo($requestid, 1);
	if(is_null($request)) {
		$data = array('error' => 1,
		              'msg' => "The specified reservation no longer exists.");
		sendJSON($data);
		return;
	}
	if($request['forimaging'])
		$reservation = $request['reservations'][0];
	else {
		foreach($request["reservations"] as $res) {
			if($res["forcheckout"]) {
				$reservation = $res;
				break;
			}
		}
	}
	if(datetimeToUnix($request["start"]) > time()) {
		$text = "Delete reservation for <b>" . $reservation["prettyimage"]
		      . "</b> starting " . prettyDatetime($request["start"]) . "?<br>\n";
	}
	else {
		if($notbyowner == 0 && ! $reservation["production"]) {
			AJconfirmDeleteRequestProduction($request);
			return;
		}
		else {
			if($notbyowner == 0) {
				$text = "Are you finished with your reservation for <strong>"
						. $reservation["prettyimage"] . "</strong> that started ";
			}
			else {
				$userinfo = getUserInfo($request["userid"], 1, 1);
				$text = "Delete reservation by {$userinfo['unityid']}@"
				      . "{$userinfo['affiliation']} for <strong>"
				      . "{$reservation["prettyimage"]}</strong> that started ";
			}
			if(datetimeToUnix($request["start"]) <
				datetimeToUnix($request["daterequested"]))
				$text .= prettyDatetime($request["daterequested"]);
			else
				$text .= prettyDatetime($request["start"]);
			$text .= "?<br>\n";
		}
	}
	$cdata = array('requestid' => $requestid,
	               'notbyowner' => $notbyowner,
	               'fromtimetable' => $fromtimetable);
	if($fromtimetable)
		$cdata['ttdata'] = getContinuationVar('ttdata');
	$cont = addContinuationsEntry('AJsubmitDeleteRequest', $cdata, SECINDAY, 0, 0);
	$text = preg_replace("/(.{1,60}[ \n])/", '\1<br>', $text);
	$data = array('content' => $text,
	              'cont' => $cont,
	              'requestid' => $requestid,
	              'btntxt' => 'Delete Reservation');
	sendJSON($data);
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn AJconfirmDeleteRequestProduction($request)
///
/// \param $request - a request array as returend from getRequestInfo
///
/// \brief prints a page asking if the user is ready to make the image
/// production or just end this reservation
///
////////////////////////////////////////////////////////////////////////////////
function AJconfirmDeleteRequestProduction($request) {
	$cdata = array('requestid' => $request['id']);
	$text = '';
	$title = "<big><strong>End Reservation/Make Production</strong></big><br><br>";
	$text .=	"Are you satisfied that this environment is ready to be made production ";
	$text .= "and replace the current production version, or would you just like to ";
	$text .= "end this reservation and test it again later? ";

	if(isImageBlockTimeActive($request['reservations'][0]['imageid'])) {
		$text .= "<br><font color=\"red\">\nWARNING: This environment is part of ";
		$text .= "an active block allocation. Changing the production version of ";
		$text .= "the environment at this time will result in new reservations ";
		$text .= "under the block allocation to have full reload times instead of ";
		$text .= "a &lt; 1 minutes wait. You can change the production version ";
		$text .= "later by going to Manage Images-&gt;Edit Image Profiles and ";
		$text .= "clicking Edit for this environment.</font><br>";
	}

	$cont = addContinuationsEntry('AJsetImageProduction', $cdata, SECINDAY, 0, 1);
	$radios = '';
	$radios .= "<br>&nbsp;&nbsp;&nbsp;<INPUT type=radio name=continuation ";
	$radios .= "value=\"$cont\" id=\"radioprod\"><label for=\"radioprod\">Make ";
	$radios .= "this the production version</label><br>";

	$cont = addContinuationsEntry('AJsubmitDeleteRequest', $cdata, SECINDAY, 0, 0);
	$radios .= "&nbsp;&nbsp;&nbsp;<INPUT type=radio name=continuation ";
	$radios .= "value=\"$cont\" id=\"radioend\"><label for=\"radioend\">Just ";
	$radios .= "end the reservation</label><br><br>";
	$text = preg_replace("/(.{1,60}[ \n])/", '\1<br>', $text);
	$data = array('content' => $title . $text . $radios,
	              'cont' => $cont,
	              'btntxt' => 'Submit');
	sendJSON($data);
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn AJsubmitDeleteRequest()
///
/// \brief submits deleting a request and prints that it has been deleted
///
////////////////////////////////////////////////////////////////////////////////
function AJsubmitDeleteRequest() {
	global $mode;
	$mode = 'AJviewRequests';
	$requestid = getContinuationVar('requestid', 0);
	$fromtimetable = getContinuationVar('fromtimetable', 0);
	$request = getRequestInfo($requestid);
	deleteRequest($request);
	if($fromtimetable) {
		$cdata = getContinuationVar('ttdata');
		$cont = addContinuationsEntry('showTimeTable', $cdata);
		print "window.location.href='" . BASEURL . SCRIPT . "?continuation=$cont';";
		return;
	}
	viewRequests();
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn AJconfirmRemoveRequest()
///
/// \brief populates a confirmation dialog box
///
////////////////////////////////////////////////////////////////////////////////
function AJconfirmRemoveRequest() {
	$requestid = getContinuationVar('requestid', 0);
	$request = getRequestInfo($requestid, 1);
	if(is_null($request)) {
		$data = array('error' => 1,
		              'msg' => "The specified reservation no longer exists.");
		sendJSON($data);
		return;
	}
	if($request['stateid'] != 11 && $request['laststateid'] != 11 &&
	   $request['stateid'] != 12 && $request['laststateid'] != 12 &&
	   $request['stateid'] !=  5 && $request['laststateid'] !=  5) {
		$data = array('error' => 2,
		              'msg' => "The reservation is no longer failed or timed out.",
		              'url' => BASEURL . SCRIPT . "?mode=viewRequests");
		sendJSON($data);
		return;
	}
	if($request['stateid'] == 11 || $request['stateid'] == 12 ||
	   $request['stateid'] == 12 || $request['laststateid'] == 12) {
		$text  = "Remove timed out reservation from list of current ";
		$text .= "reservations?<br>\n";
	}
	else {
		$text  = "Remove failed reservation from list of current reservations?";
		$text .= "<br>\n";
	}
	$cdata = array('requestid' => $requestid);
	$cont = addContinuationsEntry('AJsubmitRemoveRequest', $cdata, SECINDAY, 0, 0);
	$text = preg_replace("/(.{1,60}[ \n])/", '\1<br>', $text);
	$data = array('content' => $text,
	              'cont' => $cont);
	sendJSON($data);
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn AJsubmitRemoveRequest()
///
/// \brief submits deleting a request and prints that it has been deleted
///
////////////////////////////////////////////////////////////////////////////////
function AJsubmitRemoveRequest() {
	global $mode;
	$mode = 'AJviewRequests';
	$requestid = getContinuationVar('requestid', 0);
	$request = getRequestInfo($requestid, 1);
	if(is_null($requestid)) {
		viewRequests();
		return;
	}

	if($request['serverrequest']) {
		$query = "DELETE FROM serverrequest WHERE requestid = $requestid";
		doQuery($query, 152);
	}

	# TODO do these need to set state to complete?
	$query = "DELETE FROM request WHERE id = $requestid";
	doQuery($query, 153);

	$query = "DELETE FROM reservation WHERE requestid = $requestid";
	doQuery($query, 154);

	viewRequests();
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn AJrebootRequest()
///
/// \brief sets a reservation to the reboot state and refreshes the Current
/// Reservations page
///
////////////////////////////////////////////////////////////////////////////////
function AJrebootRequest() {
	$requestid = getContinuationVar('requestid');
	$reboottype = processInputVar('reboottype', ARG_NUMERIC);
	$newstateid = 26;
	if($reboottype == 1)
		$newstateid = 28;
	$query = "UPDATE request SET stateid = $newstateid WHERE id = $requestid";
	doQuery($query, 101);
	print "resRefresh();";
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn AJreinstallRequest()
///
/// \brief sets a reservation to the reinstall state and refreshes the Current
/// Reservations page
///
////////////////////////////////////////////////////////////////////////////////
function AJreinstallRequest() {
	$requestid = getContinuationVar('requestid');
	$query = "UPDATE request SET stateid = 27 WHERE id = $requestid";
	doQuery($query, 101);
	print "resRefresh();";
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn printReserveItems($modifystart, $imaging, $length, $maxlength, $day,
///                       $hour, $minute, $meridian, $oneline)
///
/// \param $modifystart - (optional) 1 to print form for modifying start time, 
/// 0 not to
/// \param $imaging - (optional) 1 if imaging reservation, 0 if not
/// \param $length - (optional) initial length (in minutes)
/// \param $maxlength - (optional) max initial length (in minutes)
/// \param $day - (optional) initial day of week (Sunday - Saturday)
/// \param $hour - (optional) initial hour (1-12)
/// \param $minute - (optional) initial minute (00-59)
/// \param $meridian - (optional) initial meridian (am/pm)
/// \param $oneline - (optional) print all items on one line
///
/// \brief prints reserve form data
///
////////////////////////////////////////////////////////////////////////////////
function printReserveItems($modifystart=1, $imaging=0, $length=60, $maxlength=0,
                           $day=NULL, $hour=NULL, $minute=NULL, $meridian=NULL,
                           $oneline=0) {
	global $user, $submitErr;
	if(! is_numeric($length))
		$length = 60;
	$ending = processInputVar("ending", ARG_STRING, "length");
	$enddate = processInputVar("enddate", ARG_STRING);
	$groupid = getUserGroupID('Specify End Time', 1);
	$members = getUserGroupMembers($groupid);
	if(array_key_exists($user['id'], $members))
		$openend = 1;
	else
		$openend = 0;
	$days = array();
	$inputday = "";
	for($cur = time(), $end = $cur + DAYSAHEAD * SECINDAY; 
	    $cur < $end; 
		 $cur += SECINDAY) {
		$tmp = getdate($cur);
		$index = $tmp["mon"] . "/" . $tmp["mday"] . "/" . $tmp["year"];
		$days[$index] = $tmp["weekday"];
		if($tmp["weekday"] == $day) {
			$inputday = $index;
		}
	}

	if($modifystart) {
		printSelectInput("day", $days, $inputday, 0, 0, 'reqday', "onChange='selectLater();'");
		print "&nbsp;At&nbsp;\n";
		$tmpArr = array();
		for($i = 1; $i < 13; $i++) {
			$tmpArr[$i] = $i;
		}
		printSelectInput("hour", $tmpArr, $hour, 0, 0, 'reqhour', "onChange='selectLater();'");

		$minutes = array("zero" => "00",
							  "15" => "15",
							  "30" => "30", 
							  "45" => "45");
		printSelectInput("minute", $minutes, $minute, 0, 0, 'reqmin', "onChange='selectLater();'");
		printSelectInput("meridian", array("am" => "a.m.", "pm" => "p.m."), $meridian,
		                 0, 0, 'reqmeridian', "onChange='selectLater();'");
		print "<small>(" . date('T') . ")</small>";
		if($submitErr & STARTDAYERR)
			printSubmitErr(STARTDAYERR);
		elseif($submitErr & STARTHOURERR)
			printSubmitErr(STARTHOURERR);
		elseif($submitErr & STARTMINUTEERR)
			printSubmitErr(STARTMINUTEERR);
		print "<br><br>";
		if($openend) {
			if($ending != 'date')
				$checked = 'checked';
			else
				$checked = '';
			print "&nbsp;&nbsp;&nbsp;<INPUT type=\"radio\" name=\"ending\" ";
			print "onclick=\"updateWaitTime(0);\" value=\"length\" $checked ";
			print "id=\"durationradio\"><label for=\"durationradio\">";
		}
		print "Duration:&nbsp;\n";
		if($openend)
			print "</label>";
	}
	else {
		print "<INPUT type=hidden name=day value=$inputday>\n";
		print "<INPUT type=hidden name=hour value=$hour>\n";
		print "<INPUT type=hidden name=minute value=$minute>\n";
		print "<INPUT type=hidden name=meridian value=$meridian>\n";
	}
	// check for a "now" reservation that got 15 min added to it
	if($length % 30) {
		$length -= 15;
	}

	// if ! $modifystart, we return at this point because we don't
	# know enough about the current reservation to determine how
	# long they can extend it for, the calling function would have
	# to determine that and print a length dropdown box
	if(! $modifystart)
		return;

	# create an array of usage times based on the user's max times
	$maxtimes = getUserMaxTimes();
	if($maxlength > 0 && $maxlength < $maxtimes['initial'])
		$maxtimes['initial'] = $maxlength;
	if($imaging && $maxtimes['initial'] < 720) # make sure at least 12 hours available for imaging reservations
		$maxtimes['initial'] = 720;
	$lengths = getReservationLengths($maxtimes['initial']);

	printSelectInput("length", $lengths, $length, 0, 0, 'reqlength',
		"onChange='updateWaitTime(0); selectDuration();'");
	print "<br>\n";
	if($openend) {
		if($ending == 'date')
			$checked = 'checked';
		else
			$checked = '';
		print "&nbsp;&nbsp;&nbsp;<INPUT type=\"radio\" name=\"ending\" id=\"openend\" ";
		print "onclick=\"updateWaitTime(0);\" value=\"date\" $checked>";
		print "<label for=\"openend\">Until</label>\n";

		if(preg_match('/^(20[0-9]{2}-[0-1][0-9]-[0-3][0-9]) ((([0-1][0-9])|(2[0-3])):([0-5][0-9]):([0-5][0-9]))$/', $enddate, $regs)) {
			$edate = $regs[1];
			$etime = $regs[2];
			$validendformat = 1;
		}
		else {
			$edate = '';
			$etime = '';
			$validendformat = 0;
		}
		print "<div type=\"text\" dojoType=\"dijit.form.DateTextBox\" ";
		print "id=\"openenddate\" onChange=\"setOpenEnd();\" ";
		print "style=\"width: 78px\" value=\"$edate\"></div>\n";
		print "<div type=\"text\" dojoType=\"dijit.form.TimeTextBox\" ";
		print "id=\"openendtime\" onChange=\"setOpenEnd();\" ";
		print "style=\"width: 78px\" value=\"T$etime\"></div>\n";
		print "<small>(" . date('T') . ")</small>\n";
		print "<noscript>(You must have javascript enabled to use the 'Until' ";
		print "option.)<br></noscript>\n";
		printSubmitErr(ENDDATEERR);
		if($validendformat)
			print "<INPUT type=\"hidden\" name=\"enddate\" id=\"enddate\" value=\"$enddate\">\n";
		else
			print "<INPUT type=\"hidden\" name=\"enddate\" id=\"enddate\" value=\"\">\n";
		print "<br>\n";
	}
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn connectRequest()
///
/// \brief sets IPaddress for the request; tells the user how to connect
///
////////////////////////////////////////////////////////////////////////////////
function connectRequest() {
	global $remoteIP, $user, $inContinuation;
	if($inContinuation)
		$requestid = getContinuationVar('requestid', 0);
	else
		$requestid = processInputVar("requestid", ARG_NUMERIC);
	$requestData = getRequestInfo($requestid);
	if($requestData['stateid'] == 11 || $requestData['stateid'] == 12 ||
	   ($requestData['stateid'] == 14 && 
	   ($requestData['laststateid'] == 11 || $requestData['laststateid'] == 12))) {
		print "<H2 align=center>Connect!</H2>\n";
		print "This reservation has timed out due to lack of user activity and ";
		print "is no longer available.<br>\n";
		return;
	}
	if($requestData['reservations'][0]['remoteIP'] != $remoteIP) {
		$setback = unixToDatetime(time() - 600);
		$query = "UPDATE reservation "
		       . "SET remoteIP = '$remoteIP', "
		       .     "lastcheck = '$setback' "
		       . "WHERE requestid = $requestid";
		$qh = doQuery($query, 226);

		addChangeLogEntry($requestData["logid"], $remoteIP);
	}

	print "<H2 align=center>Connect!</H2>\n";
	if($requestData['forimaging']) {
		print "<font color=red><big><strong>NOTICE:</strong> Later in this process, you must accept a
		<a href=\"" . BASEURL . SCRIPT . "?mode=imageClickThrough\">click-through agreement</a> about software licensing.</big></font><br><br>\n";
	}
	$imagenotes = getImageNotes($requestData['reservations'][0]['imageid']);
	if(preg_match('/\w/', $imagenotes['usage'])) {
		print "<h3>Notes on using this environment:</h3>\n";
		print "{$imagenotes['usage']}<br><br><br>\n";
	}
	if(count($requestData["reservations"]) > 1)
		$cluster = 1;
	else
		$cluster = 0;
	if($cluster) {
		print "<h2>Cluster Reservation</h2>\n";
		print "This is a cluster reservation. Depending on the makeup of the ";
		print "cluster, you may need to use different methods to connect to the ";
		print "different environments in your cluster.<br><br>\n";
	}
	foreach($requestData["reservations"] as $key => $res) {
		$serverIP = $res["reservedIP"];
		$osname = $res["OS"];
		if(array_key_exists($user['id'], $requestData['passwds'][$res['reservationid']]))
			$passwd = $requestData['passwds'][$res['reservationid']][$user['id']];
		else
			$passwd = '';
		$connectData = getImageConnectMethodTexts($res['imageid'],
		                                          $res['imagerevisionid']);
		$first = 1;
		if($cluster) {
			print "<fieldset>\n";
			print "<legend><big><strong>{$res['prettyimage']}</strong></big></legend>\n";
		}
		foreach($connectData as $method) {
			if($first)
				$first = 0;
			else
				print "<hr>\n";
			if($requestData['forimaging'] && $res['OStype'] == 'windows')
				$conuser = 'Administrator';
			elseif(preg_match('/(.*)@(.*)/', $user['unityid'], $matches))
				$conuser = $matches[1];
			else
				$conuser = $user['unityid'];
			if(! strlen($passwd))
				$passwd = '(use your campus password)';
			if($cluster)
				print "<h4>Connect to reservation using {$method['description']}</h4>\n";
			else
				print "<h3>Connect to reservation using {$method['description']}</h3>\n";
			$froms = array('/#userid#/',
			               '/#password#/',
			               '/#connectIP#/',
			               '/#connectport#/');
			if(empty($res['connectIP']))
				$res['connectIP'] = $serverIP; #TODO delete this when vcld is populating connectIP
			$tos = array($conuser,
			             $passwd,
			             $res['connectIP'], 
			             $res['connectport']);
			print preg_replace($froms, $tos, $method['connecttext']);
			if($method['description'] == 'Remote Desktop') {
				print "<div id=\"counterdiv\"></div>\n";
				print "<div id=\"connectdiv\" class=\"hidden\">\n";
				print "<FORM action=\"" . BASEURL . SCRIPT . "\" method=post>\n";
				$cdata = array('requestid' => $requestid,
				               'resid' => $res['reservationid']);
				$expire = datetimeToUnix($requestData['end']) - time() + 1800; # remaining reservation time plus 30 min
				$cont = addContinuationsEntry('sendRDPfile', $cdata, $expire);
				print "<INPUT type=hidden name=continuation value=\"$cont\">\n";
				print "<INPUT type=submit value=\"Get RDP File\">\n";
				print "</FORM>\n";
				print "</div>\n";
			}
		}
		if($cluster)
			print "</fieldset><br>\n";
	}
}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn connectRDPapplet()
///
/// \brief prints a page to launch the RDP java applet
///
////////////////////////////////////////////////////////////////////////////////
/*function connectRDPapplet() {
	global $user;
	$requestid = processInputVar("requestid", ARG_NUMERIC);
	$requestData = getRequestInfo($requestid);
	$server = processInputVar("reservedIP", ARG_STRING, $requestData["reservations"][0]["reservedIP"]);
	$password = "";
	foreach($requestData["reservations"] as $res) {
		if($res["reservedIP"] == $server) {
			$password = $res["password"];
			break;
		}
	}
	print "<div align=center>\n";
	print "<H2>Connect!</H2>\n";
	print "Launching applet.  You will have to grant it any permissions it requests.<br>\n";
	print "<APPLET CODE=\"net.propero.rdp.applet.RdpApplet.class\"\n";
	print "        ARCHIVE=\"properJavaRDP/properJavaRDP-1.1.jar,properJavaRDP/properJavaRDP14-1.1.jar,properJavaRDP/log4j-java1.1.jar,properJavaRDP/java-getopt-1.0.12.jar\" WIDTH=320 HEIGHT=240>\n";
	print "  <PARAM NAME=\"server\" VALUE=\"$server\">\n";
	print "  <PARAM NAME=\"port\" VALUE=\"3389\">\n";
	print "  <PARAM NAME=\"username\" VALUE=\"{$user["unityid"]}\">\n";
	print "  <PARAM NAME=\"password\" VALUE=\"$password\">\n";
	print "  <PARAM NAME=\"bpp\" VALUE=\"16\">\n";
	print "  <PARAM NAME=\"geometry\" VALUE=\"800x600\">\n";
	print "</APPLET>\n";
	print "</div>\n";
}*/

////////////////////////////////////////////////////////////////////////////////
///
/// \fn connectMindterm
///
/// \brief prints a page with an embedded mindterm client
///
////////////////////////////////////////////////////////////////////////////////
/*function connectMindterm() {
	global $user;
	$passwd = processInputVar("passwd", ARG_STRING);
	$serverIP = processInputVar("serverip", ARG_STRING);
	$requestid = processInputVar("requestid", ARG_NUMERIC);
	$requestData = getRequestInfo($requestid);
	$reserv = "";
	foreach($requestData["reservations"] as $key => $res) {
		if($res["reservedIP"] == $serverIP) {
			$reserv = $res;
			break;
		}
	}
	print "<H2 align=center>Connect!</H2>\n";
	print "<h3>{$reserv["prettyimage"]}</h3>\n";
	print "<UL>\n";
	print "<LI><b>Platform</b>: {$reserv["OS"]}</LI>\n";
	print "<LI><b>Remote Computer</b>: {$reserv["reservedIP"]}</LI>\n";
	print "<LI><b>User ID</b>: " . $user['unityid'] . "</LI>\n";
	if(strlen($reserv['password']))
		print "<LI><b>Password</b>: {$reserv['password']}<br></LI>\n";
	else
		print "<LI><b>Password</b>: (use your campus password)</LI>\n";
	print "</UL>\n";
	print "<APPLET CODE=\"com.mindbright.application.MindTerm.class\"\n";
	print "        ARCHIVE=\"mindterm-3.0/mindterm.jar\" WIDTH=0 HEIGHT=0>\n";
	print "  <PARAM NAME=\"server\" VALUE=\"$serverIP\">\n";
	print "  <PARAM NAME=\"port\" VALUE=\"22\">\n";
	print "  <PARAM NAME=\"username\" VALUE=\"{$user["unityid"]}\">\n";
	#print "  <PARAM NAME=\"password\" VALUE=\"$passwd\">\n";
	print "  <PARAM NAME=\"x11-forward\" VALUE=\"true\">\n";
	print "  <PARAM NAME=\"protocol\" VALUE=\"ssh2\">\n";
	print "  <PARAM NAME=\"sepframe\" VALUE=\"true\">\n";
	print "  <PARAM NAME=\"quiet\" VALUE=\"true\">\n";
	print "</APPLET>\n";
}*/

////////////////////////////////////////////////////////////////////////////////
///
/// \fn processRequestInput($checks)
///
/// \param $checks - (optional) 1 to perform validation, 0 not to
///
/// \return an array with the following indexes (some may be empty):\n
/// requestid, day, hour, minute, meridian, length, started, os, imageid,
/// prettyimage, time, testjavascript, lengthchanged
///
/// \brief validates input from the previous form; if anything was improperly
/// submitted, sets submitErr and submitErrMsg
///
////////////////////////////////////////////////////////////////////////////////
function processRequestInput($checks=1) {
	global $submitErr, $submitErrMsg, $mode;
	$return = array();
	$return["requestid"] = processInputVar("requestid", ARG_NUMERIC);
	$return["day"] = preg_replace('[\s]', '', processInputVar("day", ARG_STRING));
	$return["hour"] = processInputVar("hour", ARG_NUMERIC);
	$return["minute"] = processInputVar("minute", ARG_STRING);
	$return["meridian"] = processInputVar("meridian", ARG_STRING);
	$return["endday"] = preg_replace('[\s]', '', processInputVar("endday", ARG_STRING));
	$return["endhour"] = processInputVar("endhour", ARG_NUMERIC);
	$return["endminute"] = processInputVar("endminute", ARG_STRING);
	$return["endmeridian"] = processInputVar("endmeridian", ARG_STRING);
	$return["length"] = processInputVar("length", ARG_NUMERIC);
	$return["started"] = getContinuationVar('started', processInputVar("started", ARG_NUMERIC));
	$return["os"] = processInputVar("os", ARG_STRING);
	$return["imageid"] = getContinuationVar('imageid', processInputVar("imageid", ARG_NUMERIC));
	$return["prettyimage"] = processInputVar("prettyimage", ARG_STRING);
	$return["time"] = processInputVar("time", ARG_STRING);
	$return["revisionid"] = processInputVar("revisionid", ARG_MULTINUMERIC);
	$return["ending"] = processInputVar("ending", ARG_STRING, "length");
	$return["enddate"] = processInputVar("enddate", ARG_STRING);
	$return["extend"] = processInputVar("extend", ARG_NUMERIC);
	$return["testjavascript"] = processInputVar("testjavascript", ARG_NUMERIC, 0);
	$return['imaging'] = getContinuationVar('imaging');
	$return['lengthchanged'] = 0;

	if($return["minute"] == 0) {
		$return["minute"] = "00";
	}
	if($return["endminute"] == 0) {
		$return["endminute"] = "00";
	}

	if(! $checks) {
		return $return;
	}

	$noimage = 0;
	if(empty($return['imageid'])) {
		$submitErr |= IMAGEIDERR;
		$submitErrMsg[IMAGEIDERR] = "Please select a valid environment";
		$noimage = 1;
	}

	if(! $return["started"]) {
		$checkdateArr = explode('/', $return["day"]);
		if(! is_numeric($checkdateArr[0]) ||
		   ! is_numeric($checkdateArr[1]) ||
		   ! is_numeric($checkdateArr[2]) ||
		   ! checkdate($checkdateArr[0], $checkdateArr[1], $checkdateArr[2])) {
			$submitErr |= STARTDAYERR;
			$submitErrMsg[STARTDAYERR] = "The submitted start date is invalid.";
		}
		if(! preg_match('/^((0?[1-9])|(1[0-2]))$/', $return["hour"], $regs)) {
			$submitErr |= STARTHOURERR;
			$submitErrMsg[STARTHOURERR] = "The submitted hour must be from 1 to 12.";
		}
		if(! preg_match('/^([0-5][0-9])$/', $return["minute"], $regs)) {
			$submitErr |= STARTMINUTEERR;
			$submitErrMsg[STARTMINUTEERR] = "The submitted minute must be from 00 to 59.";
		}
		if(! preg_match('/^(am|pm)$/', $return["meridian"], $regs))
			$return['meridian'] = 'am';
		$checkstart = sprintf('%04d-%02d-%02d ', $checkdateArr[2], $checkdateArr[0],
		              $checkdateArr[1]);
		if($return['meridian'] == 'am') {
			if($return['hour'] == '12')
				$checkstart .= "00:{$return['minute']}:00";
			else
				$checkstart .= "{$return['hour']}:{$return['minute']}:00";
		}
		else {
			if($return['hour'] == '12')
				$checkstart .= "12:{$return['minute']}:00";
			else
				$checkstart .= ($return['hour'] + 12) . ":{$return['minute']}:00";
		}
	}

	# TODO check for valid revisionids for each image
	if(! empty($return["revisionid"])) {
		foreach($return['revisionid'] as $key => $val) {
			if(! is_numeric($val) || $val < 0)
				unset($return['revisionid']);
		}
	}

	// make sure user hasn't submitted something longer than their allowed max length
	$maxtimes = getUserMaxTimes();
	if($return['imaging']) {
		if($maxtimes['initial'] < 720) # make sure at least 12 hours available for imaging reservations
			$maxtimes['initial'] = 720;
	}
	if($maxtimes['initial'] < $return['length']) {
		$return['lengthchanged'] = 1;
		$return['length'] = $maxtimes['initial'];
	}
	if(! $noimage) {
		$imageData = getImages(0, $return['imageid']);
		if($imageData[$return['imageid']]['maxinitialtime'] > 0 &&
			$imageData[$return['imageid']]['maxinitialtime'] < $return['length']) {
			$return['lengthchanged'] = 1;
			$return['length'] = $imageData[$return['imageid']]['maxinitialtime'];
		}
	}

	if($return["ending"] != "length") {
		if(! preg_match('/^(20[0-9]{2})-([0-1][0-9])-([0-3][0-9]) (([0-1][0-9])|(2[0-3])):([0-5][0-9]):([0-5][0-9])$/', $return["enddate"], $regs)) {
			$submitErr |= ENDDATEERR;
			$submitErrMsg[ENDDATEERR] = "The submitted date/time is invalid.";
		}
		elseif(! checkdate($regs[2], $regs[3], $regs[1])) {
			$submitErr |= ENDDATEERR;
			$submitErrMsg[ENDDATEERR] = "The submitted date/time is invalid.";
		}
		elseif(! $return["started"] && datetimeToUnix($checkstart) >= datetimeToUnix($return['enddate'])) {
			$submitErr |= ENDDATEERR;
			$submitErrMsg[ENDDATEERR] = "The end time must be later than the start time.";
		}
	}

	if($return["testjavascript"] != 0 && $return['testjavascript'] != 1)
		$return["testjavascript"] = 0;
	return $return;
}
?>
