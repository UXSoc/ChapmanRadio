<?php
namespace AppBundle\Controller\Staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use ChapmanRadio\Request;
use ChapmanRadio\Schedule;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class NowController extends Controller
{
    /**
     * @Route("/staff/now", name="staff_now")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Staff");
        Template::SetBodyHeading("Site Administration", "Site Status");
        Template::RequireLogin("/staff/now","Staff Resources", "staff");
        Template::Bootstrap();

        $listeners = DB::GetFirst("SELECT chapmanradio,chapmanradiolowquality,datetime FROM stats ORDER BY datetime DESC LIMIT 0,1");
        Template::Add("<h3>Listeners</h3>");
        Template::Add("Hi quality: <b>".($listeners['chapmanradio'])."</b><br />");
        Template::Add("Lo quality: <b>".($listeners['chapmanradiolowquality'])."</b><br />");
        Template::Add("<i>as of ".date('g:ia F jS',strtotime($listeners['datetime']) )."</i><br />");
        Template::Add("<br />");

        $timestamp = Request::Get('timestamp', time());
        Template::Add("<h3>Schedule</h3>");
        Template::Add("HappeningNow: #".Schedule::HappenedAt($timestamp)."<br />");
        Template::Add("ShouldHappenNow: #".Schedule::ShouldHappenAt($timestamp)."<br />");
        Template::Add("ScheduledNow: #".Schedule::ScheduledAt($timestamp)."<br />");
        Template::Add("<br />");
        Template::Add("<h3>Stats</h3>");

        $users = DB::GetFirst("SELECT count(*) as count FROM users WHERE seasons LIKE :season", array(":season" => "%".Site::CurrentSeason()."%"));
        Template::Add("Activated users: ".$users['count']."<br />");

        $usershows = DB::GetFirst("SELECT count(*) as count FROM users JOIN shows ON users.userid = shows.userid1 OR users.userid = shows.userid2 OR users.userid = shows.userid3 OR users.userid = shows.userid4 OR users.userid = shows.userid5 WHERE shows.seasons LIKE :season", array(":season" => "%".Site::CurrentSeason()."%"));
        Template::Add("Users on activated show: ".$usershows['count']."<br />");

        $showusers = DB::GetFirst("SELECT count(*) as count FROM users JOIN shows ON users.userid = shows.userid1 OR users.userid = shows.userid2 OR users.userid = shows.userid3 OR users.userid = shows.userid4 OR users.userid = shows.userid5 WHERE users.seasons LIKE :season", array(":season" => "%".Site::CurrentSeason()."%"));
        Template::Add("Shows with activated users: ".$showusers['count']."<br />");

        $usershowusers = DB::GetFirst("SELECT count(*) as count FROM users INNER JOIN shows ON (users.userid = shows.userid1 OR users.userid = shows.userid2 OR users.userid = shows.userid3 OR users.userid = shows.userid4 OR users.userid = shows.userid5) WHERE shows.seasons LIKE :season AND users.seasons LIKE :season", array(":season" => "%".Site::CurrentSeason()."%"));
        Template::Add("Activated Users with activated shows: ".$usershowusers['count']."<br />");
        Template::Add("<br />");

        $allshows = ShowModel::FromResults(DB::GetAll("SELECT * FROM shows WHERE seasons LIKE :season", array(":season" => "%".Site::CurrentSeason()."%")));
        $newshowcount = 0; $returningshowcount = 0;

        foreach($allshows as $s){
            if($s->seasoncount <= 1) $newshowcount++;
            else $returningshowcount++;
        }

        Template::Add("New shows: ".$newshowcount."<br />");
        Template::Add("Returning shows: ".$returningshowcount."<br />");

        $shows = DB::GetFirst("SELECT count(*) as count FROM shows WHERE seasons LIKE :season", array(":season" => "%".Site::CurrentSeason()."%"));
        Template::Add("Activated shows: ".$shows['count']."<br />");

        $acceptedshows = DB::GetFirst("SELECT count(*) as count FROM shows WHERE seasons LIKE :season AND status = 'accepted'", array(":season" => "%".Site::CurrentSeason()."%"));
        Template::Add("Accepted shows: ".$acceptedshows['count']."<br />");

        $cancelledshows = DB::GetFirst("SELECT count(*) as count FROM shows WHERE seasons LIKE :season AND status = 'cancelled'", array(":season" => "%".Site::CurrentSeason()."%"));
        Template::Add("Cancelled shows: ".$cancelledshows['count']."<br />");

        $scheduledshows = Schedule::GetAllShowIds();
        Template::Add("Scheduled shows: ".count($scheduledshows)."<br />");
        Template::Add("<br />");
        Template::Add("<h3>Problems</h3>");
        ShowModel::PreFetch($scheduledshows);
        Template::Add("<table class='eros' style='width: 100%; text-align: left;'><thead><tr><td>Scheduled Show</td><td>Problem</td></tr></thead>");
        foreach($scheduledshows as $show){
            $show = ShowModel::FromId($show);
            if($show->status != 'accepted') Template::Add("<tr><td>".$show->name."</td><td>Status = ".$show->status."</td></tr>");
        }
        Template::Add("</table><br />");

        $inactiveusers = DB::GetAll("SELECT * FROM users JOIN shows ON users.userid = shows.userid1 OR users.userid = shows.userid2 OR users.userid = shows.userid3 OR users.userid = shows.userid4 OR users.userid = shows.userid5 WHERE shows.seasons LIKE :season AND users.seasons NOT LIKE :season", array(":season" => "%".Site::CurrentSeason()."%"));
        Template::Add("<table class='eros' style='width: 100%; text-align: left;'><thead><tr><td>Inactive User</td><td>Active Show</td></tr></thead>");
        foreach($inactiveusers as $inactiveuser) Template::Add("<tr><td>".$inactiveuser['name']."</td><td>".$inactiveuser['showname']."</td></tr>");
        Template::Add("</table><br />");
        self::DispEmailBox($inactiveusers);

        $activeusers = DB::GetAll("SELECT * FROM users LEFT JOIN shows ON (users.userid = shows.userid1 OR users.userid = shows.userid2 OR users.userid = shows.userid3 OR users.userid = shows.userid4 OR users.userid = shows.userid5) AND shows.seasons LIKE :season WHERE shows.showid IS NULL AND users.seasons LIKE :season", array(":season" => "%".Site::CurrentSeason()."%"));
        Template::Add("<table class='eros' style='width: 100%; text-align: left;'><thead><tr><td>Active User</td><td>Class/Club</td><td>No Active Show</td></tr></thead>");
        foreach($activeusers as $activeuser) Template::Add("<tr><td>".$activeuser['name']."</td><td>".$activeuser['classclub']."</td><td> </td></tr>");
        Template::Add("</table><br />");
        self::DispEmailBox($activeusers);

        $userswithincompleteshows = DB::GetAll("SELECT * FROM users INNER JOIN shows ON ((shows.seasons LIKE :season AND shows.status = 'incomplete') AND (userid = userid1 OR userid = userid2 OR userid = userid3 OR userid = userid4 OR userid = userid5))", array(":season" => "%".Site::CurrentSeason()."%"));
        Template::Add("<table class='eros' style='width: 100%; text-align: left;'><thead><tr><td>Incomplete Show</td><td>User</td></tr></thead>");
        foreach($userswithincompleteshows as $userwithincompleteshow) Template::Add("<tr><td>".$userwithincompleteshow['showname']."</td><td>".$userwithincompleteshow['name']."</td></tr>");
        Template::Add("</table><br />");
        self::DispEmailBox($userswithincompleteshows);

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }

    function DispEmailBox($users){
        Template::AddBodyContent("<textarea rows='14' cols='84'>");
        foreach($users as $row) if($row['email']) Template::Add("\"".str_replace("\"","",$row['name'])."\" &lt;".$row['email']."&gt;\n");
        Template::Add("</textarea>");
    }
}