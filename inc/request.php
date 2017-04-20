<?php namespace ChapmanRadio;

class Request {
	
	public static function Get($prop, $default = ''){
		return isset($_REQUEST[$prop]) ? trim($_REQUEST[$prop]) : $default;
		}
		
	public static function IsNull($prop){
		return self::Get($prop) == '';
		}
		
	public static function IsNotNull($prop){
		return self::Get($prop) != '';
		}
	
	public static function ClientAddress(){
		return self::GetFrom($_SERVER, 'HTTP_CF_CONNECTING_IP', self::GetFrom($_SERVER, 'REMOTE_ADDR', NULL));
		}
		
	public static function GetAsPrintable($prop, $default = ''){
		return htmlspecialchars(self::Get($prop, $default), ENT_COMPAT, "UTF-8");
		}
		
	public static function GetFrom($what, $prop, $default = ''){
		return isset($what[$prop]) ? $what[$prop] : $default;
		}
		
	public static function GetBool($prop){
		return isset($_REQUEST[$prop]) ? 1 : 0;
		}
	
	public static function GetUrl($prop){
		$val = self::Get($prop);
		if($val && substr($val,0,4) != "http") $val = "http://$val";
		return $val;
		}
	
	public static function GetInteger($prop, $default = 0){
		$value = self::Get($prop);
		if(is_numeric($value)) return intval($value);
		return $default;
		}
		
	public static function GetIntegerFrom($what, $prop, $default = 0){
		$value = isset($what[$prop]) ? $what[$prop] : $default;
		if(is_numeric($value)) return intval($value);
		return $default;
		}
		
	}