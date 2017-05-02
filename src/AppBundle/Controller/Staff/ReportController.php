<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/28/17
 * Time: 8:12 AM
 */

namespace AppBundle\Controller\Staff;


use ChapmanRadio\Strikes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ChapmanRadio\DB;
use ChapmanRadio\Report;
use ChapmanRadio\Request;
use ChapmanRadio\Schedule;
use ChapmanRadio\Season;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use ChapmanRadio\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReportController extends Controller
{


    /**
     * @Route("/staff/reports/workshop", name="staff_reports_workshop")
     */
    public function workshopAction()
    {
        define('PATH', '../');

        Template::SetPageTemplate("report");
        Template::SetPageTitle("Workshop Attendance Report");

        //Template::RequireLogin("/staff/reports/workshop", "Staff Resources", "staff");
        Template::JS("/legacy/staff/js/viewAttendance.js");
        Template::Style(" .reportlink { color:#757575; font-size: 11px; } ");

        $timestamp = Request::GetInteger('timestamp');
        if (!$timestamp) die("<p>Error: Missing or invalid timestamp variable.</p>");

        $totals = array("present" => 0, "absent" => 0, "excused" => 0, "optional" => 0);
        $djs = array("class" => array(), "club" => array());
        $haveinfo = array();
        $result = DB::GetAll("SELECT * FROM attendance WHERE type='workshop' AND timestamp='$timestamp' ORDER BY status");

        foreach ($result as $row) {
            $userid = @$row['userid'] or 0;
            if (!$userid) continue;
            if (!isset($haveinfo[$userid])) {
                $temp = DB::GetFirst("SELECT userid, name, djname, classclub FROM users WHERE userid='$userid'");
                $djs[$temp['classclub']]["$temp[name] $temp[userid]"] = $temp;
                $haveinfo[$userid] = array($temp['classclub'], "$temp[name] $temp[userid]");
            }
            list($classclub, $uid) = $haveinfo[$userid];
            $djs[$classclub][$uid]["status"] = $row['status'];
            $totals[$row['status']]++;
        }


        ksort($djs['class']);
        ksort($djs['club']);
        $ccsections = array();
        $colors = array("present" => "#090", "absent" => "#A00", "excused" => "#D60", "" => "", "optional" => "#00A");
        foreach ($djs as $classclub => $uids) {
            $urows = array();
            foreach ($uids as $uid => $dj) {
                $userid = $dj['userid'];
                $urows[] = "<tr><td>$dj[name] <a class='reportlink' href='/staff/reports/user?userid={$userid}'>#$userid</a></td><td id='displayUser$userid'><b style='color:" . $colors[$dj['status']] . "'>$dj[status]</b></td></tr>";
            }
            $ccsections[] = "<h3 style='margin-top:20px;'>" . ucfirst($classclub) . "</h3><table>" . implode($urows) . "</table>";
        }


        Template::AddBodyContent("
	<h1>Chapman Radio</h1><h2>Workshop Attendance Report</h2>
	<p>Date: <b>" . date("l F jS, Y", $timestamp) . "</b></p>
	<p>Total Present: <b style='color:#090'>$totals[present]</b></p>
	<p>Total Absent: <b style='color:#A00'>$totals[absent]</b></p>
	<p>Total Excused: <b style='color:#D60'>$totals[excused]</b></p>
	" . implode($ccsections) . "");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }

    /**
     * @Route("/staff/reports/user", name="staff_reports_user")
     */
    public function userAction()
    {
        define('PATH', '../');


        Template::SetPageTemplate("report");
        Template::SetPageTitle("Attendance");
        //Template::RequireLogin("/staff/reports/user", "Staff Resources", "staff");
        Template::JS("/staff/js/viewAttendance.js");

        $season = Site::CurrentSeason(true);
        $seasonName = Season::name($season);

        Template::Style(" td { vertical-align:top; padding: 4px; }");
        Template::Style(" .reportlink { color:#757575; font-size: 11px; } ");

        $userid = Request::GetInteger('userid', null);
        if (!$userid) die("Error: Missing userid variable.");

        $user = UserModel::FromId($userid);
        if (!$user) die("Error: Invalid userid variable. User ID#$userid does not exist.");

// what shows is this user in?
        $shows = $user->GetShowModels();
// strikes
        $check = Strikes::check($userid);

        Template::SetPageTitle("Attendance for " . $user->name);

        Template::AddBodyContent("
	<h1>Chapman Radio</h1><h2>User Attendance Report</h2>
	<p>User: <b>" . $user->name . "</b> (User ID #: " . $user->id . ")</p>
	<p>Email: <b>" . $user->email . "</b></p>
	<p>Phone: <b>" . $user->phone . "</b></p>
	<p>Class or Club: <b>" . ucfirst($user->classclub) . "</b></p>
	<p>Status: <b>" . (count($user->seasons) == 1 ? "New" : "Returning") . " (seasons=" . count($user->seasons) . ")</b></p>
	<p>Show Absences: <b>$check[showAbsences]</b></p>
	<p>Tardies: <b>$check[tardies]</b></p>
	<p>Workshop Absences: <b>$check[workshopAbsences]</b></p>
	<p>Total Strikes: <b>$check[totalStrikes]</b></p>");

// there are 3 types of attendance
        $counters = array();
        $attsections = array();
        foreach (array("show", "workshop", "event") as $type) {
            $queries = array();
            $queries[] = "userid='$userid'";
            foreach ($shows as $show) $queries[] = "showid='" . $show->id . "'";
            $result = DB::GetAll("SELECT * FROM attendance WHERE (" . implode(" OR ", $queries) . ") AND type='$type' AND season='$season' ORDER BY timestamp");
            $count = 0;
            $counters[$type] = array("tardies" => 0, "excused" => 0, "present" => 0, "absences" => 0);
            $records = array();
            foreach ($result as $row) {
                $status = $row['status'];
                $attendanceid = $row['attendanceid'];
                $count++;
                $color = Report::$colors[$status];
                switch ($type) {
                    case "show":
                        $date = date("l n/j/Y g:ia", $row['timestamp']);
                        $records[] = "<tr><td><small>{$attendanceid}</small></td><td>$date <br /><a class='reportlink' href='/staff/reports/show?showid={$row['showid']}'>Show #{$row['showid']}</a> </td><td id='displayStatus$attendanceid'><b style='color:$color'>$status</b></td><td id='displayLate$attendanceid'>" . Report::dispLate($row) . "</td><td class='noPrint'><form method='get' action='javascript:att.modify($attendanceid)'>" . picker($attendanceid, $row['status'], "att.modify($attendanceid)") . "<input type='text' id='late$attendanceid' value='$row[late]' maxlength='2' style='width:48px;' /><input type='submit' value='&gt;' /></form></td></tr>";
                        break;
                    case "workshop":

                        $date = date("l n/j/Y", $row['timestamp']);

                        $records[] = "<tr><td><small>{$attendanceid}</small></td><td>$date <br /><a class='reportlink' href='/staff/reports/workshop?timestamp={$row['timestamp']}'>Workshop #{$row['timestamp']}</a> </td><td id='displayStatus$attendanceid'><b style='color:$color'>$status</b></td><td id='displayLate$attendanceid'>" . Report::dispLate($row) . "</td><td class='noPrint'><form method='get' action='javascript:att.modify($attendanceid)'>" . picker($attendanceid, $row['status'], "att.modify($attendanceid)") . "<input type='text' id='late$attendanceid' value='$row[late]' maxlength='2' style='width:48px;' /><input type='submit' value='&gt;' /></form></td></tr>";

                        break;
                    case "event":
                        $date = date("n/j/y", $row['timestamp']);
                        $records[] = "<tr><td><small>{$attendanceid}</small></td><td>$date</td><td></td><td id='displayStatus$attendanceid'><b style='color:$color'>$row[status]</b></td><td id='displayLate$attendanceid' style='display:none'>&nbsp;</td><td class='noPrint'><form method='get' action='javascript:att.modify($attendanceid)'>" . picker($attendanceid, $row['status'], "att.modify($attendanceid)") . "<input type='hidden' id='late$attendanceid' value='$row[late]' maxlength='2' style='width:48px;' /><input type='submit' value='&gt;' /></form></td></tr>";
                        break;
                }
            }
            if (!$count) $records[] = "<tr><td><p style='color:#848484'>No data.</p></td><tr>";
            Template::AddBodyContent("<h3>" . ucfirst($type) . " Attendance Records</h3><table>" . implode($records) . "</table>");
        }

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());

    }

    /**
     * @Route("/staff/reports/show", name="staff_reports_show")
     */
    public function eventShow()
    {
        define('PATH', '../');

        Template::SetPageTemplate("report");
        Template::SetPageTitle("Attendance");
        //Template::RequireLogin("/staff/reports/show", "Staff Resources", "staff");


        Template::Js("/legacy/staff/js/viewAttendance.js");
        Template::Style(" .reportlink { color:#757575; font-size: 11px; } ");

        $showid = Request::GetInteger('showid');
        if (!$showid) die("missing showid request variable");

        $show = ShowModel::FromId($showid);
        if (!$show) die("the show you are looking for does not exist");
        $djs = array();
        foreach ($show->GetDjModels() as $dj)
            $djs[] = "<p>DJ: <b>{$dj->name}</b> <a class='reportlink' href='/staff/reports/user?userid={$dj->id}'>User #{$dj->id}</a></p>";

        $result = DB::GetAll("SELECT * FROM attendance WHERE showid='$showid' ORDER BY timestamp");
        $tardies = 0;
        $absences = 0;
        $count = 0;
        $atts = array();
        foreach ($result as $row) {
            $count++;
            $timestamp = $row['timestamp'];
            $date = date("l n/j/Y g:ia", $row['timestamp']);
            $status = $row['status'];
            $color = Report::$colors[$status];
            $late = "";
            if ($status == 'present') list($late, $tardies) = self::dispLate($row['late'], $tardies);
            if ($status == 'absent') $absences++;
            $atts[] = "<tr><td>$date</td><td id='displayShow$showid'><b style='color:$color'>$status</b></td><td id='displayLate$showid'>$late</td></tr>";
        }

        Template::SetPageTitle("Attendance for {$show->name}");
        Template::AddBodyContent("
	<h1>Chapman Radio</h1><h2>Show Attendance Report</h2>
	<p>Show: <b>{$show->name}</b> <a class='reportlink' href='/staff/reports/show?showid={$show->id}'>Show #{$show->id}<a/></p>
	" . implode($djs) . "
	<p>Total Tardies: <b style='color:#D60'>$tardies</b></p>
	<p>Total Absent: <b style='color:#A00'>$absences</b></p>
	<h2>Attendance</h2>
	<table cellpadding='3'>" . implode($atts) . "</table>");

        if (!$count) Template::AddBodyContent("<p style='color:#848484'>No data.</p>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());

    }

    function dispLate($late, $tardies = 0)
    {
        $s = $late == 1 ? "" : "s";
        if ($late < 0) return array("<span style='color:#090'>" . (0 - $late) . " minute$s early</span>", $tardies);
        else if ($late == 0) return array("<span style='color:#848484'>on time</span>", $tardies);
        else if ($late < 8) return array("<span style='color:#A60;'>$late minute$s late</span>", $tardies);
        else return array("<span style='color:#A00;'>$late minute$s late</span>", ++$tardies);
    }


    /**
     * @Route("/staff/reports/recording", name="staff_reports_recording")
     */
    public function eventRecording()
    {
        define('PATH', '../');
        Template::SetPageTemplate("report");
        Template::SetPageTitle("Recording Details");
        //Template::RequireLogin("/staff/reports/recording", "Staff Resources", "staff");


        $id = Request::Get('id');

        if (!$id) die("Error: Missing id variable.");


        $rec = DB::GetFirst("SELECT * FROM mp3s WHERE mp3id = :id", array(":id" => $id));
        if ($rec == null)
            Template::AddBodyContent("That recording record id does not exist");
        else
            Template::AddBodyContent("That recording record is for show #{$rec['showid']} attendance on <strong>{$rec['recordedon']}</strong>");


        Template::AddBodyContent("<br style='clear:both;' />");
        Template::AddBodyContent("<pre>Attendance Record: " . print_r($rec, true) . "</pre>");

        $show = DB::GetFirst("SELECT * FROM shows WHERE showid = :id", array(":id" => $rec['showid']));
        Template::AddBodyContent("<pre>Show: " . print_r($show, true) . "</pre>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }

    /**
     * @Route("/staff/reports/instance", name="staff_reports_instance")
     */
    public function eventInstance()
    {
        define('PATH', '../');

        Template::SetPageTemplate("report");
        Template::SetPageTitle("Attendance");
        //Template::RequireLogin("Staff Resources", "staff");

        $attid = Request::Get('id');
        if (!$attid) die("Error: Missing id variable.");

        $att = DB::GetFirst("SELECT * FROM attendance WHERE attendanceid = :id", array(":id" => $attid));

        if ($att == null)
            Template::AddBodyContent("That attendance record id does not exist");

        else
            Template::AddBodyContent("That attendance record is for <strong>{$att['type']}</strong> attendance on <strong>" . date("l F jS, Y h:ia", $att['timestamp']) . "</strong>");

        Template::AddBodyContent("<br style='clear:both;' />");


        Template::AddBodyContent("<pre>Attendance Record: " . print_r($att, true) . "</pre>");

        $show = DB::GetFirst("SELECT * FROM shows WHERE showid = :id", array(":id" => $att['showid']));
        Template::AddBodyContent("<pre>Show: " . print_r($show, true) . "</pre>");


        $user = DB::GetFirst("SELECT * FROM users WHERE userid = :id", array(":id" => $att['userid']));
        Template::AddBodyContent("<pre>User: " . print_r($user, true) . "</pre>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }


    /**
     * @Route("/staff/reports/event", name="staff_reports_event")
     */
    public function eventAction()
    {

        Template::SetPageTemplate("report");
        Template::SetPageTitle("Event Attendance Report");
        //Template::RequireLogin("/staff/reports/event", "Staff Resources", "staff");

        Template::IncludeJs("/legacy/staff/js/viewAttendance.js");


        $timestamp = Request::GetInteger('timestamp');
        if (!$timestamp) die("<p>Error: Missing or invalid timestamp variable.</p>");


        Template::Style(" .reportlink { color:#757575; font-size: 11px; } ");


        $totals = array("present" => 0, "absent" => 0, "excused" => 0);

        $djs = array("class" => array(), "club" => array());

        $haveinfo = array();

        $result = DB::GetAll("SELECT * FROM attendance WHERE type='event' AND timestamp='$timestamp' ORDER BY status");

        foreach ($result as $row) {

            $userid = @$row['userid'] or 0;

            if (!$userid) continue;

            if (!isset($haveinfo[$userid])) {

                $temp = DB::GetFirst("SELECT userid, name, djname, classclub FROM users WHERE userid='$userid'");

                $djs[$temp['classclub']]["$temp[name] $temp[userid]"] = $temp;

                $haveinfo[$userid] = array($temp['classclub'], "$temp[name] $temp[userid]");

            }

            list($classclub, $uid) = $haveinfo[$userid];

            $djs[$classclub][$uid]["status"] = $row['status'];

            $totals[$row['status']]++;

        }


        ksort($djs['class']);

        ksort($djs['club']);

        $ccsections = array();

        $colors = array("present" => "#090", "absent" => "#A00", "excused" => "#D60", "" => "");

        foreach ($djs as $classclub => $uids) {

            $urows = array();

            foreach ($uids as $uid => $dj) {

                $userid = $dj['userid'];

                $urows[] = "<tr><td>$dj[name] <a class='reportlink' href='/staff/reports/user?userid={$userid}'>#$userid</a></td><td id='displayUser$userid'><b style='color:" . $colors[$dj['status']] . "'>$dj[status]</b></td></tr>";

            }

            $ccsections[] = "<h3 style='margin-top:20px;'>" . ucfirst($classclub) . "</h3><table>" . implode($urows) . "</table>";

        }


        Template::AddBodyContent("

	<h1>Chapman Radio</h1><h2>Event Attendance Report</h2>

	<p>Date: <b>" . date("l F jS, Y", $timestamp) . "</b></p>

	<p>Total Present: <b style='color:#090'>$totals[present]</b></p>

	<p>Total Absent: <b style='color:#A00'>$totals[absent]</b></p>

	<p>Total Excused: <b style='color:#D60'>$totals[excused]</b></p>

	" . implode($ccsections) . "");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }


    /**
     * @Route("/staff/reports/emails", name="staff_reports_emails")
     */
    public function emailAction()
    {

        Template::SetPageTemplate("report");
        Template::SetPageTitle("Attendance");
        //Template::RequireLogin("/staff/reports/emails", "Staff Resources", "staff");

        $season = Season::current();
        $seasonName = Season::name($season);

        Template::IncludeJs("/staff/js/viewAttendance.js");


        $userid = intval(@$_REQUEST['userid']);

        if (!$userid) die("Error: Missing userid variable.");


        $user = UserModel::FromId($userid);

        if (!$user) die("Error: Invalid userid variable. User ID#$userid does not exist.");


        Template::SetPageTitle("Email History for " . $user->name);

        Template::AddBodyContent("

	<h1>Chapman Radio</h1><h2>User Attendance Report</h2>

	<p>User: <b>" . $user->name . "</b> (User ID #: " . $user->id . ")</p>

	<p>Email: <b>" . $user->email . "</b></p>

	<p>Phone: <b>" . $user->phone . "</b></p>

	<p>Class or Club: <b>" . ucfirst($user->classclub) . "</b></p><table>");


        $notifs = DB::GetAll("SELECT * FROM notifications WHERE `to` LIKE :email", array(":email" => $user->email));


        foreach ($notifs as $notif)

            Template::AddBodyContent("<tr><td>" . date('g:ia F jS', $notif['timestamp']) . "</td><td>" . $notif['subject'] . "</td></tr>");


        Template::AddBodyContent("</table>");


        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());

    }


    /**
     * @Route("/staff/reports/date", name="staff_reports_date")
     */
    public function dateAction()
    {
        define('PATH', '../');

        Template::SetPageTemplate("report");
        Template::SetPageTitle("Attendance");
        //Template::RequireLogin("/staff/reports/date", "Staff Resources", "staff");


        Template::IncludeJs("/staff/js/viewAttendance.js");
        Template::Style(" .reportlink { color:#757575; font-size: 11px; } ");

        $date = Request::Get('date');

        if (!$date) die("Error: Missing date variable.");

        $timestamp = strtotime($date);

        if (!$timestamp) die("Error: Invalid date variable.");

        Template::SetPageTitle("Attendance for " . date("n/j/y", $timestamp));

        $starttimestamp = strtotime(date("Y-m-d 05:00:00", $timestamp));
        $endtimestamp = strtotime("+1 day -1 second", $starttimestamp);

        Template::AddBodyContent("
	<h1>Chapman Radio</h1><h2>" . date("n/j/y", $timestamp) . " Attendance Report</h2>
	<p>From: <b>" . date("g:ia - l, F jS, Y", $starttimestamp) . "</b></p>
	<p>To: <b>" . date("g:ia - l, F jS, Y", $endtimestamp) . "</b></p>");


        Schedule::PreFetch($starttimestamp, $endtimestamp);

        $result = DB::GetAll("SELECT * FROM attendance WHERE timestamp>=$starttimestamp AND timestamp <= $endtimestamp AND type='show' ORDER BY timestamp");

        $count = 0;

        $atts = array();
        $totals = array();
        foreach (Report::$colors as $stat => $color) $totals[$stat] = 0;

        foreach ($result as $row) {

            $showid = $row['showid'];

            if (!$showid) continue;

            $show = ShowModel::FromId($showid);

            if (!$show) continue;

            $count++;

            $date = date("g:ia", $row['timestamp']);

            $status = $row['status'];
            if ($row['late'] > Site::$TardyGrace) $totals["tardy"]++;
            $totals[$status]++;

            $sched = Schedule::ScheduledAt($row['timestamp']);
            $note = ($sched == $showid) ? "" : "<span style='color:red;font-size:11px;'>[Override From #{$sched}]</span>";

            $atts[] = "<tr><td>$date</td><td>{$show->name} (<a class='reportlink' href='/staff/reports/show?showid={$showid}'>Show #{$showid}</a>) {$note}</td><td style='color:" . Report::$colors[$status] . "'>$status</td><td>" . Report::dispLate($row) . "</td></tr>";

        }

        Template::AddBodyContent("<table cellpadding='3'>");
        foreach ($totals as $stat => $total) if ($total != 0) Template::AddBodyContent("<tr><td style='color:" . Report::$colors[$stat] . "'>$total {$stat}</td></tr>");
        Template::AddBodyContent("</table>");

        Template::AddBodyContent("<h2>Attendance</h2><table cellpadding='3'>" . implode($atts) . "</table>");

        if (!$count) Template::AddBodyContent("<p style='color:#848484'>No data.</p>");


        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($this->container));

    }


}