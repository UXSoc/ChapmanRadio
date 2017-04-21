<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:08 AM
 */

namespace AppBundle\Controller\dj;


use ChapmanRadio\DB;
use ChapmanRadio\Report;
use ChapmanRadio\Schedule;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Site;
use ChapmanRadio\Strikes;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AttendanceController extends Controller
{

    /**
     * @Route("/dj/attendence", name="dj_apply")
     */
    public function indexAction(ContainerInterface $container = null)
    {

        Template::SetPageTitle("My Attendance");
        Template::RequireLogin("DJ Account");

        $season = Site::CurrentSeason();
        $seasonName = Season::name($season);

        Template::style(Schedule::styleGenres());
        Template::css("/css/formtable.css");
        Template::css("/css/dl.css");

// get the user info
        $userid = Session::GetCurrentUserID();
        $user = Session::GetCurrentUser();

// start the output

// strikes
        Template::SetBodyHeading("DJ Resources", "My Attendance");
        Template::AddBodyContent("<div style='width:750px;margin:20px auto;text-align:left;'>
	<h3>Strikes</h3>
	<p>If you accumulate 3 strikes, even from multiple shows, all of your shows will be cancelled.</p>
	<p>For details, see <a href='/policies'>chapmanradio.com/policies</a> or email the <a href='/contact'>Attendance Manager</a> for help.</p>");
        Template::AddBodyContent(Strikes::Overview($user->id));

        Template::AddBodyContent("<h3>Show Attendance</h3><br />");
        $shows = $user->GetShowsInSeason(0, "AND status='accepted'");

        if (empty($shows)) Template::AddBodyContent("<div style='width:420px;margin:10px auto;padding:20px;background:#F4F4F4;border:1px solid #AAA;'>No Results.<br />It doesn't appear that you aren't a part of any shows in $seasonName.</div>");

        foreach ($shows as $show) {
            Template::AddBodyContent("<table class='formtable' cellspacing='0'>
		<tr class='oddRow'><td colspan='3'>
			<img src='" . $show->img50 . "' alt='' style='float:left;margin:5px 12px 0 17px;'/>
			<div class='address'><a>" . $show->name . "</a></div>
			<div class='" . preg_replace("/\\W/", "", $show->genre) . "' style='margin:0 50px 0 0;'>" . $show->genre . "</div>
		<br style='clear:both;' /></td></tr>");
            $count = 1;
            $result2 = DB::GetAll("SELECT * FROM attendance WHERE showid='" . $show->id . "' AND type='show' AND season='$season' ORDER BY timestamp");
            $tardies = 0;
            $absences = 0;
            foreach ($result2 as $att) {
                $rowclass = ++$count % 2 == 0 ? "evenRow" : "oddRow";
                $date = date("ga, F jS", $att['timestamp']);
                $color = Report::$colors[$att['status']];
                list($late, $tardies) = self::dispLate($att['late'], $tardies);
                if ($att['status'] != "present") $late = "&nbsp;";
                if ($att['status'] == "absent") $absences++;
                Template::AddBodyContent("<tr class='$rowclass'><td style='width:33%'>$date</td><td style='color:$color;width:33%;'>" . ucfirst($att['status']) . "</td><td>$late</td></tr>");
            }
            $rowclass = ++$count % 2 == 0 ? "evenRow" : "oddRow";
            $tardytardies = $tardies == 1 ? "tardy" : "tardies";
            $s = $absences == 1 ? "" : "s";

            Template::AddBodyContent("<tr class='$rowclass'><td colspan='3' style='text-align:center;color:#333'>You have <b>$tardies $tardytardies</b> and <b>$absences absence$s</b> to your show.</td></tr></table>");
        }

// workshop attendance
        Template::AddBodyContent("<h3>Workshop Attendance</h3><br />");
        $result = DB::GetAll("SELECT * FROM attendance WHERE userid='$userid' AND type='workshop' AND season='$season'");

        if (empty($result))
            Template::AddBodyContent("<div style='width:420px;margin:10px auto;padding:20px;background:#F4F4F4;border:1px solid #AAA;'>No Results.<br />We don't have any records of your presence or absence at any weekly workshop meetings so far this season.</div>");
        else {
            Template::AddBodyContent("<table class='formtable' cellspacing='0'>");
            $count = 0;
            foreach ($result as $row) {
                $rowclass = ++$count % 2 == 0 ? "evenRow" : "oddRow";
                $color = Report::$colors[$row['status']];
                $date = date("l, F jS, Y", $row['timestamp']);
                Template::AddBodyContent("<tr class='$rowclass'><td>$date</td><td style='color:$color'>" . ucfirst($row['status']) . "</td>");
            }
            Template::AddBodyContent("</table>");
        }

// event attendance
        Template::AddBodyContent("<h3>Event Attendance</h3><br />");
        $result = DB::GetAll("SELECT * FROM attendance WHERE userid='$userid' AND type='event'");
        if (empty($result))
            Template::AddBodyContent("<div style='width:420px;margin:10px auto;padding:20px;background:#F4F4F4;border:1px solid #AAA;'>No Results.<br />We don't have any records of your presence or absence at any special events this season.</div>");
        else {
            Template::AddBodyContent("<table class='formtable' cellspacing='0'>");
            $count = 0;
            foreach ($result as $row) {
                $rowclass = ++$count % 2 == 0 ? "evenRow" : "oddRow";
                $color = Report::$colors[$row['status']];
                $date = date("l, F jS, Y", $row['timestamp']);
                Template::AddBodyContent("<tr class='$rowclass'><td>$date</td><td style='color:$color'>" . ucfirst($row['status']) . "</td>");
            }
            Template::AddBodyContent("</table>");
        }

// little notice
        Template::AddBodyContent("<p style='margin:40px auto;font-size:12px;color:#757575;'>If you have questions about your attendance, please email <a href='mailto:attendance@chapmanradio.com'>attendance@chapmanradio.com</a></p>");
// finish output
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize("</div>"));
    }

    function notify($msg, $color = "#090")
    {
        return "<div class='gloss' style='border:1px solid $color;color:$color;text-align:center;'>$msg</div>";
    }

    function append($msg)
    {
        return "<br /><small style='color:#757575'>$msg</small>";
    }

    function dispLate($late, $tardies = 0)
    {
        $s = $late == 1 ? "" : "s";
        if ($late < 0) return array("<span style='color:#090'>" . (0 - $late) . " minute$s early</span>", $tardies);
        else if ($late == 0) return array("on time", $tardies);
        else if ($late < 8) return array("<span style='color:#A60;'>$late minute$s late</span>", $tardies);
        else return array("<span style='color:#A00;'>$late minute$s late</span>", ++$tardies);
    }
}