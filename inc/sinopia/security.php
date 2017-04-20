<?php namespace Sinopia;

/*
 * The Security class provides static methods for creating and verifying secure data (eg. passwords)
 */
class Security {
	
	public static $Salt;
	
	/**
	* Returns a unique random string for use as a password salt
	*
	* @return					String - A unique random 64 character salt string
	*/
	public static function GenerateSalt(){
		return Security::GenerateRandomCryptoString(64);
		}
	
	
	/**
	* Returns a unique session ID string
	*
	* @return					String - A unique 128 character string
	*/
	public static function GenerateSessionID(){
		return Security::GenerateRandomCryptoString(128);
		}
	
	
	/**
	* Generates a 512 bit hash for the provided password and salt
	*
	* As a hash function, the result is guaranteed to be the same
	* each time the same values of password and salt are provided
	*
	* @param	password		String - The user-provided password
	* @param 	salt			String - The system-maintained salt
	* @return					String - A 512 bit hash string
	*/
	public static function Hash($password, $salt){
		if(self::$Salt == NULL) throw new Exception("Must define Security::Salt");
		return hash('sha512', self::$Salt.$password.$salt);
		}
	
	
	/**
	* Returns TRUE if the password generates the correct hash (correct password), FALSE otherwise
	*
	* @param	password		String - The user-provided password 
	* @param 	salt			String - The system-maintained salt
	* @param 	hash			String - The system-maintained hash
	* @return					Boolean
	*/
	public static function Validate($password, $salt, $hash){
		if(Security::Hash($password, $salt) === $hash) return TRUE;
		return FALSE;
		}
	

	/**
	* Returns a random string of characters with the provided length
	* This implementation uses characters 0-9,a-z,A-Z (base-62)
	*
	* Warning: Not cryptographically secure
	*
	* @param	length			Integer - Length of the string to return
	* @return					String
	*/
	public static function GenerateRandomString($length){
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$ret = '';
		for ($i = 0; $i < $length; $i++) $ret .= $chars[rand(0, strlen($chars) - 1)];
		return $ret;
		}
	
	
	/**
	* Returns a cryptographically random string of characters with the provided length
	* This implementation uses characters 0-9,A-F (base-16)
	*
	* @param	length			Integer - Length of the string to return
	* @return					String
	*/
	public static function GenerateRandomCryptoHexString($length){
		return bin2hex(openssl_random_pseudo_bytes($length/2.0));
		}
		
	
	/**
	* Returns a cryptographically random string of characters with the provided length
	* This implementation uses characters 0-9,a-z,A-Z,_ (base-64 rfc3986-safe)
	*
	* @param	length			Integer - Length of the string to return
	* @return					String
	*/
	public static function GenerateRandomCryptoString($length){
		return str_replace(array("/","+","="), array("_","_","_"), base64_encode(openssl_random_pseudo_bytes($length*0.75)));
		}
	
	
	}