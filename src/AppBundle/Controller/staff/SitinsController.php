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
use ChapmanRadio\Schedule;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SitinsController extends Controller
{
    /**
     * @Route("/staff/sitins", name="staff_sitins")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Staff");
        Template::SetBodyHeading("Site Administration", "Sit-in Requests");
        Template::RequireLogin("/staff/sitins","Staff Resources", "staff");
        Template::TableSorter();

        if(isset($_POST['submit'])){
            $request = DB::GetFirst("SELECT * FROM show_sitins WHERE season = :season AND showid = :id", array(":season" => Season::Current(), ":id" => Request::GetInteger('showid')));

            if(!$request){
                Template::AddBodyContent("<div class='couju-error'>That request no longer exists</div>");
            }

            else if($request['result'] != 0){
                Template::AddBodyContent("<div class='couju-error'>Someone is already assigned to that request</div>");
            }
            else {
                DB::Query("UPDATE show_sitins SET result = :staffid WHERE showid = :show AND season = :season", array(":staffid" => Session::GetCurrentUserId(), ":show" => Request::GetInteger('showid'), ":season" => Season::Current()));
                Template::AddBodyContent("<div class='couju-success'>You are now assigned to this request</div>");
            }
        }

        if(isset($_POST['cancel'])){
            $request = DB::GetFirst("SELECT * FROM show_sitins WHERE season = :season AND showid = :id", array(":season" => Season::Current(), ":id" => Request::GetInteger('showid')));

            if($request){ // && $request['result'] == Session::GetCurrentUserId()){
                DB::Query("UPDATE show_sitins SET result = 0 WHERE showid = :show AND season = :season", array(":show" => Request::GetInteger('showid'), ":season" => Season::Current()));
                Template::AddBodyContent("<div class='couju-success'>You are no longer assigned to this request</div>");
            }
            else if($request){
                Template::AddBodyContent("<div class='couju-error'>You can't be removed from a request you are not assigned to</div>");
            }
            else {
                Template::AddBodyContent("<div class='couju-error'>That request no longer exists</div>");
            }
        }

        $requests = DB::GetAll("SELECT * FROM show_sitins LEFT JOIN users ON result = users.userid LEFT JOIN shows ON show_sitins.showid = shows.showid WHERE show_sitins.season = :season", array(":season" => Season::Current()));

        $user = Session::GetCurrentUser();

        Template::AddBodyContent("<table class='tablesorter' style='width: 100%; text-align: left;'>
	<thead><tr><th>Show Name</th><th>Show Time</th><th>First Show</th><th>Status</th></tr></thead><tbody>");

        foreach($requests as $request){
            $nextshowtext = "Unknown";
            // $nextshow = Schedule::nextShow($request['showid']);

            $now = 1424080833 ;
            $nexttimes = Schedule::GetBroadcastsBetween($request['showid'], $now, strtotime('+2 weeks', $now));

            if(count($nexttimes) > 0) $nextshowtext = date("m/d H:00 (D ga)", $nexttimes[0]);

            if($request['result'] == 0){
                $status = "<form class='form' method='POST'><input type='hidden' name='showid' value='".$request['showid']."' /><input type='submit' name='submit' value='I will sit-in with this show' /></form>";
            }
            else if ($user->id == $request['result']){
                $status = "<span style='color: red; '>".$request['name']." is sitting in</span>";
                if(Session::GetCurrentUserId() == 571) $status .= "<form class='form' method='POST'><input type='hidden' name='showid' value='".$request['showid']."' /><input type='submit' name='cancel' value='Cancel' /></form>";
            }
            else{
                $status = $request['name']." is sitting in";
                if(Session::GetCurrentUserId() == 571) $status .= "<form class='form' method='POST'><input type='hidden' name='showid' value='".$request['showid']."' /><input type='submit' name='cancel' value='Cancel' /></form>";
            }

            Template::AddBodyContent("<tr><td>".$request['showname']."</td><td>".$request['showtime']."</td><td>".$nextshowtext."</td><td>".$status."</td></tr>");
        }

        Template::AddBodyContent("</tbody></table>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}