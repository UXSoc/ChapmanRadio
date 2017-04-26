<?php
remove_filter ('the_content', 'wpautop');
$sss_uploader_dependent = array('jquery', 'jquery-ui-dialog');

$allowed_categories = array('Senior Week', 'Senior Prom', 'Boat Cruise', 'All Night Party');

$UI['header_message'] = 'Please note that we are no longer accepting uploads for categories other than recent and upcoming senior events. The other categories have been greyed out in the drop down menu. If you have images from other categories, please send them to latepics@seniorslideshow.com.';

if(current_user_can('edit_posts')){
	array_push($sss_uploader_dependent,'jquery-ui-tabs','jquery-ui-selectable', 'jquery-ui-droppable','jquery-ui-draggable');
	wp_enqueue_script( "fancybox", site_url('/uploader/fancybox.js'), array('jquery'));
	wp_enqueue_script( "sss_uploader_admin", site_url('/uploader/uploader_admin.js'), array('jquery', 'fancybox'));
	// Getfile link is given to JS by _download()
	if(isset($_REQUEST['getfile'])){
		// Makes this page return the image contents
		die(sss_uploader_admin_action_getfile($_REQUEST['getfile'])); break;
		}
	if(isset($_POST['sss_uploader_admin_action'])){
		switch ($_POST['sss_uploader_admin_action']){
			// Move: Move to specified folder
			case 'move': die(sss_uploader_admin_action_move($_POST['img_id'], $_POST['target'])); break;
			// Delete: Move to trash
			case 'delete': die(sss_uploader_admin_action_move($_POST['img_id'], '_/')); break;
			// Destroy: Move to landfill
			case 'destroy': die(sss_uploader_admin_action_move($_POST['img_id'], '_landfill/')); break;
			// Download: Issues a redirect command to JS to load a download link
			case 'download': die(sss_uploader_admin_action_download($_POST['img_id'])); break;
			// Default: Opps...
			default: die('Error');
			}
	}
	if(isset($_GET['fetch']) && has('manage')){
		$rec = ($_GET['id']=='_downloaded') ? true : false;
		die(sss_uploader_admin_category($_GET['name'], $_GET['id'], $rec));
		}
	}
wp_enqueue_script( "sss-uploader-js", site_url('/uploader/uploader.js'), $sss_uploader_dependent);
wp_enqueue_script( "swfobject", site_url('/uploader/swfobject.js'), array('sss-uploader-js'));
wp_enqueue_style( "sss-uploader", site_url('/uploader/uploader.css'));

function sss_uploader_htmltoarray_saveoutput($index, &$output, $active){
	$save = &$output;
	foreach ($active as $act){
		if(!isset($save[$act]) || is_string($save[$act])) $save[$act] = array();
		$save = &$save[$act];
		}
	$save[$index] = $index;
	return $index;
	}	
function sss_uploader_htmltoarray($html){
	$last = 0;
	$output = array();
	$active = array();
	preg_match_all('%(<(ul)|<(/ul)|<(a)[^>]+>(.*)</a>)%Us', $html, $tags, PREG_SET_ORDER);
	foreach($tags as $tag){
		if($tag[2] == 'ul') array_push($active, $last);
		else if($tag[3] == '/ul') array_pop($active);
		else if($tag[4] == 'a') $last = sss_uploader_htmltoarray_saveoutput($tag[5], $output, $active);
		}
	return $output[0];
	}
function sss_uploader_categoryarray(){
	return sss_uploader_htmltoarray(wp_nav_menu(array('menu'=>'Slide Show Segments', 'echo'=>false)));
	}
function sss_uploader_getselector($id, $menu_array, $display = 'display:none;', $class = 'sss_uploader_child'){
	global $allowed_categories;
	$output = '<select id="'.$id.'" class="'.$class.'" style="'.$display.'" onchange="sss_uploader.change(this);"><option value="N/A" selected="selected">Select a Category</option>';
	$footer = '</select> ';
	foreach ($menu_array as $name => $type){
		$id = sanitize_title($name);
		$disabled = (($allowed_categories != '*') && !(in_array($name, $allowed_categories)));
		if($disabled) $disabled = ' disabled="disabled"';
		if($type == $name){
			$output .= '<option value="child~'.$id.'~null"'.$disabled.'>'.$name.'</option>';
			}
		else{
			// Parent
			$childrenID = 'sss_uploader_selector-'.sanitize_title($name);
			$output .= '<option value="parent~'.$id.'~'.$childrenID.'"'.$disabled.'>'.$name.'</option>';
			$footer .= sss_uploader_getselector($childrenID, $type);
			}
		}
	return $output.$footer;
	}

