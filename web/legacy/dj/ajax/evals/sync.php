<?php namespace ChapmanRadio;

define('PATH', '../../../');	
require_once PATH."inc/global.php";

Template::RequireLogin("DJ Account");

$showid = Schedule::HappeningNow();
if($showid <= 0) die("null");
$show = ShowModel::FromId($showid);
if(!$show) die("null");

$json = array();
$json['djs'] = $show->GetDjNamesCsv();
$json['showid'] = $show->id;
$json['showname'] = $show->name;
$json['icon'] = $show->img50;
$json['genre'] = $show->genre;
$json['description'] = Util::Truncate($show->description, 200);
$json['explicit'] = $show->explicit;
$json['timestamp'] = strtotime(date("Y-m-d H:00:00"));
$json['date'] = date("g:ia n/j/y", $json['timestamp']);

// send current evals data to javascript
$data = array();
$userid = Session::getCurrentUserID();
$evals = DB::GetAll("SELECT * FROM evals WHERE timestamp >= ".(time()-60*60*2)." AND showid='$showid' AND userid='$userid'");
foreach($evals as $eval){
	$eval['date'] = date("g:ia", $eval['postedtimestamp']);
	$data[$eval['evalid']] = $eval;
	$data[$eval['evalid']]["id"] = "eval".$eval["evalid"];
	}

$json['evals'] = $data;
die( json_encode( $json ) );