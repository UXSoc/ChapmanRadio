<?php namespace Sinopia;

use DateTime;

class Connection {

	public static function ClientAddress(){
		return Request::GetFrom($_SERVER, 'HTTP_CF_CONNECTING_IP', Request::GetFrom($_SERVER, 'REMOTE_ADDR', NULL));
		}

	public static function IsLocalhost(){
		return $_SERVER['REMOTE_ADDR'] == "::1" || $_SERVER['REMOTE_ADDR'] == "127.0.0.1";
		}

	public static function IsAppEngine(){
		return isset($_SERVER['APPENGINE_ACTIVE']);
		}

	public static function IsAjax(){
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
		}

	}