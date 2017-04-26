<?php namespace ChapmanRadio;

define('PATH', '../../');
require_once PATH."inc/global.php";

if(!isset($_REQUEST['genre']))  die("Unable to get genre content. Missing genre variable.");

$genre = Request::Get('genre');
if($genre=="")  die("No genre provided");

$genrecontent = DB::GetFirst("SELECT * FROM genrecontent WHERE genre='$genre'");
if(!$genrecontent || !@$genrecontent['content'])
	die("<h3>$genre</h3><p>Sorry, there is currently no content availably for <b>$genre</b>.</p>");
	
$html = "<h3>$genre</h3><div style='max-height:400px;overflow:auto;text-align:left;'>".$genrecontent['content']."</div>";
$staff = DB::GetFirst("SELECT name,staffposition FROM users WHERE userid='$genrecontent[staffid]'");
if($staff) {
	$lastupdated = date("F jS, Y",strtotime($genrecontent['lastmodified']));
	$html .= "<div class='ajaxdatafrom'>Last updated ".$lastupdated."<br />by $staff[name], $staff[staffposition]</div>";
}

print $html;