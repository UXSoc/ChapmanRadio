<?php namespace ChapmanRadio;

class Icecast {
	
	public static function server() { return Site::IcecastServer(); }
	
	public static function streams($flush = false) {
		
		// local cache within this page request
		if(Cache::Has('icecastdata') && !$flush) return Cache::Get('icecastdata');
		
		// Use any icecast data updated in the last 30 seconds
		$data = Site::GetCache('icecast', 30);
		if($data && !$flush) return json_decode($data, true);
		
		// wrap the external call to catch errors
		Util::CatchErrors(function() use(&$data){
			$data = file_get_contents(Site::$IcecastServer."admin/stats.xml", false, Icecast::Context());
			});
			
		if(!$data) return self::handleNoStreams();
		
		require_once BASE."/xml2array.php";
		$arr = xml2array($data);
		
		if(!$arr || !isset($arr["source"])) return self::handleNoStreams(); 
		$steams = array();
		foreach($arr["source"] as $source) {
			if($source["server_type"] != "audio/mpeg") continue;
			$mount = preg_replace("/^\\//","",@$source["@attributes"]["mount"]);
			if(!$mount) continue;
			$streams[$mount] = array(
				 "name"=>@$source["server_name"],
				 "description" => Request::GetFrom($source, "server_description"),
				 "listeners" => Request::GetFrom($source, "listeners", 0),
				 "bitrate" => Request::GetFrom($source, "bitrate", 0)
				 );
			}
		
		// Save data in DB
		Site::SetCache('icecast', json_encode($streams));
		
		// Save data for this request
		Cache::Set('icecastdata', $streams);
		
		return $streams;
		}
	
	private static function handleNoStreams (){
		// have we notified the mailing list in the last 10 minutes
		Site::SetCache('icecast', json_encode([]));
		if(!Site::GetCache('icecastdownnotify', 600)){
			Site::SetCache('icecastdownnotify', "1");
			Notify::emaillist('icecastdown', "Icecast is Down!", "<div style='background: red; color: white; padding: 10px; margin: 10px; display: block;'>The Chapman Radio website is reporting the IceCast server is unreachable or is not broadcasting for some reason. This email will be sent every 10 minutes while the server is unreachable.</div>");
			}
		return array();
		}
		
	public static function Context(){
		return stream_context_create([
			'http' => [
				'header' => "Authorization: Basic " . base64_encode(Site::$IcecastUsername.":".Site::$IcecastPassword),
				'timeout' => 3
				]
			]);
		}
		
	public static function metadata($song) {
		$streams = array("listen", "listen_high", "listen_low");
		foreach($streams as $stream) Util::CatchErrors(function(){
			file_get_contents(Site::$IcecastServer."admin/metadata?mount=/$stream&mode=updinfo&song=".urlencode($song), false, Icecast::Context());
			});
		return true;
		}
	
	}