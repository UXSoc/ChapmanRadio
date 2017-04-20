<?php namespace ChapmanRadio;

class Stats {
	
	public static function show($showid = 0) {
		if($showid == 0) return [];
		$result = DB::GetAll("SELECT showid,`datetime`,chapmanradio+chapmanradiolowquality AS listeners FROM `stats` WHERE showid=$showid ORDER BY `datetime`");
		$ret = Stats::process($result);
		$ret["showid"] = $showid;
		return $ret;
		}
	
	public static function hour($timestamp) {
		// round start time
		$onehour = 60*60;
		$start = Floor($timestamp/$onehour)*$onehour;
		// set end time
		$end = $start + 60*60;
		// return
		$ret = Stats::within($start, $end, -1);
		return $ret;
		}
	
	public static function within($start, $end, $showid = 0) {
	
		$start = date('Y-m-d H:i:s', $start);
		$end = date('Y-m-d H:i:s', $end);
		$query = "SELECT showid, `datetime`, chapmanradio+chapmanradiolowquality AS listeners FROM stats WHERE ";
		if($showid > 0) $query .= "showid=$showid AND ";
		$query .= "`datetime` >= '$start' AND `datetime` <= '$end' ORDER BY `datetime` ";
		$result = DB::GetAll($query);
		$ret = Stats::process($result);
		return $ret;
	}

	private static function process($result) {
		# Note - 
		#  the input must be a result from a query
		#  that has the fields:   datetime, listeners, showid
		#
		// general variables
		$stats = array();
		$dateFormat = "D ga, n/j/Y";
		$lastTimestamp = 0;
		$prev = ""; $cur = -1;
		$alltimepeak = 0;
		// process the input
		foreach($result as $row){
			extract($row);
			$timestamp = strtotime($datetime);
			if($prev != date($dateFormat,$timestamp)) {
				$cur++;
				$stats[$cur] = array();
				$stats[$cur]['timestamp'] = $timestamp;
				$stats[$cur]['showid'] = $showid;
				$stats[$cur]['label'] = date("l ga - M jS, Y", $timestamp);
				$stats[$cur]['data'] = array();
				$prev = date($dateFormat,$timestamp);
			}
			$listeners = ($listeners > 0) ? intval($listeners) : 0;
			$stats[$cur]['data'][intval(date('i',$timestamp))] = $listeners;
			}
		// clean up & calculate alltime peak
		foreach($stats as $key => $data) {
			$stats[$key] = Stats::cleanUp($data);
			if($stats[$key]['peak'] > $alltimepeak) $alltimepeak = $stats[$key]['peak'];
			}
		// return info
		return array("alltimepeak"=>$alltimepeak,"stats"=>$stats);
		}
	
	private static function cleanUp($data) {
		// set the peak and average for the data
		$data['peak'] = max($data['data']);
		$data['average'] = round(10*array_sum($data['data'])/count($data['data']))/10;
		// fill in any omitted values
		$prevNum = 0;
		for($i = 0;$i < 60;$i++) {
			if(isset($data['data'][$i])) {
				$prevNum = $data['data'][$i];
			}
			else $data['data'][$i] = $prevNum;
		}
		// sort by key (minute)
		ksort($data['data']);
		// all done
		return $data;
		}
	
