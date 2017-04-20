<?php namespace ChapmanRadio;

class Happenings {
	
	private static $feedurl = "http://25livepub.collegenet.com/calendars/maincalendar.rss?mixin=8061%2c8406";
	
	public static function Sync(){
	
		$x = simplexml_load_string(file_get_contents(Happenings::$feedurl));

        if(count($x) == 0) return;
        $inserts = array();
		foreach($x->channel->item as $item){
			$guid = basename($item->guid);
			$title = $item->title;
			$desc_raw = $item->description;
			
			$link = $item->link;
			
			// Parse out description, event date and location from desc_raw
			$desc_raw = str_replace("&nbsp;", " ", $desc_raw);
			$lines = split("<br/>", $desc_raw);
			
			preg_match("@^(.*), ([\d|:]+)(.*)(\s)(.*)(\s)([\d|:]+)(.*)$@i", $lines[1], $matches);
			$timestring = $matches[1].", ".$matches[2].(($matches[3]=="")?$matches[8]:$matches[3]);
			$timestamp = strtotime($timestring);
			if($timestamp==0) continue;
			
			$location =  $lines[0];
			$desc =  $lines[3];
			
			$data[] = array(
				":guid" => $guid,
				":title" => $title,
				":desc" => $desc,
				":location" => $location,
				":link" => $link,
				":timestamp" => $timestamp
				);
			
			echo "Guid: $guid<br />Title: $title<br />Desc: $desc<br />Location: $location<br />Link: $link<br />Timestamp: $timestamp<br />Timebase: ".$lines[1]."<br />Timeparse: ".$timestring."<br /><br />";
			}
		
		DB::QueryMany("INSERT IGNORE INTO feed (guid, title, description, location, link, `timestamp`) VALUES(:guid, :title, :desc, :location, :link, :timestamp)", $data);
		}
		
	public function GetUpcoming($count){}
	
	}