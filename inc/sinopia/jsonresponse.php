<?php namespace Sinopia;

/*
 * The JsonResponse class provides static methods for controlling data sent from this API
 */
class JsonResponse {
	
	public static $DEBUG = true;
	public static $QUIET = false;
	
	/**
	* An internal array of results to send to the client
	*/
	protected static $response = array('result' => 0);
	private static $sent = false;
	
	/**
	* Adds the specified value to the response sent to the client
	*
	* @param	key				String - index of the specified value
	* @param	value			OBJECT - object to stringify with json_encode
	* @return					VOID
	*/
	public static function Add($key, $value){
		self::$response[$key] = $value;
		}
	
	
	/**
	* Adds the specified value only if the DEBUG property is TRUE
	*
	* @param	key				String - index of the specified value
	* @param	value			OBJECT - object to stringify with json_encode
	* @return					VOID
	*/
	public static function Debug($key, $value){
		if(!self::$DEBUG) return;
		self::$response["-debug-".$key] = $value;
		}
	
	
	/**
	* Adds the specified value to the warnings list sent to the client
	*
	* @param	value			Integer - integer code to add to the list of warnings sent with the response
	* @return					VOID
	*/
	public static function AddWarning($value){
		if(!isset(self::$response["warnings"])) self::$response["warnings"] = array();
		self::$response["warnings"][] = $value;
		}
		
	/**
	* Sends the specified result code to the client
	*
	* Warning: Will close the current client connection
	*
	* @param	code			Integer
	* @return					VOID
	*/
	public static function Result($code){
		self::$response['result'] = $code;
		self::Send();
		}
	
	/**
	* Internal method for encoding and sending the results to the client
	*
	* Warning: Will close the current client connection
	*
	* @return					VOID
	*/
	protected static function Send(){
		
		if(self::$QUIET) exit;
		
		// Make sure we haven't sent a response yet
		if(self::$sent) return;
		self::$sent = true;
		
		die(json_encode(self::$response));
		}
	
	}