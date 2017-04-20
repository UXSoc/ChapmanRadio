<?php namespace ChapmanRadio;

class NowPlaying {
	
	/* Get the current nowplaying data, including track data. returns [] on failure */
	public static function getNowPlaying($showid = 0){
		if($showid==0) $showid = Schedule::HappeningNow();
		$since = time()-60*20; // max 20 min old
		$nowplaying = DB::GetFirst("SELECT * FROM nowplaying LEFT JOIN tracks ON trackid=track_id WHERE showid='$showid' AND timestamp > $since ORDER BY timestamp DESC LIMIT 0,1");
		if(!$nowplaying) return array();
		return NowPlaying::formatEntry($nowplaying);
		}
		
	public static function getShowPlaylist($showid=0, $nowplayingid=0){
		if($showid==0) $showid = Schedule::HappeningNow();
		if(!is_numeric($nowplayingid)) $nowplayingid = 0;
		$since = time()-60*60*8; // max 8 hours old
		$results = array();
		$result = DB::GetAll("SELECT * FROM nowplaying LEFT JOIN tracks ON trackid=track_id WHERE `showid`='$showid' AND `timestamp`>'$since' AND `nowplayingid`>'$nowplayingid' ORDER BY timestamp DESC");
		foreach($result as $row) $results[] = NowPlaying::formatEntry($row);
		return $results;
		}

	public static function setNowPlaying($showid, $trackid, $text){
		DB::Insert("nowplaying", array("timestamp" => time(), "showid" => $showid, "trackid" => $trackid, "text" => $text));
		}
	
	public static function getPlayingBetween($start, $end){
		$results = array();
		$result = DB::GetAll("SELECT * FROM nowplaying LEFT JOIN tracks ON trackid=track_id WHERE timestamp >= '$start' AND timestamp <='$end' ORDER BY timestamp DESC");
		foreach($result as $row) $results[] = NowPlaying::formatEntry($row);
		return $results;
		}
	
	public static function getLastPlaying($limit=10, $type='both'){
		$limit = (is_numeric($limit)) ? intval($limit) : 10;
		$type = ($type=='music') ? "WHERE trackid<>''" : (($type=='talk') ? "WHERE trackid=''" : "");
		$result = DB::GetAll("SELECT * FROM nowplaying LEFT JOIN tracks ON trackid=track_id $type ORDER BY timestamp DESC LIMIT $limit");
		foreach($result as $row) $results[] = NowPlaying::formatEntry($row);
		return $results;
		}
	
	public static function getTopTracks($timestamp, $limit=10){
		$limit = (is_numeric($limit)) ? intval($limit) : 10;
		return DB::GetAll("SELECT * FROM tracks JOIN (SELECT trackid,timestamp,count(*) as count FROM nowplaying WHERE timestamp>$timestamp AND trackid IS NOT NULL GROUP BY trackid ORDER BY count DESC) as counter ON counter.trackid=track_id LIMIT $limit");
		}
		
	public static function getTopArists($timestamp, $limit=10){
		$limit = (is_numeric($limit)) ? intval($limit) : 10;
		return DB::GetAll("SELECT * FROM tracks JOIN (SELECT trackid,timestamp,count(*) as count FROM nowplaying JOIN tracks ON track_id=trackid WHERE timestamp>$timestamp AND trackid IS NOT NULL GROUP BY artist_id ORDER BY count DESC) as counter ON counter.trackid=track_id LIMIT $limit");
		}
		
	public static function getTrackPlays($track, $limit=100){
		$limit = (is_numeric($limit)) ? intval($limit) : 10;
		return DB::GetAll("SELECT * FROM shows JOIN (SELECT * FROM nowplaying JOIN (SELECT * FROM tracks WHERE track_name=:track LIMIT $limit) as tracks ON tracks.track_id=trackid ORDER BY nowplaying.timestamp DESC) as data ON shows.showid = data.showid", array(":track"=>$track));
		}
		
	public static function getAristPlays($artist, $limit=100){
		$limit = (is_numeric($limit)) ? intval($limit) : 10;
		return DB::GetAll("SELECT * FROM shows JOIN (SELECT * FROM nowplaying JOIN (SELECT * FROM tracks WHERE artist_name=:artist LIMIT $limit) as tracks ON tracks.track_id=trackid ORDER BY nowplaying.timestamp DESC) as data ON shows.showid = data.showid", array(":artist"=>$artist));
		}
	
	private static function formatEntry($nowplaying){
		$track = (isset($nowplaying['track_id'])) ? new TrackModel($nowplaying) : null;
		return array(
			'nowplayingid' => $nowplaying['nowplayingid'],
			'text' => $nowplaying['text'],
			'type' => ($track) ? 'music' : 'talk',
			'track' => ($track) ? $track->name : '',
			'trackid' => $nowplaying['trackid'],
			'artist' => ($track) ? $track->artist : '',
			'img60' => ($track) ? $track->img60 : '/img/tracks/!default/60.png',
			'img100' => ($track) ? $track->img100 : '/img/tracks/!default/100.png',
			'img200' => ($track) ? $track->img200 : '/img/tracks/!default/200.png',
			'timestamp' => $nowplaying['timestamp'],
			'time' => date("g:ia", $nowplaying['timestamp'])
			);
		}
	
	}