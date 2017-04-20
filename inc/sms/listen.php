<?php namespace ChapmanRadio;

/* WARNING: THIS PATH MUST BE MAINTAINED FOR TEXTING TO WORK */

define('PATH', '../../');
require_once PATH."inc/global.php";

$type = Request::Get('type');
	
switch($type) {
	case "SMS":
		Livechat::recieve(Request::Get('number'), Request::Get('text'));
		echo 'SMS OK';
		exit;
	case "VM":
		echo 'VM SKIP';
		break;
	}

die("Unknown type: $type");