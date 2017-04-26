<?php namespace ChapmanRadio;

define('PATH', '../../');
require_once PATH."inc/global.php";	

$now = date('Y-m-d H:i:s');
$result = DB::GetAll("SELECT * FROM promos WHERE expireson > '$now' ORDER BY category,expireson");

if(empty($result)) die("<p>There are currently no Promos or PSAs to read.</p>");

$prevCategory = "";
echo "<br /><div class='ajaxresults'>";
foreach($result as $row){
	if($prevCategory != strtolower($row['category'])) {
		if(strtolower($row['category']) == 'psas')
			echo "<h3>$row[category] <span style='display: inline; padding-left: 5px; color: #888; font-size: 12px;'>30 minute mark</span></h3>";
		else if(strtolower($row['category']) == 'promos') 
			echo "<h3>$row[category] <span style='display: inline; padding-left: 5px; color: #888; font-size: 12px;'>15 and 45 minute marks</span></h3>";
		else
			echo "<h3>$row[category]</h3>";
		$prevCategory = strtolower($row['category']);
		}
	echo "<a><div class='title'>$row[title]</div><p>$row[description]</p></a>";
	}
echo "</div>";