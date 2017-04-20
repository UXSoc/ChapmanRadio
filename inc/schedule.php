<?php namespace ChapmanRadio;

class Schedule {
	
	private static $schedule_cache = array();
	
	private static $alteration_cache_start = NULL;
	private static $alteration_cache_end = NULL;
	private static $alteration_cache = array();
	
	public static $genres;
	public static $genreClasses;
	public static $colors = array(
		array("rgb(174,105,254)", "rgb(66,0,152)"),
		array("#E475DB", "#830779"), // rgb(254,105,219)","rgb(104,40,40)"), // 152,0,114)"),
		array("#EF7A93", "#3A000D"), // 900825"), // rgb(254,105,138)","rgb(104,6,6)"),
		array("rgb(254,226,105)","rgb(152,122,0)"),
		array("#7481E4", "#071583"),
		array("#A2EA77", "#388A07"), // rgb(93,213,23)","rgb(56,138,7)"), // ("rgb(158,103,208)","rgb(92,15,162)"),
		array("rgb(230,230,230)","rgb(100,100,100)"),
		array("rgb(74,219,252)","rgb(0,54,95)"),
		array("rgb(255,175,82)","rgb(114,63,31)"),
		array("rgb(184,205,232)","rgb(2,60,10)"),
		array("rgb(144,206,156)","rgb(6,100,6)"),
		array("rgb(224,237,248)","rgb(71,98,165)"),
		array("rgb(184,184,184)","rgb(57,57,57)"),
		array("rgb(194,216,57)","rgb(84,122,22)")
		);
		
	private static $days = array("mon","tue","wed","thu","fri","sat","sun");
	private static $dayNames = array("mon"=>"Monday","tue"=>"Tuesday","wed"=>"Wednesday","thu"=>"Thursday","fri"=>"Friday","sat"=>"Saturday","sun"=>"Sunday");
	
	public static function cycle($timestamp = 0) {
		if(!$timestamp) $timestamp = time();
		return (date("W", $timestamp)%2!=0) ? 1 : 2;
	}
	
	/* Use wrappers in the following frequencies: */
	
	/* 1. What is happening with All alterations */
	public static function HappenedAt($timestamp){ return Schedule::GetShowAt($timestamp, 'all'); }
	public static function HappeningNow(){ return Schedule::GetShowAt(time(), 'all'); }
	public static function HappeningSoon($count){ return Schedule::NextShows(time(), 'all', $count); }
	public static function HappeningFrom($start, $end){ return Schedule::ShowsFrom($start, $end, 'all'); }
	
	/* 2. What is scheduled with Staff alterations (could show a show that didn't show up) */
	public static function ShouldHappenAt($timestamp){ return Schedule::GetShowAt($timestamp, 'staff'); }
	public static function ShouldHappenNow(){ return Schedule::GetShowAt(time(), 'staff'); }
	public static function ShouldHappenSoon($count){ return Schedule::NextShows(time(), 'staff', $count); }
	public static function ShouldHappenFrom($start, $end){ return Schedule::ShowsFrom($start, $end, 'staff'); }
	
	/* 3. What is usually happening with No alterations */
	public static function ScheduledAt($timestamp){ return Schedule::GetShowAt($timestamp, 'none'); }
	public static function ScheduledNow(){ return Schedule::GetShowAt(time(), 'none'); }
	public static function ScheduledSoon($count){ return Schedule::NextShows(time(), 'none', $count); }
	public static function ScheduledFrom($start, $end){ return Schedule::ShowsFrom($start, $end, 'none'); }
	
