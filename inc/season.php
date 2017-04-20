<?php namespace ChapmanRadio;

class Season {

	public static function current() {
		return Site::CurrentSeason();
		}
	
	public static function CurrentStartUnix()
	{
		$season = self::reduce(Site::CurrentSeason());
		preg_match("/^(\\d{4})(\\w{2})\$/", $season, $matches);
		list(,$year,$letter) = $matches;
		
		if($letter == "FA")
		{
			return strtotime("{$year}-08-01");
		}
		else
		{
			return strtotime("{$year}-02-01");
		}
	}
		
	public static function prev($current=""){
		if(!$current) $current = Season::reduce(Season::current());
		preg_match("/^(\\d{4})(\\w{2})\$/", $current, $matches);
		list(,$year,$letter) = $matches;
		if($letter == "FA"){
			$letter = "SP";
			}
		else {
			$letter = "FA";
			$year = ($year*1)-1;
			}
		return $year.$letter;
		}
	
	public static function next() {
		$current = Season::reduce(Season::current());
		preg_match("/^(\\d{4})(\\w{2})\$/", $current, $matches);
		list(,$year,$letter) = $matches;
		if($letter == "FA"){
			$letter = "SP";
			$year = ($year*1)+1;
			}
		else {
			$letter = "FA";
			}
		return $year.$letter;
	}
	
	public static function previous($current=""){
		return Season::prev($current);
		}
	
	public static function last($current) {
		return Season::prev($current);
		}
	
	public static function schedule() {
		return Site::ScheduleSeason();
		}
	
	public static function awards(){
		if(isset($_REQUEST['season']) && Season::valid($_REQUEST['season'])) return $_REQUEST['season'];
		return Season::Previous();
		
		if(Site::$Broadcasting) return Season::Previous();
		return Season::Current();
		
		/*
			$seasonPicker = "";
			$thisYear = date('Y'); $thisMonth = date('m');
			for($year = 2011; $year <= $thisYear; $year++) {
				foreach(array("SP" => "Spring", "FA" => "Fall") as $key => $label) {
					if($year == $thisYear && $key == "SP" && $thisMonth < 5) break;
					if($year == $thisYear && $key == "FA" && $thisMonth < 12) break;
					$seasonPicker .= "<option value='$year$key' ".("$year$key" == $season ? "selected='selected'":"").">$label $year</option>";
				}
			}
		*/
		}
	
	public static function applications() { return Site::ApplicationSeason(); }
	
	public static function reduce($input) {
		preg_match("/^(\\d{4})([A-Za-z]{2})\$/", $input, $matches);
		list(,$year,$letter) = $matches;            
		switch($letter) {
			case 'IN':
				return ($year-1)."FA";
			case 'SP':
				return $year."SP";
			case 'SB':
				return $year."SP";
			case 'SM':
				return $year."SP";
			case 'SU':
				return $year."SP";
			case 'FA':
				return $year."FA";
			case 'FB':
				return $year."FA";
			case 'FM':
				return $year."FA";
			case 'WI':
				return $year."FA";
			default:
			return $input;
		}
	}
	public static function valid($input) {
		return preg_match("/^(\\d{4})([A-Za-z]{2})\$/", $input, $matches);
	}
	public static function fromTimestamp($timestamp) {
		return date('Y', $timestamp) . (date('n', $timestamp) < 7 ? "SP" : "FA");
	}
	public static function name($input="") {
		if(!$input) $input = Season::Current();
		// allow multiple seasons, like '2010FA,2011SP'
		else if(strpos($input, ",") !== false) {
			$pieces = explode(",",$input);
			$names = array();
			foreach($pieces as $piece) {
				if($piece) $names[] = Season::name($piece);
			}
			return implode("; ", $names);
		}
		else if(!Season::valid($input)) return $input;
		preg_match("/^(\\d{4})([A-Za-z]{2})\$/", $input, $matches);
		list(,$year,$letter) = $matches;
		switch($letter) {
			case 'IN':
				return "Interterm, $year";
			case 'SP':
				return "Spring, $year";
			case 'SB':
				return "Spring Break, $year";
			case 'SM':
				return "Spring Marathon Programming, $year";
			case 'SU':
				return "Summer Break, $year";
			case 'FA':
				return "Fall, $year";
			case 'FB':
				return "Thanksgiving Break, $year";
			case 'FM':
				return "Fall Marathon Programming, $year";
			case 'WI':
				return "Winter Break, $year";
			default:
				return $input;
		}
	}
	
	public static function picker($startyear=2011, $more=false, $default="", $omitselect=false) {
		if(!$default) $default = Season::current();
		$html = "";
		if(!$omitselect) $html = "<select name='season'>";
		if($more) {
			$seasons = array(
							 "Interterm" => array("IN"=>"Interterm"),
							 "Spring" => array("SP"=>"Spring Semester","SB"=>"Spring Break","SM"=>"Spring Marathon Programming"),
							 "Summer" => array("SU"=>"Summer Break"),
							 "Fall" => array("FA"=>"Fall Semester","FB"=>"Thanksgiving Break","FM"=>"Fall Marathon Programming"),
							 "Winter" => array("WI"=>"Winter Break"),							 
			);
			for($y = $startyear;$y <= date('Y')+1;$y++) {
				foreach($seasons as $optgroup => $codes) {
					$html .= "<optgroup label='$optgroup, $y'>";
					foreach($codes as $code => $label) {
						$selected = "$y$code" == $default ? "selected='selected'" : "";
						$html .= "<option value='$y$code' $selected>$y$code - $label</option>";
					}
					$html .= "</optgroup>";
				}
				$html .= "<option value=''></option>";
			}
		}
		else {
			$seasons = array("SP"=>"Spring", "FA"=>"Fall");
			for($y = $startyear;$y <= date('Y')+1;$y++) {
				$html .= "<optgroup label='$y'>";
				foreach($seasons as $code => $label) {
					$selected = "$y$code" == $default ? "selected='selected'" : "";
					$html .= "<option value='$y$code' $selected>$y$code - $label</option>";
				}
				$html .= "</optgroup>";
			}				
		}
		if(!$omitselect) $html .= "</select>";
		return $html;
	}
	
	public function sort($seasons) {
		$years = array();
		foreach($seasons as $season) {
			if(!preg_match("/(\d{4})(\w{2})/",$season,$matches)) continue;
			list(,$year,$letters) = $matches;
			if(!@$years[$year]) $years[$year] = array();
			$years[$year][$letters] = $season;
		}
		ksort($years);
		$ret = array();
		$letterses = str_split("INSPSBSMSUFAFMWI",2);
		foreach($years as $year => $seasons) {
			foreach($letterses as $letters) {
				if(@$seasons[$letters]) $ret[] = $seasons[$letters];
			}
		}
		return $ret;
	}
	
	public static function isThanksgiving($season){
		if(strpos($season, "FB") !== FALSE) return true;
		return false;
	}
	
	public static function isSpringBreak($season){
		if(strpos($season, "SB") !== FALSE) return true;
		return false;
	}

	public static function isBreak($season){
		if(strpos($season, "SB") !== FALSE) return true;
		if(strpos($season, "FB") !== FALSE) return true;
		return false;
	}
	
	public static function isMarathon($season){
		if(strpos($season, "SM") !== FALSE) return true;
		if(strpos($season, "FM") !== FALSE) return true;
		return false;
	}
	
}