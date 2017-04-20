<?php namespace ChapmanRadio;

class TrackModel {
	
	public $id;
	public $objtype;
	public $rawdata;
	
	public $name;
	public $artist;
	public $artist_id;
	public $img_base;
	
	public $img60;
	public $img100;
	public $img200;
	public $img300;
	
	private static $DataCache = array();
	
	protected static $DataMap = array(
		"id" => "track_id",
		"name" => "track_name",
		"artist" => "artist_name",
		"artist_id" => "artist_id",
		"img_base" => "img_base",
		);
		
	protected static $ImgMap = array(
		'30' => '30x30-50',
		'60' => '60x60-50',
		'100' => '100x100-75',
		'200' => '200x200-75',
		'400' => '400x400-75'
		);
	
	public static function FromId($id, $usecache = false){
		if($id == 0) return NULL;
		if($usecache && isset(self::$DataCache[$id])) return self::$DataCache[$id];
		return self::$DataCache[$id] = self::FromResult(DB::GetFirst("SELECT * FROM tracks WHERE track_id = :id", array(":id" => $id)));
		}
		
	public static function FromResult($result){
		if($result === NULL || $result === FALSE) return NULL;
		return new TrackModel($result);
		}
	
	public static function FromResults($results){
		$ret = array();
		foreach($results as $result) $ret[] = self::FromResult($result);
		return $ret;
		}
		
	public static function Sync($term){
		$sterm = urlencode($term);
		$snoopy = new Snoopy();
		$snoopy->host = "apple.com";
		$snoopy->referer = "http://apple.com";
		$snoopy->fetch("http://itunes.apple.com/search?kind=song&limit=10&term=$sterm");
		//$snoopy->fetch("http://itunes.apple.com/search?media=music&limit=10&term=$sterm");
		
		if($snoopy->results){
			$results = json_decode($snoopy -> results);
			if($results) foreach($results->results as $result){
				if(!isset($result->kind) || $result->kind != "song") continue;
				
				if(DB::GetFirst("SELECT * FROM tracks WHERE track_name = :trackname AND artist_name = :artistname AND track_id <> :trackid", array(
					":trackid" => $result->trackId,
					":trackname" => $result->trackName,
					":artistname" => $result->artistName
					)) != NULL) continue;
				
				DB::Query("INSERT INTO tracks VALUES (:trackid, :artistid, :trackname, :artistname, :imgbase) ON DUPLICATE KEY UPDATE img_base = VALUES(img_base)", array(
					":trackid" => $result->trackId,
					":artistid" => $result->artistId,
					":trackname" => $result->trackName,
					":artistname" => $result->artistName,
					":imgbase" => str_replace ('100x100-75', '~~~~~~', $result->artworkUrl100)
					));
				}
			}
		}
		
	public static function Search($term, $sync = false){
		
		$limit = Request::GetInteger('limit', 7);
		
		if($sync) self::Sync($term);
		
		$term = trim(urldecode($term));
		$words = explode(" ", $term);
		
		if(count($words) == 0) return array();
		
		$terms = array(':search' => $term);
		
		$statements = array();
		$ifstatements = array();
		$counter = 0;
		foreach($words as $word){
			if($word == "" || $word == " ") continue;
			$statements[] = "(track_name LIKE :likeword$counter OR artist_name LIKE :likeword$counter)";
			$ifstatements[] = "if(track_name like :likeword$counter, 1, 0)";
			$ifstatements[] = "if(track_name = :word$counter, 3, 0)";
			$ifstatements[] = "if(artist_name like :likeword$counter, 1, 0)";
			$ifstatements[] = "if(artist_name = :word$counter, 3, 0)";
			$ifstatements[] = "if(track_name like '%karaoke%', -1, 0)";
			$ifstatements[] = "if(artist_name like '%karaoke%', -1, 0)";
			$terms[":word$counter"] = $word;
			$terms[":likeword$counter"] = '%'.$word.'%';
			$counter++;
			}
		$ifstatements[] = "if(track_name = :search, 3, 0)";
		$ifstatements[] = "if(artist_name = :search, 3, 0)";
		
		return self::FromResults(DB::GetAll("
			SELECT *, (".implode($ifstatements, ' + ').") as num
			FROM tracks WHERE (".implode($statements, ' AND ').") OR track_id = :search
			ORDER BY num DESC LIMIT $limit", $terms));
		}
		
	public function __construct($db_assoc){
		if($db_assoc === NULL || $db_assoc === FALSE) throw new Exception("Cannot create a TrackModel from empty data");
		$this->objtype = "TrackModel";
		$this->rawdata = $db_assoc;
		
		foreach(self::$DataMap as $prop => $db) if(isset($db_assoc[$db])) $this->$prop = $db_assoc[$db];
		
		$this->img60 = $this->GetImg(60);
		$this->img100 = $this->GetImg(100);
		$this->img200 = $this->GetImg(200);
		$this->img300 = $this->GetImg(300);
		}
	
	public function GetImg($size){
		if($this->img_base == "" || !isset(self::$ImgMap[$size])) return "http://chapmanradio.com/img/tracks/!default/$size.png";
		return str_replace ('~~~~~~', self::$ImgMap[$size], $this->img_base);
		}
	
	}
	