	public static function alter($start, $end, $showid, $note="") {
		
		// Alterations should never overlap. If one exists where we want to put this one, fix it

		// if an existing start is within our new range: move the offending start to the end of this one
		$result = DB::GetAll("SELECT * FROM alterations WHERE starttimestamp >= '$start' AND starttimestamp <= '$end'");
		foreach($result as $row){
			if($row['endtimestamp'] > $end)
				DB::Query("UPDATE alterations SET starttimestamp = :start WHERE alterationid=:id", array(
					":start" => $end+1,
					":id" => $row['alterationid']
					));
			else
				DB::Query("DELETE FROM alterations WHERE alterationid = :id", array(
					":id" => $row['alterationid']
					));
			}
		
		// if an existing endtime is within our new range: move the offending end to the start of this one
		$result = DB::GetAll("SELECT * FROM alterations WHERE endtimestamp >= '$start' AND endtimestamp <= '$end'");
		foreach($result as $row){
			if($row['starttimestamp'] < $start)
				DB::Query("UPDATE alterations SET endtimestamp = :end WHERE alterationid = :id", array(
					":end" => $start-1,
					":id" => $row['alterationid']
					));
			else
				DB::Query("DELETE FROM alterations WHERE alterationid = :id", array(
					":id" => $row['alterationid']
					));
			}
		
		// if the existing range spans our new range: break existing range into 2 ranges
		$result = DB::GetAll("SELECT * FROM alterations WHERE starttimestamp <= '$start' AND endtimestamp >= '$end'");
		foreach($result as $row){
			DB::Query("UPDATE alterations SET endtimestamp = ".($start-1)." WHERE alterationid='$row[alterationid]'");
			DB::Query("INSERT INTO alterations (starttimestamp,endtimestamp,showid,note) VALUES (".($end+1).",$row[endtimestamp],$row[showid],'$row[note]')");
			}
		
		DB::Insert("alterations", array(
			"starttimestamp" => $start,
			"endtimestamp" => $end,
			"showid" => $showid,
			"alteredby" => Session::getCurrentUserID(),
			"note" => $note));
		}
	
	// Use me!
	public static function GetShowAt($timestamp, $alterationmode='none', $season = NULL){
	
		if(!Site::$Broadcasting && $season == NULL) return 0;
		
		$alteration = Schedule::GetAlteration($timestamp, $alterationmode);
		if($alteration != NULL) return $alteration;
		
		list($hour, $day, $cycle) = Schedule::TimestampToHourDayCycle($timestamp);
		
		// echo $timestamp . " = " . $hour . "|" . $day . "|" . $cycle . "<br />";
		
		if($season === NULL) $season = Site::ScheduleSeason();
		
		return Schedule::GetScheduledShowAt($season, $hour, $day, $cycle);
		
		}
	
	public static function HasShows($season = 0){
		$schedule = Schedule::GetScheduleObject($season);
		
		foreach($schedule as $hour){
			foreach($hour as $day => $shows){
				if(Schedule::ShowFromScheduleField($shows, 1) != 0) return true;
				if(Schedule::ShowFromScheduleField($shows, 2) != 0) return true;
				}
			}
		
		return false;
		}
	
	public static function ShowsFrom($timestamp, $endstamp, $alterationmode){
		$results = array();
		$timestamp = strtotime(date("Y-m-d H:00:00", $timestamp));
		while($timestamp < $endstamp){
			$show = Schedule::GetShowAt($timestamp, $alterationmode);
			if($show) $results[$timestamp] = $show;
			$timestamp += 3600;
		}
		return $results;
		}
	
	public static function NextShows($timestamp, $alterationmode, $count, $future_limit = 24){

		$results = array();
		
		$timestamp = strtotime(date("Y-m-d H:00:00",$timestamp));
		
		$counter = 0; $future = 0;
		while($counter < $count && $future < $future_limit){
			$show = Schedule::GetShowAt($timestamp, $alterationmode);
			
			if($show){
				$counter++;
				$results[$timestamp] = $show;
				}
			
			$timestamp += 3600;
			$future++;
		}
		
		return $results;
		}
	
	public static function HandleUserLogin($user){
		// should we redirect to /dj/live if they're scheduled?
		$now = time();
		$prevShowid = Schedule::ShouldHappenAt($now - 3600);
		$curShowid = Schedule::ShouldHappenAt($now);
		$nextShowid = Schedule::ShouldHappenAt($now + 3600);
		foreach($user->GetShows() as $show){
			switch($show['showid']){
				case $prevShowid:
				case $curShowid:
				case $nextShowid:
					if(isset($_SESSION['redirectPageName'])) unset($_SESSION['redirectPageName']);
					if(isset($_SESSION['redirect'])) unset($_SESSION['redirect']);
					header("Location: /dj/live");
					exit;
					break;
				}
			}
		return true;
		}
	
