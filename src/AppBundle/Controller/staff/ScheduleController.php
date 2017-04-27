<?php

namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use ChapmanRadio\Log;
use ChapmanRadio\Request;
use ChapmanRadio\Schedule;
use ChapmanRadio\Season;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use ChapmanRadio\Util;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ScheduleController extends Controller
{

    /**
     * @Route("/staff/schedule", name="staff_schedule")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Schedule Editor");
        Template::SetBodyHeading("Site Administration", "Schedule Editor");
        Template::RequireLogin("Staff Resources", "staff");

        Template::shadowbox();
        Template::css("/legacy/css/dl.css");
        Template::js("/legacy/staff/js/schedule.js?3");
        Template::css("/legacy/css/formtable.css");
        Template::js("/legacy/js/jquery.watermark.min.js");
        Template::css("/legacy/css/admin-schedule.css");
        Template::css("/legacy/css/timeslots.css?3");

        $season = Site::ScheduleSeason(true);
        $seasonName = Season::name($season);

        $starthour = 5;
        $endhour = 28;

// process AJAX
        if (Request::Get('action') == 'saveSchedulePlacement') {

            // what show to place
            $showid = Request::GetInteger('showid');

            // when
            $hour = Request::GetInteger('hour');
            $day = Request::Get('day');
            $season = Request::Get('season');

            // what week (1 = week 1, 2 = week 2, 3 = both)
            $biweekly = Request::Get('biweekly');

            // get current slot
            $sched = DB::GetFirst("SELECT `$day` FROM schedule WHERE hour='$hour' AND season='$season'");

            if (!$sched) die(json_encode(array("error" => "season entry does not exist for hour $hour, day $day, season $season")));
            $showid1 = $showid2 = $oldshowid1 = $oldshowid2 = 0;
            $parts = explode(",", $sched[$day]);
            $showid1 = $oldshowid1 = @$parts[0] or 0;
            $showid2 = $oldshowid2 = @$parts[1] or 0;

            // new slot values
            if ($biweekly == 1 || $biweekly == 3) $showid1 = $showid;
            if ($biweekly == 2 || $biweekly == 3) $showid2 = $showid;

            Log::StaffEvent("Changed $season schedule: shows $showid1 and $showid2 now broadcast $day at $hour");

            // push changes to schedule
            DB::Query("UPDATE schedule SET `$day`='$showid1,$showid2' WHERE hour='$hour' AND season='$season'");
            if (DB::AffectedRows() == 0) die(json_encode(array("error" => "No changes were made.")));

            // push changes to each show
            foreach (array($showid, $showid1, $showid2, $oldshowid1, $oldshowid2) as $up_showid) {
                if ($up_showid) Schedule::HandleChange($up_showid, $season);
            }

            if ($showid) $show = DB::GetFirst("SELECT showtime,status FROM shows WHERE showid='$showid'");
            else $show = array("status" => "", "showtime" => "");
            die(json_encode(array("success" => true, "status" => $show['status'], "showtime" => $show['showtime'], "showid" => $showid)));
        }

        Template::AddBodyContent("<p>It is currently <b>cycle " . Schedule::cycle() . "</b> in a week of " . Season::name(Site::ScheduleSeason()) . ".</p>
	<form method='get' action='$_SERVER[PHP_SELF]'>
	<p style='margin:20px auto;'>Edit the schedule for " . Season::picker(2011, true, $season) . "<input type='submit' value=' &gt; ' /></p>
	</form>");

        Template::Add("New! Generate <a href='/staff/tools/scheduleppt?season=$season'>Reveal Powerpoint</a>");

        Template::AddBodyContent("<div id='showsPanel' class='panel'>
<div class='navbar'><span class='noHover'>Shows</span></div>
<div class='address'>
	<div style='float:right'>
	Status: 
	<input type='radio' name='status' id='incomplete' onchange='s.draw()' /> <label for='incomplete'>Incomplete</label>
	<input type='radio' name='status' id='finalized' onchange='s.draw()' checked='checked' /> <label for='finalized'>Finalized</label>
	<input type='radio' name='status' id='accepted' onchange='s.draw()' /> <label for='accepted'>Accepted</label>
	</div>
	<div style='padding:2px 0 0 10px;float:left;'><input type='text' id='search' autocomplete='off' onkeyup='s.search()' /></div>
</div>
<div class='address'>
	<div id='numresults' style='text-align: left; color:#757575; float: left; font-size: 11px; padding: 4px 36px;'></div>
	<div style='float:right'>
		<select id='type' onchange='s.draw();'><option value=''>Both Staff and DJs</option><option value='staff'>Staff Only</option><option value='dj'>DJs only</option></select>
		<select id='newreturning' onchange='s.draw();'><option value=''>Both New &amp; Returning</option><option value='new'>New Only</option><option value='returning'>Returning only</option></select>
		<select id='genre' onchange='s.draw();'><option value='' style='color:#757575'>All Genres</option>");

        $genres = Site::$Genres;
        foreach ($genres as $genre) {
            $genre = trim($genre);
            if (!$genre) continue;
            Template::AddBodyContent("<option value='$genre'>$genre</option>");
        }

        Template::AddBodyContent("</select>
	</div>
</div>
<script>\$('#search').watermark('Search...');</script>
<div id='shows'>
<p style='padding:40px;text-align:center;color:#757575;font-style:italic'>Loading...</p>
</div>
</div>");

        Template::style(Schedule::styleGenres());

        $script = "if(typeof s == 'undefined') s = new Object(); s.data = [];";
        $reducedSeason = Season::reduce($season);
        $result = DB::GetAll("SELECT * FROM shows WHERE (shows.seasons LIKE '%$season%' OR shows.seasons LIKE '%$reducedSeason%' OR shows.status='pending')");
        foreach ($result as $row) {
            $show = new ShowModel($row);
            // showinfo
            $data = array(
                "showname" => $show->name,
                "turntables" => $show->turntables,
                "genre" => $show->genre,
                "full" => $show->img310,
                "pic" => $show->img192,
                "icon" => $show->img50,
                "djs" => array(),
                "status" => $show->status,
                "seasons" => $show->seasons,
                "seasoncount" => count($show->seasons) - 1,
                "musictalk" => $show->musictalk,
                "explicit" => $show->explicit,
                "availabilitynotes" => $show->availabilitynotes,
                "description" => $show->description,
                "showtime" => $show->time,
                "app_differentiate" => $show->app_differentiate,
                "app_promote" => $show->app_promote,
                "app_timeline" => $show->app_timeline,
                "app_giveaway" => $show->app_giveaway,
                "app_speaking" => $show->app_speaking,
                "app_equipment" => $show->app_equipment,
                "app_prepare" => $show->app_prepare,
                "app_examples" => $show->app_examples,
            );

            // djs
            foreach ($show->GetDjModels() as $dj) $data["djs"][$dj->id] = $dj;

            // availability
            $table = array();
            $total = 0;
            for ($row = $starthour; $row <= $endhour; $row++) {
                $table[$row] = array();
                for ($col = 1; $col <= 7; $col++) {
                    $table[$row][$col] = 0;
                    $total++;
                }
            }

            $availability = explode(",", $show->availability);
            $count = 0;
            foreach ($availability as $bit) {
                $boops = explode("-", $bit);
                if (count($boops) == 2) {
                    list($x, $y) = $boops;
                    $table[$x][$y] = 1;
                    $count++;
                }
            }

            $data["table"] = $table;
            $data["unavailability"] = round(1000 * ($count / $total)) / 10 . "%";
            // commit to script
            $script .= "s.data[" . $show->id . "] = " . json_encode($data) . ";\n";
        }

        Template::script($script);

// give javascript season info
        Template::script("s.season='$season';s.seasonName='$seasonName';s.self='$_SERVER[PHP_SELF]'");

// selected panel
        Template::AddBodyContent("
<div id='selectedPanel' class='panel' style='text-align:left;display:none;'>
<div class='navbar'><span class='noHover'>Selected Show</span></div>
<div id='selected' style='padding:5px;'></div>
</div>");

// schedule data
        $temp = DB::GetFirst("SELECT sun FROM schedule WHERE hour=12 AND season='$season'");
        if (!$temp) {
            for ($i = $starthour; $i <= $endhour; $i++) {
                DB::Query("INSERT INTO schedule (hour,mon,tue,wed,thu,fri,sat,sun,season) VALUES ($i,',',',',',',',',',',',',',','$season')");
            }
            Template::AddInlineSuccess("The schedule data for <b>$season</b> has been created.");
        }

        $days = array("mon", "tue", "wed", "thu", "fri", "sat", "sun");
        $script = "s.schedule = [];";
        for ($i = $starthour; $i <= $endhour; $i++) {
            $row = DB::GetFirst("SELECT * FROM schedule WHERE hour='$i' AND season='$season' LIMIT 0,1");
            $script .= "\ns.schedule[$i]=[];";
            foreach ($days as $index => $day) {
                $showid1 = $showid2 = 0;
                list($showid1, $showid2) = explode(",", $row[$day]);
                if (!$showid1) $showid1 = 0;
                if (!$showid2) $showid2 = 0;
                $script .= "s.schedule[$i][" . ($index + 1) . "]=[$showid1,$showid2];";
            }
        }
        Template::script($script);
        Template::js("/js/jquery.scrollTo.js");

        Template::AddBodyContent("<div id='schedulePanel' class='panel'>
<div class='navbar'><span class='noHover'>Schedule - $seasonName</span></div>
<div id='schedule'>
<div id='availabilitynotes'></div>
<div class='t_container'>
<div class='t_blank'></div><div class='t_label t_toplabel'>Monday</div><div class='t_label t_toplabel'>Tuesday</div><div class='t_label t_toplabel'>Wednesday</div><div class='t_label t_toplabel'>Thursday</div><div class='t_label t_toplabel'>Friday</div><div class='t_label t_toplabel'>Saturday</div><div class='t_label t_toplabel'>Sunday</div>");

        $table = array();
        $cellHeight = 33;
        $cellWidth = 101;
        $genresDiv = "";
        for ($row = $starthour; $row <= $endhour; $row++) {
            Template::AddBodyContent("<div class='t_row'><div class='t_label'>" . Util::hourName($row) . "</div>");
            $table[$row] = array();
            for ($col = 1; $col <= 7; $col++) {
                $table[$row][$col] = false;
                Template::AddBodyContent("<div class='t_cell' id='t$row-$col'><u class='t_show1'></u><u class='t_show2'></u></div>");
                $genresDiv .= "<div class='t_genre' id='genre$row-$col-0' style='left:" . ($cellWidth * ($col - 1) + 4) . "px;top:" . ($cellHeight * ($row - $starthour) + 4) . "px'></div>";
                $genresDiv .= "<div class='t_genre' id='genre$row-$col-1' style='left:" . ($cellWidth * ($col - 1) + 52) . "px;top:" . ($cellHeight * ($row - $starthour) + 4) . "px'></div>";
            }
            Template::AddBodyContent("</div>");
        }

        Template::AddBodyContent("
	<div id='schedhover'>
		<div class='top'>Keyboard Shortcuts</div>
		<div class='address'><a>Place</a></div>
		<div style='padding:4px 10px 12px;'>
		<code>1</code> <input type='radio' name='biweekly' id='cycle1' onchange='s.updateBiweekly()' value='1' /> <label for='cycle1'>Cycle 1</label>  <br />
		<code>2</code> <input type='radio' name='biweekly' id='cycle2' onchange='s.updateBiweekly()' value='1' /> <label for='cycle2'>Cycle 2</label>  <br /> 
		<code>3</code> <input type='radio' name='biweekly' id='everyweek' onchange='s.updateBiweekly()' value='1' checked='checked' /> <label for='everyweek'>Every Week</label>
		</div>
		<div class='address'><a>Clear</a></div>
		<div style='padding:4px 10px 12px;'><code>F</code> Clear selected show</div>
		<div class='address'><a>View</a></div>
		<div style='padding:4px 10px;'>
		<code>Q</code> <input type='checkbox' id='djavailability' onchange='s.drawAvailability();' checked='checked' /> <label for='selectedavailability'> DJ Availability</label> <br />
		<code>W</code> <input type='checkbox' id='scheduleavailability' checked='checked' onchange='s.drawAvailability();'/> <label for='scheduleavailability'>Schedule Availability</label><br />
		<code>E</code> <input type='checkbox' id='viewgenres' onchange='s.drawGenres();' /> <label for='viewgenres'> Genres</label> <br />
		<code>R</code> <input type='checkbox' id='viewshows' onchange='s.drawSchedule();' /> <label for='viewshows'> Show Icons</label> <br />
		</div>
		<div class='address'><a>Scroll To</a></div>
		<div style='padding:4px 10px 12px;'>
		<code>A</code> Shows <br />
		<code>S</code> Selected Show<br />
		<code>D</code> Schedule
		</div>
	</div>
	<div class='t_div' id='genres'>$genresDiv</div>
	<div class='t_div'><div id='s_holder'></div></div>
	<div class='t_div'><div id='t_highlight1'></div><div id='t_highlight2'></div><div id='tooltip' style='display:none;'></div></div>
	<div id='t_div' draggable='false' ondragstart='return false;' style='-webkit-user-drag:none;'></div>
</div></div>");
        Template::AddBodyContent("</div>");

// schednav
        Template::AddBodyContent("<div id='schednav'>
	<div class='top'>Selected Show</div>
	<div id='schednavShows'></div>
</div>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}