function sss_uploader_ui($fb=false){
	global $embedLoginObject, $UI;
	if (file_exists($_SERVER['document_root'].'uploader/session.lock')){
		echo _Birdie('The uploader is currently undergoing maintenance work. Please check back soon to upload images');
		}
	else if (is_user_logged_in()){
		if($UI['header_message']){ echo _Notice($UI['header_message']); }
		$user = wp_get_current_user();
		echo "<script type='text/javascript'>jQuery(document).ready(function($){ sss_uploader.attach('#'+sss_uploader.ui, { 'uploader': '/uploader/sss_uploder.swf', 'script': '/uploader/backend.php', 'cancelImg': '/uploader/images/cancel.png', 'scriptData': { 'UID': '".$user->ID."' }, 'folder': '_/', 'buttonText': 'BROWSE', 'queueID': sss_uploader.ui+'_queue', 'fileDesc': 'Image & Video Files', 'fileExt': '*.wmv; *.avi; *.mov; *.jpg; *.jpeg; *.gif; *.png;' }); jQuery(\"#upload_etiquette\").dialog({ width: 600, draggable: false, resizable: false, modal: true, closeText: \"X\", autoOpen: false }); });  </script>";
		echo _NoScript('uploader');
		
		// Step 1: Selector (JS Required)
		echo _YesScript('<div id="sss_uploader_selector_container" class="numbered stepnum1"><h2 class="sss_uploader_step_title">Select a Category for your Uploads:</h2><form action="#selected" autocomplete="off"><div id="sss_uploader_selector_format">'.sss_uploader_getselector('sss_uploader_selector', sss_uploader_categoryarray(), 'display:block;', 'sss_uploader_root').'</div></form><div id="sss_uploader_function_container"><a onclick="sss_uploader.cg()">Change Category</a></div></div>');
		
		// Step 2: Uploader (Hidden)
		echo '<div id="sss_uploader_uploader_container" class="numbered stepnum2" style="display:none">';
		echo '<h2 class="sss_uploader_step_title">Browse for your Files:</h2>';
		echo _info('','id="sss_uploader_uploader_status"');
		echo _Notice('We are now supporting wmv, avi, and mov video formats!<br />If you have any problems uploading, please let us know');
		echo '<input type="file" name="sss_uploader_uploader" id="sss_uploader_uploader" /><div id="sss_uploader_uploader_queue"></div><div><em>Please have a look at the <a onclick="jQuery(\'#upload_etiquette\').dialog(\'open\');">Uploading Etiquette</a> before uploading</em></div>';
		echo '<div id="upload_etiquette" title="Upload Etiquette" style="display:none"><p>A few things to keep in mind before uploading:</p><ul><li>Use good  judgment when selecting images to upload. We want to include the best of your high school experience.</li><li>Be sure that anyone else in your uploads wants to be in the Slide Show.</li><li>Only upload if you are from Reading Memorial High School. Only send us appropiate uploads. Anyone who doesn\'t will be banned.</li><li>Uploads must comply with our <a href="'.site_url('terms-and-conditions').'" target="_blank">terms and conditions</a></li></ul></div>';
		echo '</div>';
		
		}
	elseif($fb==false){
		echo _info('Sorry, we need you to login or register below before you can upload any images. This prevents any malicious user from misusing our site.');
		sss_general_loginforms();
		}
	else{
		echo '<div style="text-align:center"><h2>Want to Help Create <em>The</em><br />Reading Memorial High School<br /><strong>Senior Slide Show</strong>?</h2><h3>First we need you to ';
		$embedLoginObject->facebookButton();
		echo '</h3></div>';
		}
	}
	
function sss_uploader_admin_action_move($id, $target){
	$file = sss_uploader_admin_find_dir($id);
	if($file === false) return '[0xSUAAM2/FileNotFound]';
	$info = pathinfo($file);
	$img = str_replace('//','/', $_SERVER['DOCUMENT_ROOT'].'/content/'.$file);
	$tgt = str_replace('//','/', $_SERVER['DOCUMENT_ROOT'].'/content/'.$target);
	if(!is_dir($tgt)){ mkdir($tgt, 0755, true); }
	$tgt = str_replace('//','/', $tgt.'/'.$info['basename']);
	if(rename($img, $tgt) && rename(sss_general_thumbname($img), sss_general_thumbname($tgt))){
		return '1';
		}
	return '[0xSUAAM8/RenameFailed]';
	}