	public static function PreFetch($from, $to){
		
		self::$alteration_cache_start = (self::$alteration_cache_start===NULL) ? $from : min($from, self::$alteration_cache_start);
		self::$alteration_cache_end = (self::$alteration_cache_end===NULL) ? $to : max($to, self::$alteration_cache_end);
		
		$alterations = DB::GetAll("SELECT * FROM alterations WHERE starttimestamp >= '$from' AND endtimestamp <= '$to'");
		foreach($alterations as $alteration){
			self::$alteration_cache[$alteration['starttimestamp']] = $alteration;
			}
		}
	
	public static function styleGenres() {
		$genres = Site::$Genres;
		$colors = Schedule::$colors;
		$count = count($colors);
		$style = "";
		foreach($genres as $index => $genre) {
			$genre = preg_replace("/\\W/","",$genre);
			list($color1,$color2) = $colors[$index % $count];
			$style .= ".$genre { background:$color1; color:$color2; }";
		}
		return $style;
	}
	
	public static function nextShow($showid, $season = '') {
		if(!$season) $season = Season::schedule();
		$schedule = Schedule::GetScheduleObject($season);
		ksort($schedule);
		
		$timestamp = 0;
		$now = time();
		foreach($schedule as $hour => $dat) {
			foreach($dat as $day => $showids) {
				$showids = explode(",",$showids);
				if(in_array($showid, $showids)) {
					$dayIndex = array_search($day, Schedule::$days);
					$todayIndex = array_search(strtolower(date("D")),Schedule::$days);
					if($dayIndex == $todayIndex) $timestamp = strtotime( "today 12:00am" );
					else $timestamp = strtotime( "next ".Schedule::$dayNames[$day]." 12:00am" );
					$timestamp += 3600*$hour;
					for($a = 0;$a <= 3;$a++) {
						$scheduledshowid = Schedule::HappenedAt($timestamp);
						if($timestamp + 3600 > $now && $scheduledshowid == $showid) return $timestamp;
						$timestamp += 3600*24*7;
					}
				}
			}
		}
		return 0;
	}
	
	public static function GetBroadcastsBetween($showid, $start, $end, $season = 0){
		
		if(!$season) $season = Site::ScheduleSeason(true);
		$schedule = Schedule::GetScheduleObject($season);
		ksort($schedule);
		
		$timestamp = 0;
		
		$results = array();
		
		foreach($schedule as $hour => $dat) {
			foreach($dat as $day => $showids) {
				$showids = explode(",",$showids);
				if(!in_array($showid, $showids)) continue;
				
				// move to the correct day and time
				$timestamp = strtotime( "next ".Schedule::$dayNames[$day]." 12:00am" );
				$timestamp += 3600*$hour;
				
				// shift back in time to the start
				while($timestamp > $start) $timestamp -= (3600*24*7);
				
				// while not the end, go through the weeks looking for this show
				while($timestamp <= $end){
					if($timestamp >= $start){
						$scheduledshowid = self::GetShowAt($timestamp, 'all', $season); // Happened with all alterations
						if($scheduledshowid == $showid) $results[] = $timestamp; // ($hour > 24) ? $timestamp - (3600*24) : $timestamp;
						}
					$timestamp += (3600*24*7);
					}
				}
			}
			
		// print_r($results);
		
		sort($results);
		
		return $results;
		}
	
