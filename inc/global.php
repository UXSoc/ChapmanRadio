<?php namespace ChapmanRadio;

$__cr_global_startime = microtime(true);

session_set_cookie_params(0, '/', '.chapmanradio.com');
session_name("ChapmanRadio");

ini_set('display_errors', 1);
error_reporting(-1);
date_default_timezone_set('America/Los_Angeles');
define('BASE', __DIR__);
define('ROOT', dirname(BASE));

$_SERVER['PHP_SELF'] = substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF'])-4);


spl_autoload_register(function($className) {
	$classParts = explode('\\', strtolower($className));
	$class = end($classParts);
	if($classParts[0] == "sinopia" && file_exists(BASE . "/sinopia/$class.php")){
		require_once BASE . "/sinopia/$class.php";
		return true;
		}
	else if($classParts[0] == "chapmanradio" && file_exists(BASE . "/$class.php")){
		require_once BASE . "/$class.php";
		return true;
		}
	else if (file_exists(BASE . '/' . strtolower($className) . '.php')){
		require_once BASE . '/' . strtolower($className) . '.php'; 
		return true;
		}
	return false; 
	});

//set_exception_handler(function ($ex){ Template::UnhandledException($ex); });