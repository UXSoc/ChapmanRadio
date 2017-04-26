<?php

/*
 
 create a box with given text
 
 ?input=Text in Blue1|Text in Blue2||Blue2|Blue1
 
 */
	
define('PATH', '../../');

error_reporting(E_ALL);
	
$input = @$_REQUEST['input'] or die("missing input");

$fontsize = @$_REQUEST['fontsize'] or 22;
if(!is_numeric($fontsize)) $fontsize = 22;

$fontfile = getcwd()."/".PATH."css/fonts/gotham-ultra.ttf";
	
$bbox = imagettfbbox($fontsize, 0, $fontfile, str_replace("|","",$input));
$width = ($bbox[4]-$bbox[6]);
$height = ($bbox[3]-$bbox[5]);
$width += 3; // just a bit of nice padding so antialiasing looks okay
$height += 3; // just a bit of nice padding so antialiasing looks okay

echo "fontsize $fontsize <br /> box will be " . $width." x ".$height."<br />";


// create image
$im = imagecreatetruecolor($width, $height);

// create colors
$white = imagecolorallocate($im, 255, 255, 255);
$blue = array();
$blue[0] = imagecolorallocate($im, 146, 171, 183);
$blue[1] = imagecolorallocate($im, 116, 138, 149);

// white background
imagefilledrectangle($im, 0, 0, $width, $height, $white);
// transparent background
imagecolortransparent($im, $white);

// output parts
$parts = explode("|", $input);
$left = 0;
foreach($parts as $num => $part) {
	echo "printing $part in color ".$blue[$num%2]."<br />";
	imagettftext($im, $fontsize, 0, $left, $fontsize, $blue[$num%2], $fontfile, $part);
	$bbox = imagettfbbox($fontsize, 0, $fontfile, $part);
	$left += $bbox[4] - $bbox[6];
}

$name = preg_replace("/\\W/", "", $input);
$name = strtolower($name).".png";
$path = "img/text/".$name;

imagepng($im, PATH.$path, 6);
imagedestroy($im);



echo "<img src='".PATH.$path."' /><br />".(filesize(PATH.$path)/1000)."kb";
echo "<br /><input value='/".$path."' style='width:480px;' />";

die();

// define the colors
$blue1 = imagecolorallocate($im, 146, 171, 183);
$blue2 = imagecolorallocate($im, 116, 138, 149);



// let's create a pretty header
$blue1 = imagecolorallocate($im, 146, 171, 183);
$blue2 = imagecolorallocate($im, 116, 138, 149);

// show name
$fontsize = 18;
$bbox = imagettfbbox($fontsize, 0, $fontfile, $showname);
imagettftext($im, $fontsize, 0, round(($imagewidth - ($bbox[4] - $bbox[0]) )/2), 24, $blue2, $fontfile, $showname);
// date
$fontsize = 12;
$bbox = imagettfbbox($fontsize, 0, $fontfile, $date);
imagettftext($im, $fontsize, 0, round(($imagewidth - ($bbox[4] - $bbox[0]) )/2), 46, $blue1, $fontfile, $date);


?>