	public static function draw($stats) {
		// $stats needs to be a result for a Stats::process, like Stats::hour, or Stats:show
		if(!$stats) return "";
		$stats = isset($stats['stats'][0]) ? $stats['stats'][0] : 0;
		if(!$stats) return "";
		
		// create image and set background to gloss
		$imagewidth = 420;
		$imageheight = 300;
		$im = imagecreatetruecolor($imagewidth, $imageheight);
		$gloss = imagecreatefrompng(PATH."img/bg/glossbg.png");
		imagecopy($im, $gloss, 0, 0, 0, 0, $imagewidth, $imageheight);
		imagedestroy($gloss);
		
		// draw a border
		$gray = imagecolorallocate($im, 200, 200, 200);
		imagerectangle($im, 0, 0, $imagewidth-1, $imageheight-1, $gray);
		
		$colors = array();
		
		$peak = $stats['peak'];
		$average = $stats['average'];
		$minheight = 10; // also change these below
		$width = 4; // gd draws a border, so this is 5 in javascript
		$spacing = 6;
		
		$timestamp = $stats['timestamp'];
		$temp = DB::GetFirst("SELECT showname FROM shows WHERE showid='$stats[showid]'");
		$showname = @$temp['showname'];
		if(!$showname) $showname = " - Stats - ";
		$showname = strtoupper($showname);
		
		// let's create a pretty header
		$blue1 = imagecolorallocate($im, 146, 171, 183);
		$blue2 = imagecolorallocate($im, 116, 138, 149);
		$date = date("M j - ga", $timestamp);
		$date = strtoupper($date);
		$fontfile = PATH."css/fonts/gotham-ultra.ttf";
		// show name
		$fontsize = 18;
		$bbox = imagettfbbox($fontsize, 0, $fontfile, $showname);
		imagettftext($im, $fontsize, 0, round(($imagewidth - ($bbox[4] - $bbox[0]) )/2), 24, $blue2, $fontfile, $showname);
		// date
		$fontsize = 12;
		$bbox = imagettfbbox($fontsize, 0, $fontfile, $date);
		imagettftext($im, $fontsize, 0, round(($imagewidth - ($bbox[4] - $bbox[0]) )/2), 46, $blue1, $fontfile, $date);
		
		// the bars will be displaced according to these "box" dimensions
		// its like the container in the HTML version
		$boxheight = 200; // also change these below
		$boxwidth = 360;
		$boxtop = 80;
		$boxleft = 40;
		
		// labels setting
		$labelindent = -22;
		$labelheight = 14;
		$labelcolor = imagecolorallocate($im, 184, 184, 184);
		$numlabels = 3;
		if($peak < 3) $numlabels = $peak;
		if($numlabels < 2) $numlabels = 1;
		$displaypeak = $peak + 1;
		
		// draw the stats guides
		$eachvalue = ceil(($peak+1)/$numlabels);
		for($i = 0;$i <= $numlabels;$i++) {
			$yoffset = Stats::calcHeight($i*$eachvalue, $displaypeak);
			if($i*$eachvalue >= $boxheight) continue;
			imagerectangle($im, $boxleft + $labelindent, $boxtop+$yoffset+$minheight, $boxleft+$boxwidth, $boxtop+$yoffset+$minheight, $labelcolor);
			imagestring($im, 3, $boxleft + $labelindent, $boxtop+$yoffset+$minheight-$labelheight, $i*$eachvalue, $labelcolor);
		}
		
		// process the stats
		foreach($stats['data'] as $min => $num) {
			$color = Stats::createColor($min, $num, $displaypeak);
			$ckey = implode(",", $color);
			if(!isset($colors[$ckey])) {
				list($r,$g,$b) = $color;
				$colors[$ckey] = imagecolorallocate($im, $r, $g, $b);
			}
			
			$x1 = $boxleft + ($min * $spacing);
			$y1 = $boxtop + Stats::calcHeight($num, $displaypeak) + $minheight;
			$x2 = $boxleft + ($min * $spacing) + $width;
			$y2 = $boxtop + $boxheight;
			
			imagefilledrectangle($im, $x1, $y1, $x2, $y2, $colors[$ckey]);
		}
		
		
		$img = "img/stats/".date("Y", $timestamp)."/";
		if(!file_exists(PATH.$img)) mkdir(PATH.$img);
		$img .= date("m", $timestamp)."/";
		if(!file_exists(PATH.$img)) mkdir(PATH.$img);
		$img .= $timestamp.".png";
		imagepng($im, PATH.$img, 6);
		imagedestroy($im);
		return "/".$img;
	}
	
	private static function createColor($min, $num, $peak) {
		// this algorithm is the same as js/dj-shows.js
		// except there is no $index variable, bc there is only one, so it doesnt alternate btwn even and odd
		$r = 150 - round(2.5*$min);
		$g = round(220*$num/$peak);
		$b = 175 - round(175*$num/$peak);
		return array($r, $g, $b);
	}
	
	private static function calcHeight($num, $peak) {
		$boxheight = 200;
		$minheight = 10;
		return (1 - $num/($peak+1))*($boxheight - $minheight);
	}
	
	public static function generateHTML($showid){
		$html = "<h2>Listenership Statistics</h2><br />";
		$html .= "<div class='stats_title'>";
		$html .= "<a class='stats_prev' onclick='stats.prev($showid);'><img src='/img/icons/prev_icon.png' alt='&lt;' /></a>";
		$html .= "<a class='stats_next' onclick='stats.next($showid);'><img src='/img/icons/next_icon.png' alt='&gt;' /></a>";
		$html .= "<div class='stats_label'><i>Loading...</i></div>";
		$html .= "</div>";
		$html .= "<div class='stats_container'>";
		$html .= "<div class='stats_listeners'></div>";
		$html .= "<div class='stats_peak'></div>";
		$html .= "<div class='stats_average'></div>";
		for($i = 0;$i< 60;$i++) $html .= "<div class='stats_bar stats_hour$i' title='$i' style='left:".($i*6)."px'></div>";
		$html .= "</div>";
		return $html;
	}
	
