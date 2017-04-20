<?php namespace ChapmanRadio;

class Tracker {
	
	public static function TrackRecording($recording, $source){
		DB::Insert("listens", [
			"recording_id" => $recording,
			"timestamp" => new \DateTime(),
			"source" => $source,
			"ipaddr" => inet_pton(Request::ClientAddress())
			]);
		}
		
	public static function TrackListenLive(){
		DB::Insert("listens", [
			"recording_id" => 0,
			"timestamp" => new \DateTime(),
			"source" => 'live',
			"ipaddr" => inet_pton(Request::ClientAddress())
			]);
		}
	}
	