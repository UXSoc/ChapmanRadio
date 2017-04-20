<?php namespace Sinopia;

class Cache {
	
	private static $data;
	
	public static function Has($id){
		return isset(self::$data[$id]);
		}
	
	public static function Get($id){
		return isset(self::$data[$id]) ? self::$data[$id] : NULL;
		}
	
	public static function Set($id, $val){
		self::$data[$id] = $val;
		return $val;
		}
	
	public static function Handle($id, $fn){
		return self::Has($id) ? self::Get($id) : self::Set($id, $fn());
		}
	
	}