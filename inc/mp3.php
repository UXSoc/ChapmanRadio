<?php namespace ChapmanRadio;

class mp3 {
	
	public static $getID3;
	
	public static function Get_ID3(){
		if(isset(mp3::$getID3)) return mp3::$getID3;
		require_once(PATH.'plugins/getid3/getid3/getid3.php');
		mp3::$getID3 = new getID3;
		mp3::$getID3 -> setOption(array('encoding'=>'UTF-8'));
		return mp3::$getID3;
		}
	
	public function fields() {
		return array("title","artist","album","year","genre","comment","attached_picture");
		}
	
	// make sure $file exists, returns a relative path
	public static function file($file) {
		if(!file_exists($file)) {
			$file = PATH.substr($file, 1);
			if(!file_exists($file)) return "";
		}
		return $file;
	}
	
	// accepts either numeric mp3id or filepath as a string
	public static function read($input) {
		#debug
		return array();
		if(is_numeric($input)) {
			$mp3 = DB::GetFirst("SELECT * FROM mp3s WHERE mp3id='$input'");
			if(!$mp3) return array();
			$file = $mp3['url'];
		} else $file = $input;
		// does $file exist?
		$file = mp3::file($file);
		if(!$file) return array();
		// get tags
		global $getID3;
		$dat = $getID3->analyze($file);
		// did it work?
		if(!isset($dat['id3v2'])) return array();
		if(!isset($dat['id3v2']['comments'])) return array();
		$dat = $dat['id3v2']['comments'];
		return $dat;
	}
	
	// get duration (accepts an mp3id or filepath)
	public static function duration($input) {
		#debug
		return 0;
		if(is_numeric($input)) {
			$mp3 = DB::GetFirst("SELECT * FROM mp3s WHERE mp3id='$input'");
			if(!$mp3) return array();
			$file = $mp3['url'];
		} else $file = $input;
		// does $file exist?
		$file = mp3::file($file);
		if(!$file) return 0;
		// get tags
		$dat = mp3::Get_ID3()->analyze($file);
		if(@$dat['playtime_string']) return $dat['playtime_string'];
		else return 0;
		}
	
	// write id3 tags
	public static function write($mp3id) {
		#debug
		return false; 
		$mp3 = DB::GetFirst("SELECT * FROM mp3s WHERE mp3id='$mp3id'");
		if(!$mp3) return false;
		$file = $mp3["url"];
		// does $file exist?
		$file = mp3::file($file);
		if(!$file) return false;
		
		// prepare all of the fields
		$fields = array();
		
		$show = ShowModel::FromId($mp3['showid']);
		
		$time = date("n/j/y", strtotime($mp3["recordedon"]));
		
		$fields["title"] = $time . " Chapman Radio";
		if($show) $fields["title"] = $time . " " . $show->name;
		else if($mp3["label"]) $fields["title"] = $mp3['label'];
		else if($mp3["shortname"]) $fields["title"] = ucwords(str_replace("_"," ",$mp3["shortname"]));
		
		$fields["artist"] = $show ? $show->GetDjNamesCsv() : "Chapman Radio";
		$fields["album"] = $show ? $show->name : "Chapman Radio";
		$fields["year"] = date("Y", strtotime($mp3["recordedon"]));
		$fields["genre"] = $show ? $show->genre : "";
		$fields["comment"] = $mp3["description"];
		
		if($show){
			$size = getimagesize($show->img310);
			$mime = $size["mime"];
			$fields["attached_picture"] = array("data"=>file_get_contents($show->img310),"description"=>"","mime"=>$mime,"picturetypeid"=>"3");
			}
		
		// id3
		getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'write.php', __FILE__, true);
		$tagwriter = new getid3_writetags;
		$tagwriter -> filename = $file;
		$tagwriter -> tagformats = array('id3v1','id3v2.3');
		$tagwriter -> overwrite_tags = true;
		$tagwriter -> remove_other_tags = false;
		$tagwriter -> tag_encoding = 'UTF-8';
		$tagData = array();
		foreach($fields as $key => $val) $tagData[$key] = array($val);
		$tagwriter -> tag_data = $tagData;
		if($tagwriter -> WriteTags()) {
			//echo "WARNINGS:<br />".implode("<br />",$tagwriter -> warnings);
			return true;
			}
		else {
			//echo "ERRORS:<br />".implode("<br />",$tagwriter -> errors);
			return false;
			}
		}
	}