<?php namespace Sinopia;
use DateTime;

abstract class Request {
	
	private static $source;
	
	public static function SetSource($source){
		self::$source = $source;
		}
	
	public static function Get($prop, $default = ''){
		return isset(self::$source[$prop]) ? self::$source[$prop] : $default;
		}
	
	public static function IsNull($prop){
		return !isset(self::$source[$prop]);
		}
	
	public static function IsInteger($prop){
		return isset(self::$source[$prop]) && is_numeric(self::$source[$prop]);
		}
		
	public static function Has($prop){
		return isset(self::$source[$prop]);
		}
	
	public static function GetFrom($what, $prop, $default = ''){
		return isset($what[$prop]) ? $what[$prop] : $default;
		}
		
	public static function GetBool($prop){
		return isset(self::$source[$prop]) ? 1 : 0;
		}
	
	public static function GetInteger($prop, $default = 0){
		$value = isset(self::$source[$prop]) ? self::$source[$prop] : $default;
		if(is_numeric($value)) return intval($value);
		return $default;
		}
	
	public static function GetDatetime($prop, $default = 0){
		
		$var = isset(self::$source[$prop]) ? self::$source[$prop] : NULL;
		if($var instanceof DateTime) return $var;
		
		$variable_datetime = date_create($variable);
		if($variable_datetime !== FALSE) return $variable_datetime;
		
		return $default;
		}
	
	public static function GetJSON($name, $default = NULL){
		$var = isset(self::$source[$prop]) ? self::$source[$prop] : NULL;
		$var = json_decode($var);
		if($var !== NULL) return $var;
		return $default;
		}
	
	}