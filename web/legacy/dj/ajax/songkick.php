<?php namespace ChapmanRadio;

define('PATH', '../../');
require_once PATH."inc/global.php";

function ezDate($timestamp, $today, $tomorrow) {
	if(date("Y-m-d",$timestamp) == $today) return date("g:ia",$timestamp).", Today";
	else if(date("Y-m-d",$timestamp) == $tomorrow) return date("g:ia",$timestamp).", Tomorrow";
	else return date("g:ia, F jS",$timestamp);
}

Util::CatchErrors(function() use(&$data){
	$skkey = Site::$SongKickApiKey;
	$sklocation = Site::$SongKickLocation;
	$limit = 12;
	$data = json_decode(file_get_contents("http://api.songkick.com/api/3.0/metro_areas/$sklocation/calendar.json?apikey=$skkey&per_page=$limit"), true);
	$data = $data["resultsPage"]["results"]["event"];
	}, function($e){ die("Unable to load data: ".$e->getMessage()); });

$html = "<h3>Upcoming Concerts</h3><div class='ajaxresults'>";
$today = date("Y-m-d");
$tomorrow = date("Y-m-d",strtotime("tomorrow"));
foreach($data as $item) {
	$timestamp = strtotime($item['start']['date']." ".$item['start']['time']);
	$date = ezDate($timestamp,$today,$tomorrow);
	$month = date("M",$timestamp);
	$day = date("j",$timestamp);
	$title = $item['displayName'];
	if(preg_match("/(\\(([^)]*)\\))/", $title, $matches) && strtotime($matches[2])) $title = preg_replace("/(\\([^)]*\\))/","",$title);
	if(!$timestamp) $date = $item['start']['datetime'];
	$html .= "<a href='$item[uri]' target='_blank'><div class='title'>$title</div><table cellspacing='0' cellpadding='0'><tr><td class='icon'><div class='calendaricon'><span class='month'>$month</span><span class='day'>$day</span></div></td><td><dl>
		<dt>Location</dt><dd>{$item['location']['city']}</dd>
		<dt>Date</dt><dd>$date</dd>
		<dt>Venue</dt><dd>{$item['venue']['displayName']}</dd>
	</dl></td></tr></table></a>";
	}
$html .= "</div><p class='ajaxdatafrom'>Concert data from <a href='http://songkick.com' target='_blank'>Songkick</a></p>";
print $html;
exit;