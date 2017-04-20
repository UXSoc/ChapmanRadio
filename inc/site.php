<?php namespace ChapmanRadio; /* chapmanradio.com/inc/site.php (C) David Tyler 2014 */

class Site {

	private static $_vals_;
	private static $_cache_;
	
	public static $Broadcasting;
	
	public static $Applications;
	public static $ApplicationDeadline;
	
	public static $IcecastServer;
	public static $IcecastUsername;
	public static $IcecastPassword;
	
	public static $StationIps;
	public static $StationIpsCsv;
	
	public static $ipinfodb_apikey;
	
	public static $SongKickApiKey;
	public static $SongKickLocation;
	
	public static $Genres;
	public static $GenresCsv;
	
	public static $FacebookAppId;
	public static $FacebookAppSecret;
	
	public static $MetaKeywords;
	public static $MetaDescription;
	
	/* Attendance Constants */
	public static $TardyGrace = 8;
	public static $ShowAbsencesPerStrike = 1;
	public static $WorkshopAbsencesPerStrike = 1;
	public static $TardiesPerStrike = 3;
	
	public static $SmsNumber;
	

	public static function Init(){

		self::$_vals_ = array();

		$pref_data = DB::GetAll("SELECT * FROM prefs");

		foreach($pref_data as $pref){
			
			$val = Request::Get('_pref_'.$pref['key'], $pref['val']);
			
			if(substr($pref['key'], 0, 6) == "cache_") self::$_cache_[substr($pref['key'], 6)] = array($pref['updated'], $val);
			
			switch($pref['key']){
				case 'broadcasting':
					self::$Broadcasting = $val;
					break;
				case 'applications':
					self::$Applications = $val;
					break;
				case 'applicationsdeadline':
					self::$ApplicationDeadline = $val;
					break;
				case 'genres':
					self::$Genres = explode(",", $val);
					self::$GenresCsv = $val;
					break;
				case 'icecastserver':
					self::$IcecastServer = $val;
					break;
				case 'icecastusername':
					self::$IcecastUsername = $val;
					break;
				case 'icecastpassword':
					self::$IcecastPassword = $val;
					break;
				case 'ipinfodb_apikey':
					self::$ipinfodb_apikey = $val;
					break;
				case 'songkickapikey':
					self::$SongKickApiKey = $val;
					break;
				case 'songkicklocation':
					self::$SongKickLocation = $val;
					break;
				case 'facebookappid':
					self::$FacebookAppId = $val;
					break;
				case 'facebookappsecret':
					self::$FacebookAppSecret = $val;
					break;
				case 'metakeywords':
					self::$MetaKeywords = $val;
					break;
				case 'metadescription':
					self::$MetaDescription = $val;
					break;
				case 'smsnumber':
					self::$SmsNumber = $val;
					break;
				case 'stationips':
					self::$StationIps = explode(",", $val);
					self::$StationIpsCsv = $val;
					break;
				}
			
			// temp
			self::$_vals_[$pref['key']] = $val;
			}

		}
	
	public static function MustBeBroadcasting(){
		if(!self::$_vals_['broadcasting']){
			echo "Not broadcasting.";
			exit;
			}
		}
		
	public static function CurrentSeason($editable = false){
		if($editable && isset($_REQUEST['season']) && Season::valid($_REQUEST['season'])) return $_REQUEST['season'];
		return self::$_vals_['currentseason'];
		}
		
	public static function ScheduleSeason($editable = false){
		if($editable && isset($_REQUEST['season']) && Season::valid($_REQUEST['season'])) return $_REQUEST['season'];
		return self::$_vals_['scheduleseason'];
		}
	
	public static function ApplicationSeason($editable = false){
		if($editable && isset($_REQUEST['season']) && Season::valid($_REQUEST['season'])) return $_REQUEST['season'];
		return self::$_vals_['applicationsseason'];
		}
	
	public static function HandleCache($key, $fn, $required_freshness = 3600){
		$c = self::GetCache($key, $required_freshness);
		if($c !== NULL) return $c;
		$n = $fn();
		self::SetCache($key, $n);
		return $n;
		}
	
	public static function GetCache($key, $required_freshness = 3600){
		if(!isset(self::$_cache_[$key])) return NULL;
		if($required_freshness !== NULL && self::$_cache_[$key][0] < (time() - $required_freshness)) return NULL;
		return self::$_cache_[$key][1];
		}
	
	public static function SetCache($key, $val){
		if(!isset(self::$_cache_[$key])) DB::Insert('prefs', array("key" => "cache_".$key, "val" => $val, "updated" => time()));
		else self::Update("cache_".$key, $val);
		}
	
	public static function Update($key, $val){
		DB::Query("UPDATE prefs SET `val` = :val, updated = :updated WHERE `key` = :key", array(":key" => $key, ":val" => $val, ":updated" => time()));
		}
	

	}
	
Site::Init();