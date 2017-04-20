<?php namespace ChapmanRadio;

class Attendance {
	
	public static function set($values) {
		$fields = array("status", "timestamp", "type");
		foreach($fields as $field) if(!isset($values[$field])) return false;
		$showid = Request::GetFrom($values, 'showid', 0);
		$userid = Request::GetFrom($values, 'userid', 0);
		if(!$userid && !$showid) return false;
		if(!isset($values['late'])) $values['late'] = 0;
		if(!isset($values['season'])) $values['season'] = Site::CurrentSeason();
		$query = "SELECT * FROM attendance WHERE type='$values[type]' AND timestamp='$values[timestamp]' ";
		if($showid) $query .= "AND showid='$showid' ";
		if($userid) $query .= "AND userid='$userid' ";
		$att = DB::GetFirst($query);
		if($att) {
			DB::Query("UPDATE attendance SET status='$values[status]' WHERE attendanceid='$att[attendanceid]'");
			$attendanceid = $att['attendanceid'];
			}
		else {
			$attendanceid = DB::Insert("attendance", array(
				"timestamp" => $values['timestamp'],
				"showid" => $showid,
				"userid" => $userid,
				"status" => $values['status'],
				"type" => $values['type'],
				"late" => $values['late'],
				"season" => $values['season']));
			}
		return $attendanceid;
		}

	public static function recordShow($target_timestamp, $showid, $userid, $late = 61){		
		// are they before the target (early) or after (late)
		if($late == 61){
			$now = time();
			if($target_timestamp <= time()) $late = date('i',$now) - date('i', $target_timestamp);
			else $late = - (60 - date('i',$now));
			}
		
		// Is this show already recorded
		if(DB::GetFirst("SELECT attendanceid FROM attendance WHERE `timestamp`='$target_timestamp' AND `showid`='$showid'")) return;
		
		DB::Insert("attendance", array(
			"timestamp" => $target_timestamp,
			"showid" => $showid,
			"userid" => $userid,
			"status" => 'present',
			"type" => 'show',
			"late" => $late,
			"season" => Site::CurrentSeason()));
		
		}

	}