<?php namespace ChapmanRadio;

class GiveawayModel extends ImageModel {
	
	public $id;
	
	public $title;
	public $text;
	public $link;
	public $order;
	public $active;
	public $showscsv;
	public $shows;
	
	private static $DataCache = array();
	public static $DataMap = array(
		"id" => "giveawayid",
		"title" => "title",
		"about" => "about",
		"link" => "link",
		"howtowin" => "howtowin",
		"hometext" => "hometext",
		"expireson" => "expireson",
		"active" => "active",
		"showscsv" => "shows",
		"revisionkey" => "revisionkey"
		);
		
	public static function FromId($id, $usecache = false){
		if($id == 0) return NULL;
		if($usecache && isset(self::$DataCache[$id])) return self::$DataCache[$id];
		return self::$DataCache[$id] = self::FromResult(DB::GetFirst("SELECT * FROM giveaways WHERE giveawayid = :id", array(":id" => $id)));
		}
		
	public static function FromResult($result){
		if($result === NULL || $result === FALSE) return NULL;
		return new GiveawayModel($result);
		}
	
	public static function FromResults($results){
		$ret = array();
		foreach($results as $result) $ret[] = self::FromResult($result);
		return $ret;
		}
	
	public function __construct($db_assoc){
		foreach(self::$DataMap as $prop => $db) if(isset($db_assoc[$db])) $this->$prop = $db_assoc[$db];
		$this->objtype = "GiveawayModel";
		
		if(trim($this->showscsv) == "") $this->shows = array();
		else $this->shows = explode(',', $this->showscsv);
		
		$this->imgpath = "giveaways/".sha1("cr_giveaway_".$this->id)."/";
		$this->FlushImgRefs();
		}
		
	public function GetShows($additional = ""){
		if(empty($this->shows)) return array();
		$showwhr = array();
		foreach($this->shows as $s) $showwhr[] = "showid=$s";
		$showsql = implode(" OR ", $showwhr);
		return DB::GetAll("SELECT * FROM shows WHERE ($showsql) $additional");
		}
		
	public function GetShowModels(){
		return ShowModel::FromResults($this->GetShows());
		}
	
	public function Update($field, $value){
		if(isset(self::$DataMap[$field])){
			$dbfield = self::$DataMap[$field];
			DB::Query("UPDATE giveaways SET $dbfield = :value WHERE showid = :id", array(":value" => $value, ":id" => $this->id));
			$this->$field = $value;
			}
		else {
			throw new Exception("Unable to update unmapped property $field");
			}
		}
	
	public function MarkRevised(){
		$this->revsionkey = Util::RandomKey(10);
		$this->FlushImgRefs();
		DB::Query("UPDATE giveaways SET revisionkey = :key WHERE giveawayid = :id", array(":key" => $this->revsionkey, ":id" => $this->id));
		}
		
	}