function sss_uploader_admin_action_download($imgid){
	// URL that will download the image
	$dwnlk = "http://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI']."?getfile=".$imgid;
	// Move img to downloaded folder
	$file = sss_uploader_admin_find_dir($imgid);
	$file = explode('/', $file);
	array_pop($file);
	$target = implode('/', $file);
	sss_uploader_admin_action_move($imgid, '_downloaded'.$target);
	// Return URL
	return 'goto:'.$dwnlk;
	}
function sss_uploader_admin_action_getfile($imgid){
	$file = sss_uploader_admin_find_dir($imgid);
	$path = str_replace('//','/', $_SERVER['DOCUMENT_ROOT'].'/content/'.$file);
	$parts = explode('/', $file);
	$file = array_pop($parts);
	$parts = explode('_', $file);
	header('Content-disposition: attachment; filename='.array_pop($parts));
	header('Content-type: application/image');
	readfile($path);
	}
function sss_uploader_admin_find_dir($id){
	$files = sss_uploader_admin_list_files($_SERVER['DOCUMENT_ROOT'].'/content/', true);
	$test = array();
	foreach($files as $file){
		$info = pathinfo($file);
		$img = explode('_', $info['basename']);
		$test = str_replace('.','',$img[0]);
		if($test == $id) return $file;
		}
	return false;
	}
	
function sss_uploader_admin_image_output($id, $file, $ourl = false){
	$trash = ($id == '_') ? true : false;
	$downloaded = ($id == '_downloaded') ? true : false;
	$ret = '';

	$ifile = explode('_', $file);
	$img = array();
	$img['id'] = str_replace('.','',substr($ifile[0], 1));
	$img['user_id'] = $ifile[1];
	$user = get_userdata($img['user_id']);
	$img['user'] = $user->first_name.' '.$user->last_name;
	$img['orig_file'] = $ifile[2];

	$url = ($ourl === false) ? (site_url(sss_general_thumbname('content/'.$id.$file))) : $ourl;
	
	$ret .= '<li id="'.$img['id'].'_li" class="sss_uploader_li"><span class="sss_uploader_middle"><a target="_blank" href="'.site_url('content/'.$id.$file).'"><img rel="lightbox" alt="Uploaded Image" class="sss_uploader_image" id="'.$img['id'].'" src="'.$url.'" /></a></span><span class="sss_uploader_bottom"><span class="sss_uploader_buttons">';
	if(!$downloaded){
		$ret .= sss_uploader_admin_button($img['id'], 'Download', site_url('uploader/images/download.gif'), 'download');
		if (!$trash) $ret .= sss_uploader_admin_button($img['id'], 'Delete', site_url('uploader/images/trash.png'), 'delete');
		else $ret .= sss_uploader_admin_button($img['id'], 'Delete Forever', site_url('uploader/images/destroy.png'), 'destroy');
		}
	$ret .= '</span><span class="sss_uploader_person">'.$img['user'].'</span></span></li>';
	return $ret;
	}
function sss_uploader_admin_button($id, $text, $img, $action){
	return '<span class="sss_uploader_button"><form method="POST" class="sss_uploader_admin_action_form" action="#'.str_replace(' ','_',$text).'"><input type="hidden" name="sss_uploader_admin_action" value="'.$action.'"><input type="submit" style="display:none;" name="submitter" value="anonSubmitter"><input type="hidden" name="img_id" value="'.$id.'"><a title="'.$text.'" onclick="jQuery(parentNode).submit()"><img src="'.$img.'" width="16px" height="16px" /></a></form></span>';
	}
function sss_uploader_admin_list_files($dir, $recursive = false, $sub = '', $ret = Array()){
	if(is_dir($dir.$sub)) {
		if($dh = opendir($dir.$sub)) {
			while(($file = readdir($dh)) !== false) {
				if($file != "." && $file != "..") {
					if(is_dir($dir.$sub.'/'.$file)){
						if($recursive == true) $ret = sss_uploader_admin_list_files($dir, true, $sub.'/'.$file, $ret);
						}
					else array_push($ret, $sub.'/'.$file);
					}
				}
			closedir($dh);
			}
		}
	return $ret;
	}
