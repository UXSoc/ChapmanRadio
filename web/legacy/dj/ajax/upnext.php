<?php namespace ChapmanRadio;

define('PATH', '../../');
require_once PATH."inc/global.php";

if(!Site::$Broadcasting) die();

// number of minutes when this will display
if(date("i") < 48) exit;

$showid = Schedule::ShouldHappenAt(strtotime('+1 hour'));	
if($showid <= 0) exit;

$show = ShowModel::FromId($showid);
if(!$show) exit;

$html = "<img src='/img/sidebar/upnext.png' alt='' style='margin-top:20px;' />";
$html .= "<div class='upnext'>";
$html .= "<img src='".$show->img50."' alt='' style='float:left;margin:0;' />";
$html .= "<h3 class='showname'>".$show->name."</h3>";
$html .= "<p class='genre'>".$show->genre."</p>";
$html .= "</div>";
print $html;
exit;
