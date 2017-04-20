<?php namespace ChapmanRadio;

require_once BASE."/lib/googlevoice.php";

class LiveChat {
	
	const CommandPrefix = '$dtcmd=';

	public static function recieve($contactid, $message){
	
		if(trim($message) == "") return;
		
		DB::Insert("livechat", array(
			"contactid" => $contactid,
			"direction" => 'in',
			"message" => $message,
			"datetime" => date('Y-m-d H:i:s')));
		}
	
	public static function send($contactid, $message){
		
		$livechatid = DB::Insert("livechat", array(
			"contactid" => $contactid,
			"direction" => 'out',
			"message" => $message,
			"datetime" => date('Y-m-d H:i:s')));
		
		// If a text message, send it
		if(strpos($contactid, 'key') === FALSE) livechat::sendtxt($contactid, $message);
		return $livechatid;
		}
	
	public static function getMostRecent($count, $allow_cmds = false){
		$count = intval($count);
		$rows = DB::getAll("SELECT * FROM v_livechat ORDER BY datetime DESC LIMIT 0,$count");
		return self::formatRows($rows, $allow_cmds);
		}
	
	public static function getContactKey(){
		if(isset($_COOKIE['livechat_contactkey'])){
			$key = $_COOKIE['livechat_contactkey'];
			if(DB::GetFirst("SELECT * FROM livechat_contacts WHERE contactkey = :key", array(":key" => $key))){
				DB::Query("UPDATE livechat_contacts SET contactip = :ip WHERE contactkey = :key", array(":key" => $key, ":ip" => Request::ClientAddress() ));
				return $key;
				}
			}
		return livechat::generateContactKey();
		}
	
	public static function generateContactKey(){
		$uid = uniqid();
		$key = "key$uid";
		setcookie("livechat_contactkey", $key, time()+86400*180);
		DB::Insert("livechat_contacts", array(
			"contactkey" => $key,
			"contactname" => "",
			"contactip" => Request::ClientAddress()
			));
		return $key;
		}
	
	public static function getContactHash($key){
		return sha1("chapmanradio-livechat-key-$key-awesome");
		}
		
	public static function getChatName($key){
		$row = DB::GetFirst("SELECT * FROM livechat_contacts WHERE contactkey = '$key'");
		return $row['contactname'];
		}
	
	public static function setChatName($key, $name){
		DB::Query("UPDATE livechat_contacts SET contactname = :name WHERE contactkey = :key", array(
			":name" => $name,
			":key" => $key
			));
		}
		
	public static function verfifyContactHash($key, $hash){
		return livechat::getContactHash($key) == $hash;
		}
		
	public static function fetchContactMessages($key, $last, $allow_cmds = false){
		$results = array();
		if($last == 'null') $last = 0;
		$since = date('Y-m-d H:00:00');
		
		$rows = DB::GetAll("SELECT * FROM livechat WHERE contactid='$key' AND datetime >= '$since' AND livechatid > '$last' ORDER BY livechatid ASC");
		return self::formatRows($rows, $allow_cmds);
		}
		
	public static function getSentBetween($start, $end, $allow_cmds = false){
		$results = array();
		$startdt = date("Y-m-d H:i:s", $start);
		$enddt = date("Y-m-d H:i:s", $end);
		
		$rows = DB::GetAll("SELECT * FROM v_livechat LEFT JOIN users ON v_livechat.contactid = users.phone WHERE datetime >= '$startdt' AND datetime <= '$enddt' ORDER BY datetime");
		return self::formatRows($rows, $allow_cmds);
		}
		
	public static function getChatsSince($since, $last, $allow_cmds = false){
		$rows = DB::GetAll("SELECT * FROM v_livechat LEFT JOIN users ON v_livechat.contactid = users.phone WHERE datetime >= '$since' AND livechatid > '$last' ORDER BY livechatid");
		return self::formatRows($rows, $allow_cmds);
		}
		
	private static function formatRows($rows, $allow_cmds = false){
		$results = array();
		foreach($rows as $row){
			if(!$allow_cmds && strpos($row['message'], self::CommandPrefix) === 0) continue;
			$results[] = livechat::formatRow($row);
			}
		return $results;
		}
		
	public static function formatRow($row){
		
		$number = util::formatPhoneNumber($row['contactid']);
		$label = $number;
		
		if(isset($row['fname']) && $row['fname'] != ''){
			$label = $number.' - '.$row['fname'].' '.$row['lname'];
			if(isset($row['staffposition']) && $row['staffposition'] != "") $label .= ' ('.$row['staffposition'].')';
			}
		else if(isset($row['contactname']) && $row['contactname'] != ''){
			$label = 'ListenLive - '.$row['contactname'];
			}
		
		return array(
			"livechatid" => $row['livechatid'],
			"contactid" => $row['contactid'],
			"number" => $number,
			"direction" => $row['direction'],
			"message" => $row['message'],
			"datetime" => $row['datetime'],
			"time" => date('g:ia', strtotime($row['datetime'])),
			"label" => $label
			);
		}
	
	public static function sendtxt($number, $text) {
		
		$number = preg_replace("/\\D/","", "$number");
		if(strlen($number) != 10) return false;
		
		$maxlength = 160;
		if(strlen($text) > $maxlength*3) return false;
		$pieces = str_split($text, $maxlength);
		$pieces = array_reverse($pieces);
		$gv = new \GoogleVoice('requestcr','chapmanr4di05'); // NcbMzvhktaHvnXkQJawT8kBJzYLCHzzjbWJcNtLjogvk6RAVYE
		foreach($pieces as $piece) {
			if(!$piece) continue;
			$result = $gv->sms($number, $piece);
			if(!strpos($result, 'Text sent to')) die("\"error: Failed to send SMS!\"");
			}
		return true;
		}
	
	}