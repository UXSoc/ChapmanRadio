<?php namespace ChapmanRadio;

class Log {
	
	public static function StaffEvent($detail){
		//if(Session::GetCurrentUserID() == 571) return;
		DB::Query("INSERT INTO staff_log (timestamp, userid, details) VALUES (:time, :user, :details)", array(
			":time" => date("Y-m-d H:i:s"),
			":user" => Session::GetCurrentUserID(),
			":details" => $detail));
		}
		
	public static function Error($code, $data){
		try{
			DB::Query("INSERT INTO errors (ip, code, data, referer, useragent) VALUES (:ip, :code, :data, :referer, :agent)", array(
				":ip" => Request::ClientAddress(),
				":code" => $code,
				":data" => print_r($data, true), 
				":referer" => Request::GetFrom($_SERVER, 'REQUEST_URI'),
				":agent" => Request::GetFrom($_SERVER, 'HTTP_USER_AGENT')
				));
			mail('webmaster@chapmanradio.com', "[CHAPMANRADIO] $code", print_r($data, true)."\n\nServer:".print_r($_SERVER, true));
			}
		catch(Exception $ex){ }
		}
	}
	