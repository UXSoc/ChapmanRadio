<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use ChapmanRadio\Request;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class EventsController extends  Controller
{
    /**
     * @Route("/staff/events", name="staff_events")
     */
    public function indexAction(ContainerInterface $container = null)
    {

        Template::SetPageTitle("Events - Admin");
        Template::SetBodyHeading("Site Administration", "Events");
        Template::RequireLogin("Staff Resources", "staff");

        Template::AddBodyContent("<div class='leftcontent'>");

        Template::shadowbox();
        Template::css("/css/formtable.css");
        Template::css("/css/calendaricon.css");
        Template::style(".events{margin:10px 40px;}.event{margin:10px auto;}.calendaricon{margin:0 10px 10px;float:left;}");
        Template::script("
	e={
		show:function(eventid){
			\$('#edit'+eventid).slideDown();
			eid('show'+eventid).style.display='none';
			eid('hide'+eventid).style.display='block';
		},
		hide:function(eventid){
			\$('#edit'+eventid).slideUp();
			eid('show'+eventid).style.display='block';
			eid('hide'+eventid).style.display='none';
		}
	};");

        if(isset($_POST['NewEvent'])) {
            $title = Request::Get('title');
            $timestamp = strtotime(Request::Get('when'));
            if(!$title) {
                Template::AddInlineError("Please enter a title for your new event");
            }
            else if(!$timestamp || $timestamp == -1) {
                Template::AddInlineError("Please enter a valid time for your new event");
            }
            else {
                $eventid = DB::Insert("events", array('title' => $title, 'timestamp' => $timestamp));
                Template::AddInlineSuccess("Your event has been created.");
            }
        }

        Template::AddBodyContent("<h2>New Event</h2>
<form method='post'>
<table class='formtable' cellspacing='0' style='margin:10px auto;'>
<tr class='oddRow'><td>Title</td><td><input type='text' name='title' value=\"".Request::Get('title')."\" /></td></tr>
<tr class='evenRow'><td>When</td><td><input type='text' name='when' value=\"".Request::Get('when')."\" /></td></tr>
<tr class='oddRow'><td colspan='2' style='text-align:center;'><input type='submit' name='NewEvent' value=' New Event ' /><br /><span style='color:#757575;font-size:11px;'>You can edit more details after you create the event.<br />It won't be visible on the website until you activate it.</span></td></tr>
</table>
</form>");

        if(isset($_POST['SaveEvent'])) {
            $eventid = Request::GetInteger('eventid');
            if(!$eventid) Template::Error("Internal Programming Error: Missing eventid post variable in SaveEvent");

            $updates = array();
            $updates['title'] = Request::Get('title');
            $updates['location'] = Request::Get('location');
            $updates['link'] = Request::GetUrl('link');
            $updates['description'] = Request::Get('description');
            $updates['active'] = Request::GetBool('active');
            $updates['primaryeventpicid'] = Request::GetInteger('primaryeventpicid');

            $timestamp = strtotime($_REQUEST['when']);
            if(!$timestamp || $timestamp == -1) Template::AddInlineError("The time you entered was not recognized and was not saved.");
            else $updates['timestamp'] = $timestamp;

            DB::Update("events", "eventid", $eventid, $updates);

            Template::AddInlineSuccess("Your changes to ".stripslashes($updates['title'])." have been saved.");

            // check pics
            if(is_array(@$_FILES['file'])) {
                foreach(@$_FILES['file']['tmp_name'] as $index => $tmp_name) {
                    if(!$tmp_name || $_FILES['file']['error'][$index]) continue;
                    $s = getimagesize($tmp_name);
                    switch($s['mime']) {
                        case "image/png": $original = imagecreatefrompng($tmp_name); break;
                        case "image/jpeg": $original = imagecreatefromjpeg($tmp_name); break;
                        case "image/gif": $original = imagecreatefromgif($tmp_name); break;
                        default: $original = null;
                    }
                    if(!$original) {
                        Template::AddInlineError("The file ".$_FILES['file']['name'][$index]." failed to upload because it is not a valid PNG, JPEG, or GIF image");
                    }
                    else {
                        $name = trim($_FILES['file']['name'][$index]);
                        $name = preg_replace("/\\.[^.\\s]{1,10}$/", "", $name);
                        $name = str_replace(" ","_",$name);
                        $name = preg_replace("/[^\\w_]/","",$name);
                        if(!$name) $name = "Upload".($index+1);
                        $now = time();
                        $pathParts = array("img","events",date("Y",$now),date("m",$now),date("d",$now));
                        $path = "";
                        foreach($pathParts as $part) {
                            if(!file_exists(PATH.$path.$part)) mkdir(PATH.$path.$part);
                            $path .= "$part/";
                        }
                        $full = "$path$now-$name-full.jpg";
                        $pic = "$path$now-$name-pic.jpg";
                        $icon = "$path$now-$name-icon.jpg";
                        imagejpeg($original, PATH.$full, 84);
                        imagejpeg($original, PATH.$pic, 75);
                        imagejpeg($original, PATH.$icon, 75);
                        imagedestroy($original);
                        require_once PATH."inc/resize.php";
                        smartResize(PATH.$full, 880, 2400);
                        smartResize(PATH.$pic, 200, 600);
                        resize(PATH.$icon, 50, 50);
                        DB::Query("INSERT INTO eventpics (eventid,pic,icon,full) VALUES ($eventid,'/$pic','/$icon','/$full')");
                    }
                }
            }

            // update pic captions
            $result = DB::GetAll("SELECT * FROM eventpics WHERE eventid='$eventid'");
            foreach($result as $pic){
                $eventpicid = $pic['eventpicid'];
                if(isset($_REQUEST['deletepic'.$eventpicid])) {
                    unlink(PATH.substr($pic['full'],1));
                    unlink(PATH.substr($pic['icon'],1));
                    unlink(PATH.substr($pic['pic'],1));
                    DB::Query("DELETE FROM eventpics WHERE eventpicid='$eventpicid'");
                    Template::AddInlineNotice("That picture has been permanently deleted","#A00");
                }
                else {
                    $caption = Request::Get("eventpic{$eventpicid}caption");
                    DB::Query("UPDATE eventpics SET caption = :caption WHERE eventpicid = :id", array('caption' => $caption, 'id' => $eventpicid));
                }
            }
        }

        Template::AddBodyContent("<h2>Events</h2>");
        $result = DB::GetAll("SELECT * FROM events ORDER BY timestamp DESC");

        if(empty($result)) Template::AddBodyContent("<p>There are no events to display.</p>");
        else {
            Template::AddBodyContent("<div class='events'>");
            foreach($result as $event){
                $eventid = $event['eventid'];
                Template::AddBodyContent("<div class='event'>");
                Template::AddBodyContent("<form method='post' action='$_SERVER[PHP_SELF]' enctype='multipart/form-data'>");
                $month = date("M.", $event['timestamp']);
                $day = date("j", $event['timestamp']);
                Template::AddBodyContent("<div class='calendaricon'><span class='month'>$month</span><span class='day'>$day</span></div>");
                Template::AddBodyContent("<h3>$event[title]</h3>");
                Template::AddBodyContent("<p><a href='javascript:e.show($eventid)' id='show$eventid'>Edit</a><a id='hide$eventid' href='javascript:e.hide($eventid)' style='display:none;'>Cancel</a></p><br style='clear:both;' />");
                Template::AddBodyContent("<div id='edit$eventid' style='display:none;'><table class='formtable' cellspacing='0'>");
                Template::AddBodyContent("<tr class='oddRow'><td>Title</td><td><input type='text' name='title' value=\"$event[title]\" ></td></tr>");
                Template::AddBodyContent("<tr class='evenRow'><td>When</td><td><input type='text' name='when' value=\"".date("g:ia n/j/y",$event['timestamp'])."\" ></td></tr>");
                Template::AddBodyContent("<tr class='oddRow'><td>Location</td><td><input type='text' name='location' value=\"$event[location]\" ></td></tr>");
                Template::AddBodyContent("<tr class='evenRow'><td>Link</td><td><input type='text' name='link' value=\"$event[link]\" ><br /><span style='color:#757575;font-size:11px;'>Optional, e.g. link to Facebook Event page</span></td></tr>");
                Template::AddBodyContent("<tr class='oddRow'><td colspan='2' style='text-align:center'>Description<br /><textarea name='description'>$event[description]</textarea></td></tr>");
                Template::AddBodyContent("<tr class='evenRow'><td colspan='2' style='text-align:center;'>Pictures<br />");

                $picsresult = DB::GetAll("SELECT * FROM eventpics WHERE eventid='$eventid'");
                if(!empty($picsresult)){
                    Template::AddBodyContent("<table>");
                    foreach($picsresult as $pic){
                        Template::AddBodyContent("<tr><td><a href='$pic[full]' rel='shadowbox[event$eventid]' title='$pic[caption]'><img src='$pic[pic]' alt=''/></a></td>");
                        Template::AddBodyContent("<td>Caption: <input type='text' name='eventpic$pic[eventpicid]caption' value=\"$pic[caption]\" /><br />");
                        Template::AddBodyContent("<input type='checkbox' style='width:auto;' name='deletepic$pic[eventpicid]' id='deletepic$pic[eventpicid]' value='1' onclick='return confirm(\"Are you sure? If you delete this picture, you will not be able to recover it.\")' /> <label for='deletepic$pic[eventpicid]'>Delete</label><br />");
                        Template::AddBodyContent("<input type='radio' name='primaryeventpicid' style='width:auto;' id='primaryeventpicid$pic[eventpicid]' value='$pic[eventpicid]' ".($event['primaryeventpicid'] == $pic['eventpicid']? "checked='checked":"")." /> <label for='primaryeventpicid$pic[eventpicid]'>Primary Picture</label>");
                        Template::AddBodyContent("</td></tr>");
                    }
                    Template::AddBodyContent("<tr><td>&nbsp;</td><td><input type='radio' name='primaryeventpicid' value='0' ".($event['primaryeventpicid']?"":"checked='checked'")." style='width:auto;' id='event{$eventid}noprimarypic' /> <label for='event{$eventid}noprimarypic'>No primary picture</label></td></tr>");
                    Template::AddBodyContent("</table>");
                }

                Template::AddBodyContent("Upload pictures:<br /><input type='file' name='file[]' /><br /><input type='file' name='file[]' /><br /><input type='file' name='file[]' /></td></tr>");
                Template::AddBodyContent("<tr class='oddRow'><td colspan='2' style='text-align:center'><input type='radio' name='active' value='0' ".($event['active']?"":"checked='checked'")." id='inactive$eventid' style='width:auto;' /> <label for='inactive$eventid'><b>Inactive</b>, not visible on website</label><br /><input type='radio' name='active' value='1' ".($event['active']?"checked='checked'":"")." id='active$eventid' style='width:auto;' /> <label for='active$eventid'><b>Active</b>, visible on website</label></td></tr>");
                Template::AddBodyContent("<tr class='evenRow'><td colspan='2' style='text-align:center'><input type='hidden' name='eventid' value='$eventid' /><input type='submit' name='SaveEvent' value=' Save Changes ' /></td></tr>");
                Template::AddBodyContent("</table></div>");
                Template::AddBodyContent("</form>");
                Template::AddBodyContent("</div>");
            }
            Template::AddBodyContent("</div>");
        }

        Template::AddBodyContent("</div>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }

}