	public static function getShowTimes($showid, $season = "") {
		if(!$season) $season = Season::schedule();
		$schedule = self::GetScheduleObject($season);
		ksort($schedule);
		$days = array("mon","tue","wed","thu","fri","sat","sun");
		$dayNames = array("mon"=>"Monday","tue"=>"Tuesday","wed"=>"Wednesday","thu"=>"Thursday","fri"=>"Friday","sat"=>"Saturday","sun"=>"Sunday");
		$removals = array();
		$now = time();
		foreach($schedule as $hour => $dat) {
			foreach($dat as $day => $showids) {
				$showids = explode(",", $showids);
				if(!isset($showids[1])) $showids[1] = '';
				if(in_array($showid, $showids)) {
					if($hour < 12) $ampm = "am";
					else if($hour < 24) $ampm = "pm";
					else $ampm = "am";
					
					if($showids[0] == $showids[1]) $removals[] = "Every {$dayNames[$day]} at ".($hour%12)."$ampm";
					else $removals[] = "Every other {$dayNames[$day]} at ".($hour%12)."$ampm";
				}
			}
		}
		return $removals;
	}
	
	public static function cancel($showid, $season = ""){
		if(!$season) $season = Season::schedule();
		$schedule = self::GetScheduleObject($season);
		ksort($schedule);
		$removals = array();
		$now = time();
		foreach($schedule as $hour => $dat) {
			foreach($dat as $day => $showids) {
				$showids = explode(",",$showids);
				if(!@$showids[1]) $showids[1] = '';
				if(in_array($showid, $showids)) {
					if($hour < 12) $ampm = "am";
					else if($hour < 24) $ampm = "pm";
					else $ampm = "am";
					
					if($showids[0] == $showids[1]) $removals[] = "Every ".Schedule::$dayNames[$day]." at ".($hour%12)."$ampm";
					else $removals[] = "Every other ".Schedule::$dayNames[$day]." at ".($hour%12)."$ampm";
					
					if($showids[0] == $showid) $showids[0] = '';
					if($showids[1] == $showid) $showids[1] = '';
					DB::Query("UPDATE schedule SET `$day` = '".implode(",",$showids)."' WHERE hour='$hour' AND season='$season'");
				}
			}
		}
		return $removals;
	}
	
	public static function GetScheduledShowAt($season, $hour, $day, $cycle, $usecache = true){
		$obj = Schedule::GetScheduleObject($season, $usecache);
		if(!isset($obj[$hour])) return 0;
		if(!isset($obj[$hour][$day])) return 0;
		return Schedule::ShowFromScheduleField($obj[$hour][$day], $cycle);
		}
	
	public static function GetAllShowIds($season = 0, $usecache=true){
		$results = array();
		$obj = Schedule::GetScheduleObject($season, $usecache);
		foreach($obj as $hour) {
			foreach(Schedule::$days as $day) {
				$showids = explode(",", $hour[$day]);
				foreach($showids as $showid) if($showid && !in_array($showid, $results)) $results[] = $showid;
				}
			}
		return $results;
		}
	
	private static function GetScheduleObject($season = 0, $usecache = true){
		
		if(!$season) $season = Season::Schedule();
		if($usecache && isset(Schedule::$schedule_cache[$season])) return Schedule::$schedule_cache[$season];
		
		$result = DB::GetAll("SELECT * FROM schedule WHERE season='$season'");
		$schedule = array();
		foreach($result as $row) $schedule[$row['hour']] = $row;
		if($usecache) Schedule::$schedule_cache[$season] = $schedule;
		
		return $schedule;
		}
	
	// Only works for very specific alterations
	private static function GetCachedAlteration($timestamp, $mode){
		$start_timestamp = strtotime( date("Y-m-d H:00:00", $timestamp) );
		
		// Is this a nice alteration? If not we can't handle it
		if($start_timestamp != $timestamp) return FALSE;
		
		$end_timestamp = $start_timestamp + 3599;
		
		// Did we cache enough to cover this alteration? If not we can't handle it
		if(self::$alteration_cache_start > $start_timestamp || self::$alteration_cache_end < $end_timestamp) return FALSE;
		
		// Ok, if there is an alteration for this hour, then it will be in here
		// If not, then there is no alteration
		if(!isset(self::$alteration_cache[$start_timestamp])) return NULL;
		
		$alteration = self::$alteration_cache[$start_timestamp];
		
		if($alteration['endtimestamp'] != $end_timestamp) return FALSE;
		
		if($mode == 'all'){
			return $alteration["showid"];
			}
		
		if($mode == 'staff'){
			if($alteration['alteredby'] != 0) return $alteration["showid"];
			}
		
		return NULL;
		}
		
