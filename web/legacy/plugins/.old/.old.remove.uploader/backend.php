<?php
function rexit($message){
	ob_end_clean();
	header("Connection: close");
	ignore_user_abort();
	ob_start();
	echo $message;
	$size = ob_get_length();
	header("Content-Length: $size");
	ob_end_flush();
	exit();
	}
function thumbname($filename){
	$fileparts = explode('/', $filename);
	$last = end($fileparts);
	$notlast = substr($filename, 0, (0 - strlen($last)));
	return $notlast.'thumb_'.$last;
	}
function imgname($filename){
	$fileparts = explode('/', $filename);
	$last = end($fileparts);
	$notlast = substr($filename, 0, (0 - strlen($last)));
	if(strpos($last, 'thumb_') === 0){
		return $notlast.substr($last, 6);
		}
	return $false;
	}
function is_thumb($filename){
	if(substr_count($filename, 'thumb_') != '0') return true;
	return false;
	}
function list_files($dir, $recursive = false, $sub = '', $ret = Array()){
	if(is_dir($dir.$sub)) {
		if($dh = opendir($dir.$sub)) {
			while(($file = readdir($dh)) !== false) {
				if($file != "." && $file != "..") {
					if(is_dir($dir.$sub.'/'.$file)){
						if($recursive == true) $ret = list_files($dir, true, $sub.'/'.$file, $ret);
						}
					else array_push($ret, $sub.'/'.$file);
					}
				}
			closedir($dh);
			}
		}
	return $ret;
	}
function ConvertImage($image){   
	$f = pathinfo($image);
	list($w, $h, $t) = getimagesize($image);
	if($t == 2) return $image;
	switch ($t){
		case 1: $d = imagecreatefromgif($image); break;
		case 2: $d = imagecreatefromjpeg($image); break;
		case 3: $d = imagecreatefrompng($image); break;
		default: return 'ERROR: '.$image.' is not a valid image file ('.$t.')'; break;
		}
	try{
		$n = imagecreatetruecolor($w, $h);
		imagefilledrectangle($n, 0, 0, $w, $h, imagecolorallocate($n, 255, 255, 255));
		imagecopyresampled($n,$d,0,0,0,0,$w,$h,$w,$h);
		unlink($image);
		$image = $f['dirname'].'/'.$f['filename'].'.jpg';
		imagejpeg($n, $image, 100);
		}
	catch (Exception $e) {
		return 'ERROR: '.$e->getMessage();
		}
	return $image;
	}
function Thumbnail($image){
	$dest = thumbname($image);
	$width = 150; $height = 150;
	if (!isset($image) || !file_exists($image)) return "ERROR: Image does not exist";
	ini_set('memory_limit', '-1');
	list($ox, $oy, $ot, $is) = @GetImageSize($image);
	if ($ox > $width or $oy > $height){
		$px = $ox / $width;
		$py = $oy / $height;
		if ($py > $px) { $width = $ox / $py; }
		else { $height = $oy / $px; }
		try{
			$i = imagecreatetruecolor($width, $height);
			imagefilledrectangle($i, 0, 0, $width, $height, imagecolorallocate($i, 255, 255, 255));
			$im = imagecreatefromjpeg($image);
			imagepalettecopy($i, $im);
			imagecopyresampled($i, $im, 0, 0, 0, 0, $width, $height, $ox, $oy);
			imagejpeg($i, $dest, 100);
			}
		catch (Exception $e) {
			return 'ERROR: '.$e->getMessage();
			}
		return 'Success';
		}
	copy($image, $dest);
	return 'Success (But image is smaller than 150x150)';
	}
function sob($what){ file_put_contents('access.log', $what."\r\n", FILE_APPEND); }
function process($file){
	$nfile = ConvertImage($file);
	if(strpos($nfile, 'ERROR:')===0){
		sob('  - '.$nfile);
		return;
		}
	else if($nfile != $file){
		sob('  - Converted Image: To JPG');
		}
	sob('  - Created Thumbnail: '.Thumbnail($nfile));
	}

if(!isset($included)){
	if (!empty($_FILES)) {
		$extentions = array('wmv', 'avi', 'mov', 'jpg', 'jpeg', 'png', 'gif');
		$targetPath = str_replace('//','/', $_SERVER['DOCUMENT_ROOT'].'/content/');
		$targetFile = $_REQUEST['folder'].stripslashes(strtoupper(uniqid('', true)).'_'.$_REQUEST['UID'].'_'.str_replace("_", "-", $_FILES['Filedata']['name']));
		
		if(!is_dir($targetPath.$_REQUEST['folder'])){ mkdir($targetPath.$_REQUEST['folder'], 0755, true); }
		$fileInfo = pathinfo($_FILES['Filedata']['name']);
		if (in_array(strtolower($fileInfo['extension']), $extentions)){
			sob("User #".$_REQUEST['UID']." uploaded ".$targetFile);
			if(move_uploaded_file($_FILES['Filedata']['tmp_name'], $targetPath.$targetFile)){
				register_shutdown_function('process', $targetPath.$targetFile);
				rexit("1");
				}
			sob("  - ERROR: Upload failed for $targetFile");
			rexit('Internal Server');
			}
		sob("  - ERROR: Invalid file type for $targetFile (".$fileInfo['extension'].")");
		rexit('Invalid File Type');
		}
	sob('  - ERROR: No $_FILES Provided ... Hacking Attempt?');
	rexit('<strong>Error 500</strong> Invalid Authentication');
	}
?>