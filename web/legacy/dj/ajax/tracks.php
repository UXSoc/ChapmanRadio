<?php namespace ChapmanRadio;define('PATH', '../../');require_once PATH."inc/global.php";$json = array();if(isset($_GET['sync'])) $json["synced"] = true;if(!isset($_REQUEST['term'])) die(json_encode($json));
$search = trim(urldecode(($_REQUEST['term'])));if($search == "") die(json_encode($json));
// Get a list of tracks from database
$results = TrackModel::Search($search, isset($_GET['sync']));
$json['results'] = array();foreach($results as $track){		// $jrow['dir'] = sha1(strval($row['track_id']));	$json['results'][] = array(		'id' => $track->id,		'img60' => $track->img60,		'name' => $track->name,		'artist' => $track->artist		);	}// Send $jsondie(json_encode($json));