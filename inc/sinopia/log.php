<?php namespace Sinopia;

class Log {
	
	private static $log_dir = NULL;
	
	public static function SetLogDir($dir){
		self::$log_dir = $dir;
		}
	
	public static function ToFile($file, $message){
		if(self::$log_dir != NULL) file_put_contents(self::$log_dir . $file, $message . "\n", FILE_APPEND);
		else file_put_contents($file, $message, FILE_APPEND);
		}
	
	}