function sss_uploader_admin_count_files($id){
	$files = sss_uploader_admin_list_files($_SERVER['DOCUMENT_ROOT'].'/content/'.$id, false);
	$count = 0;
	foreach ($files as $file){
		if(file_exists(sss_general_thumbname($_SERVER['DOCUMENT_ROOT'].'/content/'.$id.$file))){
			$count++;
			}
		elseif(sss_general_isthumb($_SERVER['DOCUMENT_ROOT'].'/content/'.$id.$file)){
			// Nothing, we don't care about thumbs
			}
		else{
			$count++;
			}
		}
	return $count;
	}
	
function sss_uploader_admin_category($name, $id, $recursive = false){
	$files = sss_uploader_admin_list_files($_SERVER['DOCUMENT_ROOT'].'/content/'.$id, $recursive);
	$ret = ''; $pre = '';
	$trash = ($id == '_') ? true : false;
	$approved = ($id == '_approved') ? true : false;
	if ($approved){ $pre .= _info('Approved Images appear in a slideshow on the home page')._debug('This feature has not been implemented. Don\'t expect images to show up on the home page yet.'); }
	foreach ($files as $file){
		if(file_exists(sss_general_thumbname($_SERVER['DOCUMENT_ROOT'].'/content/'.$id.$file))){
			$ret .= sss_uploader_admin_image_output($id, $file);
			}
		elseif(sss_general_isthumb($_SERVER['DOCUMENT_ROOT'].'/content/'.$id.$file)){
			// Nothing, we don't care about thumbs
			}
		else{
			// Non-Thumbable files. Like Movies!!! Or weird image formats. Generic thumb, load real thing on click
			$ret .= sss_uploader_admin_image_output($id, $file, site_url('uploader/images/movie.png'));
			}
		}
	return '<div id="'.$id.'">'.$pre.(($ret == '')?'No images found in '.$name:'<ul class="sss_uploader_ul">'.$ret.'</ul>').'</div>';
	}
function sss_uploader_admin_menu($cat_menu, $ulid = 'root', $prefix = ''){
	$menu = '<ul class="sss_uploader_menu sss_uploader_'.$ulid.'">';
	if($ulid == 'root'){
		$menu .= '<li id="_approved/" class="sss_uploader_root_item"><a href="?fetch&name=Approved Images&id=_approved">Approved Images</a><div style="width: 100%; text-align: center; position: absolute;"></div></li>';
		}
	foreach($cat_menu as $name => $children){
		$id = $prefix.sanitize_title($name).'/';
		$child = ($name != $children) ? sss_uploader_admin_menu($children, 'child', $id) : '';
		$menu .= '<li id="'.$id.'" class="sss_uploader_'.$ulid.'_item"><a href="?fetch&name='.$name.'&id='.$id.'">'.$name.' ('.sss_uploader_admin_count_files($id).')</a><div style="width: 100%; text-align: center; position: absolute;"></div>'.$child.'</li>';
		}
	if($ulid == 'root'){
		$menu .= '<li id="_/" class="sss_uploader_root_item"><a href="?fetch&name=Trash&id=_"><img style="display:inline-block;vertical-align:bottom;" src="'.site_url('uploader/images/trash.png').'" /> Trash</a><div style="width: 100%; text-align: center; position: absolute;"></div></li>';
		$menu .= '<li id="_downloaded/" class="sss_uploader_root_item"><a href="?fetch&name=Downloaded&id=_downloaded"><img style="display:inline-block;vertical-align:bottom;" src="'.site_url('uploader/images/download.gif').'" width="16px" height="16px" /> Downloaded</a><div style="width: 100%; text-align: center; position: absolute;"></div></li>';
		}
	$menu .= '</ul>';
	return $menu;
	}
function sss_uploader_admin(){
	if(current_user_can('edit_posts')){
		echo _NoScript('interface');
		echo _Notice('Any files uploaded prior to 25 May 2011 2100EDT have been downloaded and will not be visible from this interface.');
		echo '<div id="sss_uploader_admin_tabs" class="_scriptrequired">'.sss_uploader_admin_menu(sss_uploader_categoryarray()).'</div><div id="sss_uploader_admin_clear" style="clear:both"></div>';
		}
	else{
		echo _Warning('Error 405 Permission Denied');
		}
	}

?>