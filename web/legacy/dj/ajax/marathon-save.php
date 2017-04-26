<?php namespace ChapmanRadio;

define('PATH', '../../');
require_once PATH."inc/global.php";

$json = [
	"message" => "unknown error",
	"result" => "error"
	];

$slot = Request::GetInteger('slot', NULL);
$show = Request::GetInteger('show', NULL);

if($slot == NULL || $show == NULL){
	$json['message'] = "missing parameter";
	die(json_encode($json));
	}

// Verify the current user is allowed to make this change
$show = ShowModel::FromId($show);

if(!$show){
	$json['message'] = "bad show";
	die(json_encode($json));
	}

$user = Session::GetCurrentUser();

if(!$user){
	$json['message'] = "not authenticated";
	die(json_encode($json));
	}

if(!$show->HasDj($user->id)){
	$json['message'] = "not authorized";
	die(json_encode($json));
	}

// TODO
// $season = "2015SM";

// TODO
$season = "2016SM";

// Figure out day, hour, and cycle from slot
$hour = date('G', $slot);
if($hour < 5){
	$hour += 24;
	$slot -= 86400;
	}

$day = strtolower(date('D', $slot));
$cycle = (date('W', $slot) + 1) % 2;

// Fetch the current schedule
$row = DB::GetFirst("SELECT * FROM schedule WHERE season = :season AND hour = :hour", [ ":season" => $season, ":hour" => $hour ]);
$slot = explode(',', $row[$day]);

// Make sure this slot is available
if($slot[$cycle] != ""){
	$json['message'] = "already taken";
	die(json_encode($json));
	}

// Commit changes
$slot[$cycle] = $show->id;
$field = implode(',', $slot);
DB::Query("UPDATE schedule SET $day = :field WHERE hour = :hour AND season = :season", [ "field" => $field, ":season" => $season, ":hour" => $hour ]);

$json["result"] = "success";
$json["hour"] = $hour;
$json["day"] = $day;
$json["cycle"] = $cycle;
$json["row"] = $row;

$json["message"] = "";
$json["show"] = [
	"name" => $show->name,
	"img64" => $show->img64
	];
die(json_encode($json));