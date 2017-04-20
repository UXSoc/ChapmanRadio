<?php namespace ChapmanRadio;class RecordingModel {		public $id;	
	public $url;	public $path;	
	public $showid;		public $shortname;	public $recordedon;
	public $timestamp;	
	public $downloads;	public $streams;	public $podcasts;	
	public $label;	public $moreinfo;	public $description;		public $season;	
	public $active;	public $clean;		public static $DataMap = array(		"id" => "mp3id",		"url" => "url",		"showid" => "showid",		"shortname" => "shortname",		"recordedon" => "recordedon",		"downloads" => "downloads",		"streams" => "streams",		"podcasts" => "podcasts",		"label" => "label",		"moreinfo" => "moreinfo",		"description" => "description",		"season" => "season",		"active" => "active",		"clean" => "clean"		);			public static function FromId($id, $usecache = false){		if($id == 0) return NULL;		return Cache::Handle("recording$id", function() use ($id){			return RecordingModel::FromResult(DB::GetFirst("SELECT * FROM mp3s WHERE mp3id = :id", array(":id" => $id)));			}, $usecache);		}		public static function FromTimestamp($stamp){		return RecordingModel::FromResult(DB::GetFirst("SELECT * FROM mp3s WHERE shortname = :stamp", array(":stamp" => $stamp)));		}			public static function FromResult($result){		if($result === NULL || $result === FALSE) return NULL;		return new self($result);		}		public static function FromResults($results){		$ret = array();		foreach($results as $result) $ret[] = self::FromResult($result);		return $ret;		}		public function __construct($db_assoc){		if($db_assoc === NULL || $db_assoc === FALSE) throw new Exception("Cannot create a RecordingModel from empty data");		$this->objtype = "RecordingModel";		$this->rawdata = $db_assoc;				foreach(self::$DataMap as $prop => $db) if(isset($db_assoc[$db])) $this->$prop = $db_assoc[$db];		
		$this->timestamp = strtotime($this->recordedon);
				$this->path = PATH.substr($this->url, 1);
		
		// If this path doesn't exist try recreating this url from the timestamp
		if($this->url == '' || !$this->Exists()){
			$file = "recordings/".date('Y/m/d/Y-m-d_Hi-T.\mp3', $this->timestamp);
			$this->path = "/".$file;
			$this->url = "/".$file;
			}				Cache::Set("recording{$this->id}", $this);		}		public function PubUrl($source)	{		return "http://recordings.chapmanradio.com/{$source}/".date('Y-m-d_Hi-T', $this->timestamp).".mp3";	}		public function Exists()	{		return file_exists($this->path);	}		public function Filesize(){		return file_exists($this->path) ? filesize($this->path) : 0;		}			public function Handle($source = 'download'){		try{						if(!file_exists($this->path)) return;			
			$handle = fopen($this->path, "r");			if (!$handle) return;						stream_set_blocking ($handle, 0);			session_write_close();						// record this listen			switch($source){								case 'stream':					DB::Query("UPDATE mp3s SET streams=streams+1 WHERE mp3id=:id", array(":id" => $this->id));					break;				case 'podcast':					DB::Query("UPDATE mp3s SET podcasts=podcasts+1 WHERE mp3id=:id", array(":id" => $this->id));					break;				case 'download':					DB::Query("UPDATE mp3s SET downloads=downloads+1 WHERE mp3id=:id", array(":id" => $this->id));					break;				}						Tracker::TrackRecording($this->id, $source);						// headers			switch($source){								case 'stream':				case 'podcast':					header("Content-type: audio/mpeg");					header("Content-length: " . filesize($this->path));					header('Cache-Control: no-cache');					break;								case 'download':					$filename = $this->GetDownloadFileName();					header("Content-type: audio/mpeg");					header("Content-length: " . filesize($this->path));					header('Cache-Control: no-cache');					header("Content-disposition: attachment; filename=\"$filename\"");					break;								default:					return False;				}						while (ob_get_level()) ob_end_clean();			while (!feof($handle)) {				$buffer = fgets($handle, 4096);				print $buffer;				flush();				}						fclose($handle);						return True;						}
		catch(Exception $e){ Template::AddInlineError($e->GetMessage()); }
		return False;
		}	
	public function GetDownloadFileName(){
		$show = ShowModel::FromId($this->showid);
		if(!$show){
			$filename = ucwords($this->shortname).date(' - F jS Y',$this->timestamp);
			}
		else {
			$filename = $show->name . " - " . (($this->label) ? $this->label : date('F jS Y', $this->timestamp));
			}
		preg_replace("/[^a-zA-Z0-9 -]/", "", $filename);
		$filename .= ".mp3";
		return $filename;
		}		}