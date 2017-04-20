<?php namespace Sinopia;

class Http {
	
	public static function Post($url, $headers, $data){
		
		if(is_array($headers)) $headers_formed = implode("\r\n", $headers);
		else $headers_formed = $headers;
		
		if(function_exists("curl_init")) return self::CurlPost($url, $headers_formed, $data);
		if(function_exists("file_get_contents")) return self::FGCPost($url, $headers_formed, $data);
		throw new \Exception("No external communication library found. Install curl or file_get_contents.");
		}
		
	private static function CurlPost($url, $headers, $data){
		// Open connection
		$ch = curl_init();

		// Set the url, number of POST vars, POST data
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, true );
		if($headers != "") curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);
		
		// Execute post
		$result = curl_exec($ch);
		
		// Close connection
		curl_close($ch);
		
		return $result;
		}
	
	private static function FGCPost($url, $headers, $data){
		$context = stream_context_create([
			'http' => [
				'method' => 'POST',
				'header'=> $headers,
				'content' => $data
				]
			]);
		return file_get_contents( $url , false, $context);     
		}
	
	}