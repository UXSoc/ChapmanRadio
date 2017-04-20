<?php namespace Sinopia;

class Util {
	
	public static function CatchErrors($try, $catch = null){
		try{
			set_error_handler(function($errno, $errstr) { throw new \Exception($errstr); });
			$try();
			restore_error_handler();
			}
		catch (\Exception $e){
			restore_error_handler();
			if($catch !== NULL) $catch($e);
			}
		}
	
	}