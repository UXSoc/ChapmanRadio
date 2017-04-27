<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:57 AM
 */

namespace AppBundle\Controller\dj;

use ChapmanRadio\DB;
use function ChapmanRadio\error;
use ChapmanRadio\Evals;
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\Request;
use ChapmanRadio\Schedule;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Stats;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StatsController extends Controller
{

    /**
     * @Route("/dj/stats", name="dj_stats")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');


        Template::SetPageTitle("Stats - DJ Resources");
        Template::RequireLogin("/dj/stats","Listenership Statistics");
        Template::SetBodyHeading("DJ Resources", "Listenership Statistics");

        Template::css("/legacy/css/dl.css");
        Template::js("/legacy/js/jquery.color.js");
        Template::js("/legacy/js/dj-stats.js");
        Template::css("/legacy/css/star.css");

        Template::style(Schedule::styleGenres());
        Template::AddBodyContent("<div style='width:930px;margin:10px auto;text-align:left;'>");

// we'll use this string in a lot of queries
        $fields = "chapmanradio+chapmanradiolowquality";
// process JSON output?
        if (Request::Get('generate') == "jsonstats") Stats::generateJSON();
// what season are we in?
        $season = Season::current();
        $seasonName = Season::name($season);
// let's display the range. before july is spring, after is fall
        preg_match("/(\\d{4})(\\w)/", $season, $matches);
        @list(, $year, $abbr) = $matches;
        if (!$year) error("internal error: invalid season string");
        if ($abbr == "S") {
            $startdatetime = "$year-01-01 00:00:00";
            $enddatetime = "$year-07-31 23:59:00";
        } else {
            $startdatetime = "$year-08-01 00:00:00";
            $enddatetime = "$year-12-31 23:59:00";
        }
// how many shows do we have this season?
        extract(DB::GetFirst("SELECT COUNT(*) as totalShows FROM shows WHERE status='accepted' AND seasons LIKE '%$season%'"));

// let's get stats for the whole range
        extract(DB::GetFirst("SELECT MAX($fields) as seasonPeak, AVG($fields) as seasonAverage FROM stats WHERE `datetime` >='$startdatetime' AND `datetime` <= '$enddatetime' AND showid>0"));
// output the overall stats
        Template::AddBodyContent("<h3>$seasonName Stats</h3><p>Stats for all of $seasonName.</p>");
        Template::AddBodyContent("<table style='width:840px;margin:10px auto;' cellspacing='0'><tr><td><h2>All Shows</h2><br style='clear:both' />");
        Template::AddBodyContent("<dl><dt>Overall Peak</dt><dd>$seasonPeak</dd><dt>Overall Average</dt><dd>$seasonAverage</dd></dl>");
        Template::AddBodyContent("</td>");
// what shows is this user in?
        $shows = Session::GetCurrentUser()->GetShowsInSeason($season, "AND status='accepted'");
        foreach ($shows as $show) {
            $temp = DB::GetFirst("SELECT MAX($fields) as peak, AVG($fields) as average FROM stats WHERE `datetime` >='$startdatetime' AND `datetime` <= '$enddatetime' AND showid='" . $show->id . "'");
            $peak = $average = 0;
            if ($temp) extract($temp);
            if (!$peak) $peak = "<span style='color:#757575'>No data</span>";
            if (!$average) $average = "<span style='color:#757575'>No data</span>";

            $ranking = ($show->ranking) ? "<dt>Ranking</dt><dd>#" . $show->ranking . " of $totalShows Shows</dd>" : "";

            Template::AddBodyContent("<td style='padding-left:20px;'>
		<img src='" . $show->img64 . "' style='float:left;margin:0 8px;' />
		<div class='address'><a>" . $show->name . "</a></div>
		<p class='" . preg_replace("/\\W/", "", $show->genre) . "'>" . $show->genre . "</p>
		<br style='clear:both;' />
		<dl><dl><dt>Overall Peak</dt><dd>$peak</dd><dt>Overall Average</dt><dd>$average</dd>$ranking</dl>");
        }
        Template::AddBodyContent("</tr></table>");
// stats viewer
        Template::AddBodyContent("<h3>Chronologic Breakdown</h3>
<div class='address'>
	<a><input id='viewbymonth' type='radio' onchange='stats.viewby();' name='breakdown' value='day' /> <label for='viewbymonth'>View by Month</label></a>
	<a><input id='viewbyday' type='radio' onchange='stats.viewby();' name='breakdown' value='day' checked='checked' /> <label for='viewbyday'>View by Day</label></a>
</div>
<div style='position:relative;margin-top:10px;'>
	<h2 id='label' style='text-align:center;padding:6px 0;'> &nbsp; </h2>
	<div id='prev' style='position:absolute;left:220px;top:0;'><img src='/img/arrows/left.png' alt='' /></div>
	<div id='next' style='position:absolute;right:220px;top:0;'><img src='/img/arrows/right.png' alt='' /></div>	
</div>
<div id='statsContainer' style='width:930px;margin:10px auto 40px;height:400px;position:relative;'>
	<div id='loading' style='text-align: center; letter-spacing: 2px; font-size: 16px; padding-top: 160px; color: rgb(170, 170, 170);'>Loading...</div>
	<div id='yaxis'></div>
	<div id='xaxis'></div>
	<div id='bars'></div>
</div>
");

        Template::style("
	#prev,#next {cursor:pointer;}
	#yaxis, #xaxis {position:absolute;width:100%;height:100%;}
	#yaxis .label {position:absolute;left:10px;color:#AAA;border-bottom:1px solid #CCC;width:920px; }
	#xaxis .label {position:absolute;bottom:10px;color:#AAA; }
	.bar1 {background:green;}
	.bar2 {background:blue;}
	.bar{position:absolute;bottom:40px;}
	.bar .label {display:none;position:absolute;top:-20px;right:-20px;white-space:nowrap;font-weight:bold;text-shadow:1px 1px 1px #FFF;color:#000;}
	.bar:hover .label {display:block;}
	");
        Template::script("
	if(typeof stats == 'undefined') stats = {};
	stats.prevMonth2 = '" . date("Y-m", strtotime("-2 months")) . "';
	stats.prevMonth = '" . date("Y-m", strtotime("-1 month")) . "';
	stats.curMonth = '" . date("Y-m") . "';
	stats.nextMonth = '" . date("Y-m", strtotime("+1 month")) . "';
	stats.nextMonth2 = '" . date("Y-m", strtotime("+2 months")) . "';
	stats.prevDay2 = '" . date("Y-m-d", strtotime("-2 days")) . "';
	stats.prevDay = '" . date("Y-m-d", strtotime("-1 day")) . "';
	stats.curDay = '" . date("Y-m-d") . "';
	stats.nextDay = '" . date("Y-m-d", strtotime("+1 day")) . "';
	stats.nextDay2 = '" . date("Y-m-d", strtotime("+2 days")) . "';
	stats.self = '$_SERVER[PHP_SELF]';
	");
// view by show

        Template::style(".showbreakdown {margin:10px 20px;border:1px solid #AAA;} .showbreakdown .evenRow { background:#E6E6E6; } .showbreakdown .oddRow{background:#F4F4F4;} .showbreakdown td {padding:2px 10px;}.showbreakdown .panda { white-space:nowrap;}");
        $year = substr($season, 0, 4);
        $startdate = date("Y-m-d H:i:s", strtotime("-1 week"));//substr($season,0,1) == 's' ? "$year-01-01 00:00:00" : "$year-07-01 00:00:00";
        $enddate = date("Y-m-d H:i:s");//substr($season,0,1) == 's' ? "$year-06-30 00:00:00" : "$year-12-31 00:00:00");
        $result = DB::GetAll("SELECT MAX($fields) as `peak`, AVG($fields) as `average`, showid FROM stats WHERE $fields > 0 AND datetime >= '$startdate' GROUP BY showid ORDER BY average DESC,peak DESC");
        Template::AddBodyContent("<h3>Breakdown by Show</h3><p>Top shows in the <b>last 7 days</b>.</p><table class='showbreakdown' cellspacing='0'>");
        $count = 0;
        foreach ($result as $row) {
            $showid = $row['showid'];
            if (!$showid) continue;
            $show = ShowModel::FromId($showid);
            if (!$show) continue;
            if ($show->status == "cancelled") continue;
            $rowclass = ++$count % 2 == 0 ? 'evenRow' : 'oddRow';
            $row['average'] = round($row['average'] * 10) / 10;
            Template::AddBodyContent("<tr class='$rowclass'>
		<td><div class='star'>$count</div></td>
		<td class='panda'>Average: <b>$row[average]</b><br />Peak: <b>$row[peak]</b></td>
		<td><a href='" . $show->permalink . "'><img src='" . $show->img64 . "' /></a></td>
		<td><a href='" . $show->permalink . "'>" . $show->name . "</a><br /><span class='genre'>" . $show->genre . "</span></td>
		<td>" . $show->GetDjNamesCsv() . "</td>
	</tr>");
        }
        Template::AddBodyContent("</table>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize("</div>"));

    }
}