<?php namespace ChapmanRadio;
define('PATH', '../../../');	
require_once PATH."inc/global.php";
Template::RequireLogin("DJ Account");
$userid = Session::GetCurrentUserId();
$showid = Request::GetInteger('showid');
$timestamp = Request::GetInteger('timestamp');
$goodbad = Request::Get('goodbad');
if($goodbad != "good" && $goodbad != "bad") $goodbad = "";
$type = Request::Get('type');
$value = Request::Get('value');
if(!$value) die(json_encode( array("error" => "Please enter something, then try again." ) ));
if(!$userid || !$showid || !$goodbad || !$type || !$value || !$timestamp) die( json_encode( array("error" => "Internal Error: Missing information") ) );
$postedtimestamp = time();
$season = Site::CurrentSeason();
$evalid = DB::Insert("evals", array(
	"userid" => $userid,
	"showid" => $showid,
	"timestamp" => $timestamp,
	"postedtimestamp" => $postedtimestamp,
	"goodbad" => $goodbad,
	"type" => $type,
	"value" => $value,
	"season" => $season
	));
$eval = DB::GetFirst("SELECT * FROM evals WHERE evalid='$evalid'");
$eval['date'] = date("g:ia", $postedtimestamp);
die(json_encode(array("success"=>true,"eval"=>$eval)));