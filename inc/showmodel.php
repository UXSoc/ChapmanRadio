<?php namespace ChapmanRadio;

class ShowModel extends ImageModel {
	
	public $id;
	public $rawdata;
	
	public $name;
	public $time;
	public $genre;
	public $description;
	public $link;
	public $musictalk;
	
	public $explicit;
	public $turntables;
	
	public $podcastcategory;
	public $podcastenabled;
	
	public $ranking;
	public $status;
	public $attendanceoptional;
	
	public $userids;
	public $seasons;
	public $seasoncount;
	public $seasons_csv;
	
	public $availability;
	public $availability_notes;
	
	public $app_differentiate;
	public $app_promote;
	public $app_timeline;
	public $app_giveaway;
	public $app_speaking;
	public $app_equipment;
	public $app_prepare;
	public $app_examples;
	
	public $permalink;
	public $permaurl;
	public $podcasturl;
	public $podcastlink;
	
	public static $default_img50 = "/legacy/img/defaults/50.png";
	
	public static $DataMap = array(
		"id" => "showid",
		"name" => "showname",
		"time" => "showtime",
		"genre" => "genre",
		"description" => "description",
		"link" => "link",
		"seasons_csv" => "seasons",
		"musictalk" => "musictalk",
		"explicit" => "explicit",
		"turntables" => "turntables",
		"podcastcategory" => "podcastcategory",
		"podcastenabled" => "podcastenabled",
		"status" => "status",
		"ranking" => "ranking",
		"attendanceoptional" => "attendanceoptional",
		"availability" => "availability",
		"availabilitynotes" => "availabilitynotes",
		"app_differentiate" => "app_differentiate",
		"app_promote" => "app_promote",
		"app_timeline" => "app_timeline",
		"app_giveaway" => "app_giveaway",
		"app_speaking" => "app_speaking",
		"app_equipment" => "app_equipment",
		"app_prepare" => "app_prepare",
		"app_examples" => "app_examples",
		"revisionkey" => "revisionkey"
		);
		
	public static function FromId($id, $usecache = false){
		if($id == 0) return NULL;
		if(!$usecache) return ShowModel::FromResult(DB::GetFirst("SELECT * FROM shows WHERE showid = :id", array(":id" => $id)));
		return Cache::Handle("show$id", function() use ($id){
			return ShowModel::FromResult(DB::GetFirst("SELECT * FROM shows WHERE showid = :id", array(":id" => $id)));
			});
		}
		
	public static function FromIds($ids, $usecache = false){
		if($usecache) self::PreFetch($ids);
		$ret = array();
		foreach($ids as $id) $ret[] = ShowModel::FromId($id, $usecache);
		return $ret;
		}
		
	public static function PreFetch($shows){
		if(empty($shows)) return;
		ShowModel::FromResults(DB::GetAll("SELECT * FROM shows WHERE showid = ".implode(' OR showid = ', $shows)));
		}
	
	public static function FromResult($result){
		if($result === NULL || $result === FALSE) return NULL;
		return new ShowModel($result);
		}
	
	public static function FromResults($results){
		$ret = array();
		foreach($results as $result) $ret[] = ShowModel::FromResult($result);
		return $ret;
		}
		
	public static function FromDj($userid){
		$results = array();
		$data = DB::GetAll("SELECT * FROM shows WHERE userid1=:id OR userid2=:id OR userid3 = :id OR userid4=:id OR userid5=:id ORDER BY createdon ASC", array(":id" => $userid));
		foreach($data as $show) $results[] = new ShowModel($show);
		return $results;
		}
		
	public static function Search($input, $season = NULL, $additional = ""){
		$results = array();
		$ids = (is_numeric($input)) ? "OR showid=:input OR userid1=:input OR userid2=:input OR userid3=:input OR userid4=:input OR userid5=:input" : "";
		$seasonquery = ($season) ? " AND seasons LIKE '%$season%'" : "";
		$data = DB::GetAll("SELECT * FROM shows WHERE (showname LIKE :search OR genre LIKE :search OR description LIKE :search $ids) $seasonquery $additional", array(
			":search" => "%".$input."%",
			":input" => $input
			));
		foreach($data as $show) $results[] = new ShowModel($show);
		return $results;
		}
		
	public static function ShowOfTheWeek(){
		return ShowModel::FromResult(DB::GetFirst("SELECT * FROM awards, shows WHERE awards.type = 'showoftheweek' AND shows.showid = awards.showid ORDER BY awards.awardedon DESC"));
		}
	