	public static function generateJSON(){
		
		$request = @$_REQUEST['request'] or "";
		$direction = @$_REQUEST['direction'] or "";
		if(!$request) die(json_encode(array("error"=>"Missing request variable: request")));
		if(!$direction) die(json_encode(array("error"=>"Missing request variable: direction")));
		if($direction != "up" && $direction != "down") die(json_encode(array("error"=>"direction can only be 'up' or 'down', '$direction' was entered")));
		preg_match("/(\\d{4})-(\\d{1,2})-?(\\d{1,2})?/",$request, $matches);
		
		$day = null;
		if(count($matches) == 4)
			list(,$year,$month,$day) = $matches;
		else
			list(,$year,$month) = $matches;
		
		if(!$month) die(json_encode(array("error"=>"invalid request: $request")));
		$type = $day ? "day" : "month";
		// make sure we have a valid day
		if($direction == "up") {
			if($type == "day") {
				while(!checkdate($month, $day, $year)) {
					if(++$day > 31) {
						$day = 1;
						if(++$month > 12) {
							$year++;
							$month = 1;
						}
					}
				}
			} else {
				while(!checkdate($month, 1, $year)) {
					if(++$month > 12) {
						$year++;
						$month = 1;
					}
				}
			}
		} else {
			if($type == "day") {
				while(!checkdate($month, $day, $year)) {
					if(--$day <= 0) {
						$day = 31;
						if(--$month <= 0) {
							$year--;
							$month = 1;
						}
					}
				}
			} else {
				while(!checkdate($month, 1, $year)) {
					if(--$month <= 0) {
						$year--;
						$month = 12;
					}
				}
			}
		}
		// clean up 1 digit ints
		if($month < 10) $month = "0".((int)$month);
		if($day < 10) $day = "0".((int)$day);
		// alright, let's start fetching some data!
		$overallPeak = 0;
		$labels = array();
		$stats = array();
		$peaks = array();
		$averages = array();
		if($type == "day") {
			$baseTimestamp = strtotime("$year-$month-$day 00:00:00");
			for($i = 5; $i <= 28; $i++) {
				$startdatetime = date("Y-m-d H:i:s",$baseTimestamp + 60*60*$i);
				$enddatetime = date("Y-m-d H:i:s",$baseTimestamp + 60*60*($i+1) - 1);
				$temp = DB::GetFirst("SELECT MAX(chapmanradio+chapmanradiolowquality) as peak, AVG(chapmanradio+chapmanradiolowquality) as average FROM stats WHERE `datetime` >= '$startdatetime' AND `datetime` <= '$enddatetime'");
				$peak = $temp['peak'] ? (int)$temp['peak'] : 0;
				$average = $temp['average'] ? (int)$temp['average'] : 0;
				if($peak > $overallPeak) $overallPeak = $peak;
				$peaks[] = $peak;
				$averages[] = $average;
				$labels[] = Util::hourName($i);
				$stats[] = array($peak,$average);
			}
		}
		else {
			$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			$baseTimestamp = strtotime("$year-$month-01 00:00:00");
			$i = 0;
			for($i = 1;$i <= $daysInMonth;$i++) {
				$startdatetime = date("Y-m-d H:i:s",$baseTimestamp + 60*60*24*($i-1));
				$enddatetime = date("Y-m-d H:i:s",$baseTimestamp + 60*60*24*($i) - 1);
				$temp = DB::GetFirst("SELECT MAX(chapmanradio+chapmanradiolowquality) as peak, AVG(chapmanradio+chapmanradiolowquality) as average FROM stats WHERE `datetime` >= '$startdatetime' AND `datetime` <= '$enddatetime'");
				$peak = $temp['peak'] ? (int)$temp['peak'] : 0;
				$average = $temp['average'] ? (int)$temp['average'] : 0;
				if($peak > $overallPeak) $overallPeak = $peak;
				$peaks[] = $peak;
				$averages[] = $average;
				$labels[] = $i;
				$stats[] = array($peak,$average);
			}
			for(;$i <= 31;$i++) {
				$labels[] = $i;
				$stats[] = array(-1,-1);
			}
		}
		$averagePeak = round(100*array_sum($peaks)/count($peaks))/100;
		$averageAverage = round(100*array_sum($averages)/count($averages))/100;
		$request = ($type == "day") ? "$year-$month-$day" : "$year-$month";
		checkdate($month, $day, $year);
		$label = $type=="day" ? date("l, F jS, Y",$baseTimestamp) : date("F, Y",$baseTimestamp);
		$prevDay2 = date("Y-m-d", $baseTimestamp - 60*60*24*2);
		$prevDay = date("Y-m-d", $baseTimestamp - 60*60*24);
		$nextDay = date("Y-m-d", $baseTimestamp + 60*60*24);
		$nextDay2 = date("Y-m-d", $baseTimestamp + 60*60*24*2);
		$prevMonth2 = date("Y-m", $baseTimestamp - 60*60*24*24*2);
		$prevMonth = date("Y-m", $baseTimestamp - 60*60*24*24);
		$nextMonth = date("Y-m", $baseTimestamp + 60*60*24*32);
		$nextMonth2 = date("Y-m", $baseTimestamp + 60*60*24*32*2);
		$loadOnReceive = @$_REQUEST['loadOnReceive'] ? true : false;
		$response = array("request"=>$request,"type"=>$type,"label"=>$label,"overallPeak"=>$overallPeak,"labels"=>$labels,"stats"=>$stats,"averagePeak"=>$averagePeak,"averageAverage"=>$averageAverage,"prevDay2"=>$prevDay2,"prevDay"=>$prevDay,"nextDay"=>$nextDay,"nextDay2"=>$nextDay2,"prevMonth"=>$prevMonth,"nextMonth"=>$nextMonth,"prevMonth2"=>$prevMonth2,"nextMonth2"=>$nextMonth2,"loadOnReceive"=>$loadOnReceive);
		die(json_encode($response));
		}
	
	}
