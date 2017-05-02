<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:57 AM
 */

namespace AppBundle\Controller\dj;

use ChapmanRadio\DB;
use ChapmanRadio\Evals;
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\Schedule;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SitinsController extends Controller
{

    /**
     * @Route("/dj/sitins", name="dj_sitins")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Staff Sit-ins");
        //Template::RequireLogin("/dj/sitins","Staff Sit-ins");

        if (isset($_POST['request'])) {
            $request = DB::GetFirst("SELECT * FROM show_sitins WHERE season = :season AND showid = :id", array(":season" => Season::Current(), ":id" => Request::GetInteger('showid')));

            if (!$request) {
                DB::Query("INSERT INTO show_sitins VALUES(:show, :season, 0)", array(":show" => Request::GetInteger('showid'), ":season" => Season::Current()));
                Template::AddBodyContent("<div class='couju-success'>A request has been submitted</div>");
            } else {
                Template::AddBodyContent("<div class='couju-error'>A request has already been submitted for this show</div>");
            }
        }

        if (isset($_POST['cancel'])) {
            $request = DB::GetFirst("SELECT * FROM show_sitins WHERE season = :season AND showid = :id AND result = 0", array(":season" => Season::Current(), ":id" => Request::GetInteger('showid')));

            if ($request) {
                DB::Query("DELETE FROM show_sitins WHERE showid = :show AND season = :season", array(":show" => Request::GetInteger('showid'), ":season" => Season::Current()));
                Template::AddBodyContent("<div class='couju-success'>A pending request has been cancelled</div>");
            } else {
                Template::AddBodyContent("<div class='couju-error'>There is no pending request for this show</div>");
            }
        }

        $user = Session::GetCurrentUser();


        $shows = $user->GetShowsInSeason(0, "AND (status='finalized' OR status='accepted')");


        if (count($shows) == 0) Template::AddBodyContent("<div class='couju-debug'>You have no current shows eligible for a staff sit-in. Please contact webmaster@chapmanradio.com if this is not correct.</div>");

        else Template::AddBodyContent("<div class='couju-info'>

If you are a new show, you can request a <b>Staff sit-in</b>, where you are asking for a staff member to come to the station during your first scheduled show and make sure that you are able to broadcast and don't have any questions about the equipment.<br /><br />

These are strictly <strong><em>requests</em></strong>. We may not have any staff members available that can come to your show, but we will do our best.<br /><br />

If a staff member commits to sitting in on your show, their name will appear on this page, so check back as your first show gets closer. <br />Email webmaster@chapmanradio.com with any questions.</div>");


        foreach ($shows as $show) {


            $nextshowtext = "Not Scheduled";

            // $nextshow = Schedule::nextShow($request['showid']);

            $now = 1424080833;
            $nexttimes = Schedule::GetBroadcastsBetween($show->id, $now, strtotime('+2 weeks', $now));

            if (count($nexttimes) > 0) $nextshowtext = date("D M j ga", $nexttimes[0]);


            $request = DB::GetFirst("SELECT * FROM show_sitins LEFT JOIN users ON result = users.userid WHERE show_sitins.season = :season AND show_sitins.showid = :id", array(":season" => Season::Current(), ":id" => $show->id));

            Template::AddBodyContent("

		<div style='display: inline-block; vertical-align: top; width: 192px; margin: 10px;'>
			<form class='form' method='POST'>
				<div style='font-size: 16px; padding: 5px; font-weight: bold;'>" . $show->name . "</div>
				<img alt='" . $show->name . "' src='" . $show->img192 . "' />
				<input type='hidden' name='showid' value='" . $show->id . "' />" .
                (($request == NULL) ? "<input type='submit' name='request' value='Request a Staff Sit-in' />" :
                    (($request['result'] == 0) ? "<input type='submit' name='cancel' value='Cancel Request' />" : "Staff member " . $request['name'] . " will be at your show")) . "
			</form>

			<br />First Show: $nextshowtext

		</div>");

        }
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($this->container));
    }
}