<?php namespace ChapmanRadio;

error_reporting(E_ALL);

function error($msg) {
	$msg = str_replace("\"", "'", $msg);
	die("{\"error\":\"$msg\"}");
}