<?php namespace Sinopia;

abstract class BaseEnum {
	
	public static function ToString($val){
		return array_search($val, static::$EnumMap) ?: NULL;
		}
	
	public static function FromString($val){
		return isset(static::$EnumMap[$val]) ? static::$EnumMap[$val] : NULL;
		}
	
	public static function Equal($v1, $v2){
		if(!is_numeric($v1)) $v1 = self::FromString($v1);
		if(!is_numeric($v2)) $v2 = self::FromString($v2);
		return $v1 === $v2;
		}
	
	}