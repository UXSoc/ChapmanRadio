<?php namespace ChapmanRadio;

define('PATH', '../../');
require_once PATH."inc/global.php";

$streams = array("High Quality Stream" => "chapmanradio", "Low Quality Stream" =>"chapmanradiolowquality");

$stats = DB::GetFirst("SELECT ".implode(",", $streams).", datetime FROM stats ORDER BY datetime DESC LIMIT 0,1");

$html = "<table class='formtable' cellspacing='0' cellpadding='0' style='width:300px;'>";
$total = 0;
foreach($streams as $streamName => $stream) {
	$listeners = $stats[$stream];
	//$listeners = rand(10, 1000000);
	$rowclass = (++$total % 2 == 0) ? 'evenRow' : 'oddRow';
	if($listeners < 0 && false) {
		$html .= "<tr class='$rowclass'><td><img src='/img/icons/listeners.png' /></td><td><span style='color:red;'><b>$streamName</b> is down.</span><br /><small>Contact someone on the technical team for assistance.</small></td></tr>";
		}
	else {
		$html .= "<tr class='$rowclass'><td><img src='/img/icons/listeners.png' /></td><td><span style='color:#848484'>$streamName</span><br />".($listeners == 1 ? "<b>1 Listener</b>" : "<b>$listeners Listeners</b>")."</td></tr>";
		}
	}
$html .= "</table>";

$timestamp = strtotime($stats['datetime']);
// are the stats older than 20 minutes?
if(Site::$Broadcasting) {
	if(time() - 60*20 > $timestamp ) {
		$html .= "<p><small><b>Out of date?</b> These stats were last updated <b>".Util::timeDifference($timestamp)." ago</b>.<br />This delay is caused either by (1) no changes in listenership, or (2) the broadcasting server is down.</small></p>";
		}
	}
else {
	$html .= "<p><small>These stats were last updated <b>".Util::timeDifference($timestamp)." ago</b>, and they will not be updated again until a staff member <a href='/staff/advanced'>re-enables broadcasting</a>.</small></p>";
	}

print $html;
exit;