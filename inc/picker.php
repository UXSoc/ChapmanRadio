<?php namespace ChapmanRadio;

class Picker {
	
	public static function Users($season = "", $config = array()) {
		if(!$season) $season = Season::current();
		if(!isset($config['name'])) $config['name'] = "userid";
		if(!isset($config['showuserid'])) $config['showuserid'] = false;
		$seasonName = Season::name($season);
		$ret = "<select name='$config[name]'><option value=''> - User in $seasonName - </option>";
		$result = DB::GetAll("SELECT userid, name FROM users WHERE seasons LIKE :season ORDER BY name", array(":season" => "%$season%"));
		
		$count = 0;
		foreach($result as $row){
			$count++;
			$ret .= "<option value='$row[userid]'>$row[name] ".($config['showuserid']?"(#$row[userid])":"")."</option>";
			}
		if(!$count) $ret .= "<option value=''> - There are no users with Activated Accounts in $seasonName</option>";
		$ret .= "</select>";
		return $ret;
		}
	
	}