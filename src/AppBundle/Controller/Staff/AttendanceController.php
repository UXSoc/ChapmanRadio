<?php
namespace AppBundle\Controller\Staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 2:32 PM
 */


use ChapmanRadio\DB;
use ChapmanRadio\Picker;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use ChapmanRadio\Util;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AttendanceController extends Controller
{

    /**
     * @Route("/staff/attendance", name="staff_attendance")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Attendance");
        //Template::RequireLogin("/staff/attendance","Staff Resources", "staff");
        Template::css(PATH . "css/formtable.css");
        Template::css(PATH . "css/dl.css");

        Template::Style(".attendance-block { border: 1px solid #CCC; text-align: left; padding: 5px; margin: 5px; vertical-align: top; width: 375px; display: inline-block; }");

        Template::SetBodyHeading("Site Administration", "Attendance");
        Template::AddBodyContent("<div style='margin: 0 auto; text-align: center;'>
	<!--<a href='/staff/alterations' style='display: inline-block; margin: 20px;' class='largeButton green'>Schedule Alterations</a>-->
	<a href='/staff/strikes' style='display: inline-block; margin: 20px;' class='largeButton red'>Manage Strikes</a>
	<a href='/staff/cancelledshows' style='display: inline-block; margin: 20px;' class='largeButton blue'>Cancelled Shows</a>
	</div><br style='clear: both;' />");

        Template::AddBodyContent("<div style='width: 800px; margin: 0 auto; vertical-align: top;'>");

// create the workshop picker
        $picker = array();
        $timestamp = strtotime("this wednesday");
        $oneweek = 60 * 60 * 24 * 7;
        for ($i = $timestamp - $oneweek; $i <= $timestamp + $oneweek; $i += $oneweek)
            $picker[] = "<option value='" . date("n/j/y", $i) . "' " . ($timestamp == $i ? "selected='selected'" : "") . ">" . date("l, F jS", $i) . "</option>";

// record workshop
        Template::AddBodyContent("
	<div class='attendance-block'>
	<h3>Record Workshop Attendance</h3>
	<form class='zeus-form' method='GET' action='/staff/record' target='_blank'>
	<div><select name='date' id='workshop-date'><option value=''> - Pick a Date - </option>" . implode('', $picker) . "</select></div>
	<div>Required for
		<input type='radio' name='requiredfor' value='class' id='requiredforclass' style='width:auto;' />
		<label for='requiredforclass'><b>Class</b></label>
		<input type='radio' name='requiredfor' value='everyone' id='requiredforeveryone' style='width:auto;' checked />
		<label for='requiredforeveryone'><b>Everyone</b></label>
		<input type='radio' name='requiredfor' value='classnew' id='requiredforclassnew' style='width:auto;' />
		<label for='requiredforclassnew'><b>Class + New DJs</b></label>
	</div>
	<input type='hidden' name='type' value='workshop' />
	<input type='submit' value='Launch Recording Utility' />
	<input type='submit' name='quick' value='CardSwipe Version' />
	</form>
	</div>");

// create the event picker
        $picker = array();
        $events = DB::GetAll("SELECT * FROM attendance_events ORDER BY timestamp DESC"); // WHERE season = :season", array( ":season" => Site::CurrentSeason()));
        foreach ($events as $event)
            $picker[] = "<option value='" . $event['timestamp'] . "'>" . $event['eventname'] . "</option>";

// record event
        Template::AddBodyContent("
	<div class='attendance-block'>
	<h3>Record Special Event Attendance</h3>
	<form class='zeus-form' method='get' action='/staff/record' target='_blank'>
	<div><select name='date'><option value=''> - Pick an Event - </option>" . implode('', $picker) . "</select></div>
	<input type='hidden' name='type' value='event' />
	<input type='submit' value='Launch Recording Utility' />
	<input type='submit' name='quick' value='CardSwipe Version' />
	</form>
	</div>");

// view by workshop
        $workshops = DB::GetAll("SELECT timestamp FROM attendance WHERE type='workshop' GROUP BY timestamp ORDER BY timestamp DESC");
        Template::AddBodyContent("
	<div class='attendance-block'>
	<h3>View Workshop Attendance</h3>
	<form class='zeus-form' method='get' action='/staff/reports/workshop' target='_blank'>
	<div>" . Util::picker('timestamp', $workshops, function ($row) {
                return "<option value='$row[timestamp]'>" . date("l F, jS, Y", $row['timestamp']) . "</option>";
            }) . "</div>
	<div><input type='submit' value='Generate Report' /></div>
	</form>
	</div>");

// view by event
        $events = DB::GetAll("SELECT timestamp FROM attendance WHERE type='event' GROUP BY timestamp ORDER BY timestamp DESC");
        Template::AddBodyContent("
	<div class='attendance-block'>
	<h3>View Event Attendance</h3>
	<form class='zeus-form' method='get' action='/staff/reports/event' target='_blank'>
	<div>" . Util::picker('timestamp', $events, function ($row) {
                return "<option value='$row[timestamp]'>" . date("l F, jS, Y", $row['timestamp']) . "</option>";
            }) . "</div>
	<div><input type='submit' value='Generate Report' /></div>
	</form>
	</div>");

// view by user
        Template::AddBodyContent("
	<div class='attendance-block'>
	<h3>View User Attendance</h3>
	<form class='zeus-form' method='get' action='/staff/reports/user' target='_blank'>
	<div>" . Picker::Users() . "</div>
	<div><input type='submit' value='Generate Report' /></div>
	</form>
	</div>");

// view by show
        $opts = array();
        $season = Site::CurrentSeason();
        $result = DB::GetAll("SELECT showid,showname FROM shows WHERE seasons LIKE '%$season' ORDER BY showname");
        foreach ($result as $row) $opts[] = "<option value='$row[showid]'>$row[showname]</option>";

        Template::AddBodyContent("
	<div class='attendance-block'>
	<h3>View Show Attendance by Show</h3>
	<form class='zeus-form' method='get' action='/staff/reports/show' target='_blank'>
	<div><select name='showid'><option value=''> - Pick a Show - </option>" . implode($opts) . "</select></div>
	<div><input type='submit' value=' View Attendance ' /></div>
	</form>
	</div>");


        Template::AddBodyContent("
	<div class='attendance-block'>
	<h3>View Show Attendance by Date</h3>
	<form class='zeus-form' method='get' action='/staff/reports/date' target='_blank'>
	<div><input type='text' name='date' value='" . date("F jS, Y") . "' /></div>
	<div><input type='submit' value=' View Attendance ' /></div>
	</form>
	</div>");


        Template::AddBodyContent("</div>");


        return Template::Finalize($this->container);


    }
}