	private static function GetAlteration($timestamp, $mode, $usecache = true){
	
		// $alterations = 'all'			-> What is happening (with all alterations, such as DJ Live Tardies)
		// $alterations = 'staff'		-> What should have happened (with staff alterations)
		// $alterations = 'none'		-> What was supposed to happen (with no alterations)
		
		if($mode == 'none') return NULL;
		
		if($usecache){
			$cached_alteration = self::GetCachedAlteration($timestamp, $mode);
			if($cached_alteration !== FALSE) return $cached_alteration;
			}
		
		if($mode=='all'){
			$alteration = DB::getFirst("SELECT showid FROM alterations WHERE starttimestamp <= '$timestamp' AND endtimestamp >= '$timestamp' ORDER BY alterationid DESC LIMIT 0,1");
			if($alteration) return $alteration["showid"];
			}
		
		else if($mode=='staff'){
			$alteration = DB::getFirst("SELECT showid FROM alterations WHERE starttimestamp <= '$timestamp' AND endtimestamp >= '$timestamp' AND alteredby<>'0' ORDER BY alterationid DESC LIMIT 0,1");
			if($alteration) return $alteration["showid"];
			}
		
		return null;
		}
	
	private static function TimestampToHourDayCycle($timestamp){
		$hour = date("G",$timestamp);
		if($hour < 5) {
			// ug fuck DST
			$timestamp = strtotime("-1 day", $timestamp);
			// $timestamp -= 86400; // move back 1 day
			$hour += 24;
		}
		$day = strtolower(date("D",$timestamp));
		$cycle = Schedule::cycle($timestamp);
		return array($hour, $day, $cycle);
		}
	
	private static function ShowFromScheduleField($field, $cycle){
		
		if(empty($field)) return 0;
		
		if(strpos($field, ",") === false) return $field;
		list($id1, $id2) = explode(",", $field);
		
		if($cycle == 1) return !empty($id1) ? $id1 : 0;
		else return !empty($id2) ? $id2 : 0;
		}
	
	public static function HandleChange($showid=0, $season=""){
		if($showid <= 0) return false;
		if(!$season) $season = Site::CurrentSeason();
		$status = "finalized";
		$showtimes = array();
		$days = array("mon"=>"Monday","tue"=>"Tuesday","wed"=>"Wednesday","thu"=>"Thursday","fri"=>"Friday","sat"=>"Saturday","sun"=>"Sunday");
		$result = DB::GetAll("SELECT hour,mon,tue,wed,thu,fri,sat,sun FROM schedule WHERE season = '$season'");
		
		// regenerate status: if a show is on the schedule its accepted; otherwise its finalized
		foreach($result as $row){
			foreach($days as $day => $dispDay) {
				list($showid1, $showid2) = explode(",", $row[$day]);
				if($showid1 == $showid2 && $showid1 == $showid) {
					$showtimes[] = "every $dispDay at ".Util::hourName($row['hour']);
					$status = 'accepted';
					}
				else if($showid1 == $showid || $showid2 == $showid) {
					$showtimes[] = "every other $dispDay at ".Util::hourName($row['hour']);
					$status = 'accepted'; 
					}
				} 
			}
		$showtime = Util::engList($showtimes);
		$showtime = ucfirst($showtime);
		
		DB::Query("UPDATE shows SET status='$status', showtime='$showtime' WHERE showid='$showid'");
		return true;
		}
	
	public static function Init(){
		Schedule::$genres = Site::$Genres;
		Schedule::$genreClasses = array();
		foreach(Schedule::$genres as $k => $v) {
			Schedule::$genres[$k] = trim($v);
			Schedule::$genreClasses[trim($v)] = preg_replace("/\\W/","",$v);
			}
		Schedule::$genreClasses[""] = "";
		}
	
	}

Schedule::Init();