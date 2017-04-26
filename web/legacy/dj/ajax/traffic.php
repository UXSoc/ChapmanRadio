<?php namespace ChapmanRadio;

$mapquestkey = "Fmjtd%7Cluua250zl9%2C7w%3Do5-962aq6";
$mapquestloc = "boundingBox=34.016409,-118.125815,33.559874,-117.576498";
$data = @json_decode(file_get_contents("http://www.mapquestapi.com/traffic/v1/incidents?key=$mapquestkey&$mapquestloc&filters=construction,incidents"));
if(!$data) die("Failed to load traffic conditions.");

echo "<div style='text-align: left;'><ul>";
foreach($data->incidents as $incident) echo "<li style='padding: 5px; list-style: none;'>".$incident->fullDesc."</li>";
echo "</ul></div>";

if(count($data->incidents) == 0) echo "<div style='padding: 5px;'>MapQuest has no incidents to report</div>";

echo "<p class='ajaxdatafrom'>Traffic data from <a href='http://mapquest.com' target='_blank'>MapQuest</a></p>";
die;