	public function __construct($db_assoc){
		if($db_assoc === NULL || $db_assoc === FALSE) throw new Exception("Cannot create a ShowModel from empty data");
		$this->rawdata = $db_assoc;
		$this->objtype = "ShowModel";
		
		foreach(ShowModel::$DataMap as $prop => $db) if(isset($db_assoc[$db])) $this->$prop = $db_assoc[$db];
		
		$this->userids = array();
		for($i=1; $i<=5; $i++) $this->userids[] = $db_assoc['userid'.$i];
		
		$this->seasons = array_filter(explode(',', $db_assoc['seasons']));
		$this->seasoncount = count($this->seasons);
		
		$this->permalink = "/show/{$this->id}/" . Util::slugify($this->name);
		$this->permaurl = "https://chapmanradio.com" . $this->permalink;
		$this->podcastlink = "/podcast/{$this->id}/" . Util::slugify($this->name);
		$this->podcasturl = "itpc://chapmanradio.com/podcast/".$this->id."/".Util::slugify($this->name);
		//	$this->podcasturl = "itpc://chapmanradio.com/podcast/".$this->id.".rss";
		
		$this->imgpath = "shows/".sha1("cr_show_".$this->id)."/";
		$this->FlushImgRefs();
		
		Cache::Set("show{$this->id}", $this);
		}
	
	public function MarkRevised(){
		$this->revsionkey = Util::RandomKey(10);
		$this->FlushImgRefs();
		DB::Query("UPDATE shows SET revisionkey = :key WHERE showid = :id", array(
			":key" => $this->revsionkey,
			":id" => $this->id));
		}
	
	public function GetDjs(){
		
		// MOD DDTIHSWACF
		if($this->id == 754) $this->userids = [ 861, 0, 0, 0, 0 ];
		
		return DB::GetAll("SELECT * FROM users WHERE userid=:u1 OR userid=:u2 OR userid=:u3 OR userid=:u4 OR userid=:u5", array(
			":u1" => $this->userids[0],
			":u2" => $this->userids[1],
			":u3" => $this->userids[2],
			":u4" => $this->userids[3],
			":u5" => $this->userids[4]));
		}
		
	public function GetDjModels(){
		return UserModel::FromResults($this->GetDjs());
		}
		
	public function GetDjNames(){
		$data = $this->GetDjModels();
		$ret = array();
		foreach($data as $dj) $ret[$dj->id] = Util::Format($dj->DjNameOrName());
		return $ret;
		}
		
	public function GetRealNames(){
		$data = $this->GetDjModels();
		$ret = array();
		foreach($data as $dj) $ret[$dj->id] = $dj->name;
		return $ret;
		}
		
	public function GetDjNamesCsv(){
		return implode(', ', $this->GetDjNames());
		}
		
	public function GetRealNamesCsv(){
		return implode(', ', $this->GetRealNames());
		}
		
	public function HasDJ($userid){
		return in_array($userid, $this->userids);
		}
		
	public function AddDJ($userid){
		if($this->HasDJ($userid)){
			throw new Exception("User is already a DJ for this show");
			}
		foreach($this->userids as $index => $uid){
			if($uid == 0){
				$i = $index+1;
				$this->userids[$index] = $userid;
				DB::Query("UPDATE shows SET userid$i = :uid WHERE showid = :id", array(
					":uid" => $userid,
					":id" => $this->id));
				return;
				}
			}
		throw new Exception("Unable to add more than 5 DJs to this show");
		}
	
	public function RemoveDj($userid){
		foreach($this->userids as $index => $uid){
			if($uid == $userid){
				$i = $index+1;
				$this->userids[$index] = 0;
				DB::Query("UPDATE shows SET userid$i = 0 WHERE showid = :id", array(
					":id" => $this->id));
				return;
				}
			}
		throw new Exception("Unable to remove a non existant DJ from a show");
		}
	
	public function HasSeason($season){
		return in_array($season, $this->seasons);
		}
		
	public function SetStatus($status){
		DB::Query("UPDATE shows SET status = :status WHERE showid = :id", array(
			":status" => $status,
			":id" => $this->id));
		}
		
	public function Update($field, $value){
		if(isset(ShowModel::$DataMap[$field])){
			$dbfield = ShowModel::$DataMap[$field];
			DB::Query("UPDATE shows SET $dbfield = :value WHERE showid = :id", array(
				":value" => $value,
				":id" => $this->id));
			$this->$field = $value;
			}
		else {
			throw new Exception("Unable to update unmapped property $field");
			}
		}
		
	public function AddSeason($season){
		if($this->HasSeason($season)) return;
		DB::Query("UPDATE shows SET seasons = CONCAT(seasons, ',', :season) WHERE showid = :id", array(
			":season" => $season,
			":id" => $this->id));
		}
		
	public function GetRecordings(){
		return DB::GetAll("SELECT * FROM mp3s WHERE showid = :id AND active=1 ORDER BY recordedon DESC", array(":id" => $this->id));
		}
	
	public function GetRecordingModels(){
		return RecordingModel::FromResults(DB::GetAll("SELECT * FROM mp3s WHERE showid = :id AND active=1 ORDER BY recordedon DESC", array(":id" => $this->id)));
		}
	
	}