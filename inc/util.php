<?php namespace ChapmanRadio;

define('ENCRYPTION_SALT', 'oa9wy73anr7clsigduflabxl'); 

class Util {
	public static function encrypt($text) { 
		return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, ENCRYPTION_SALT, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))); 
	} 
	
	public static function decrypt($text) { 
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, ENCRYPTION_SALT, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))); 
	}
	
	public static function UniqueFileName(){
		return stripslashes(strtoupper(uniqid('', true)));
		}
	
	public static function CatchErrors($try, $catch = null){
		try{
			set_error_handler(function($errno, $errstr) { throw new \Exception($errstr); });
			$try();
			}
		catch (\Exception $e){
			restore_error_handler();
			if($catch != NULL) $catch($e);
			}
		}
	
	public static function RandomKey($len){
		$charid = strtoupper(md5(uniqid(rand(), true)));
		return substr($charid, 0, $len);
		}
	
	//truncates a string to a certain char length, stopping on a word if not specified otherwise.
	public static function Truncate($string, $length, $stopanywhere = false) {
		if (strlen($string) > $length) {
			$string = substr($string,0,($length -3));
			if ($stopanywhere) $string .= '...'; //stop anywhere
			else $string = substr($string, 0, strrpos($string, ' ')).'...'; //stop on a word.
			}
		return $string;
		}
	
	public static function formatPhoneNumber($number){
		if(strlen($number) == 10) return "(".substr($number,0,3).") ".substr($number,3,3)."-".substr($number,6);
		return $number;
		}
	
	public static function timeDifference($a, $b = -1, $factor = 1) {
		if($b == -1) $b = time();
		$diff = $a - $b;
		if($diff < 0) $diff *= -1;
		if($factor != 1) $diff *= $factor;
		// special thanks to http://snippets.dzone.com/posts/show/3044
		$chunks = array(
						array(60 * 60 * 24 * 365 , 'year'),
						array(60 * 60 * 24 * 30 , 'month'),
						array(60 * 60 * 24 * 7, 'week'),
						array(60 * 60 * 24 , 'day'),
						array(60 * 60 , 'hour'),
						array(60 , 'minute'),
						array(1 , 'second'),
						);		
		// $j saves performing the count function each time around the loop
		for ($i = 0, $j = count($chunks); $i < $j; $i++) {
			$seconds = $chunks[$i][0];
			$name = $chunks[$i][1];
			// finding the biggest chunk (if the chunk fits, break)
			if (($count = floor($diff / $seconds)) != 0) break;
		}
		$print = ($count == 1) ? '1 '.$name : "$count {$name}s";
		return $print;
	}
	
	public static function slugify($string) {
		//Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
		$string = strtolower($string);
		//Strip any unwanted characters
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//Clean multiple dashes or whitespaces
		$string = preg_replace("/[\s-]+/", " ", $string);
		//Convert whitespaces and underscore to dash
		$string = preg_replace("/[\s_]/", "-", $string);
		return $string;
	}
	
	public static function Format($html){
		return htmlspecialchars($html,ENT_COMPAT | ENT_QUOTES ,"UTF-8");
		}
	
	public static function Picker($name, $values, $formatter){
		$ret = array();
		$ret[] = "<select name='$name'>";
		foreach($values as $value) $ret[] = $formatter($value);
		$ret[] = "</select>";
		return implode('', $ret);
		}
	
	public static function hourName($num) {
		if($num < 12) return $num."am";
		else if($num == 12) return "12pm";
		else if($num < 24) return ($num%12)."pm";
		else if($num == 24) return "12am";
		else return ($num%24)."am";
		}
	
	public static function engList($array) {
		$size = count($array);
		if($size < 1) return "";
		else if($size == 1) return $array[0];
		else if($size == 2) return $array[0] . " and ".$array[1];
		else {
			$ret = "";
			for($i = 0; $i <= $size-2; $i++) $ret .= $array[$i].", ";
			$ret .= " and ".$array[$size-1];
			return $ret;
			}
		}
	
	}