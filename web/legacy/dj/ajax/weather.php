<?php namespace ChapmanRadio;

function ezDate($timestamp, $today, $tomorrow) {
	if(date("Y-m-d",$timestamp) == $today) return date("g:ia",$timestamp).", Today";
	else if(date("Y-m-d",$timestamp) == $tomorrow) return date("g:ia",$timestamp).", Tomorrow";
	else return date("g:ia, F jS",$timestamp);
}

$apiurl = "http://api.wunderground.com/api/fe175a74c90b7659/forecast/conditions/q/92866.json";
$data = json_decode(file_get_contents($apiurl), true);
if(!$data) die("Failed to load weather conditions.");

$current = $data['current_observation'];
$gray = "#575757";
echo "<h3 style='font-size:16px;margin-bottom:6px;'>Orange, CA</h3>
	<table style='margin:auto;'><tr>
		<td style='vertical-align:middle'><img src='$current[icon_url]' alt='' /></td>
		<td style='padding:10px;vertical-align:middle'><b style='font-size:24px;'>$current[temp_f]&#176;F</b></td>
		<td style='vertical-align:middle;text-align:left;'><span style='color:$gray'>Current: </span>$current[weather]
			<br />
			<span style='color:$gray'>Wind:</span> $current[wind_dir] at $current[wind_mph] mph<br />
	</tr></table>
	<table style='margin:10px auto;'><tr>";
	
foreach($data['forecast']['simpleforecast']['forecastday'] as $forecast) {
	echo "<td style='width:93px;'>
		<span style='color:$gray'>".$forecast['date']['weekday']."</span> <br />
		<img src='$forecast[icon_url]' alt='$forecast[icon]' /> <br />
		<span style='color:$gray'>Hi:</span> ".$forecast['high']['fahrenheit']."&#176;<br />
		<span style='color:$gray'>Lo:</span> ".$forecast['low']['fahrenheit']."&#176;
		</td>";
}
echo "</tr></table><p class='ajaxdatafrom'>Weather data from <a href='http://wunderground.com' target='_blank'>Weather Underground</a></p>";
die;