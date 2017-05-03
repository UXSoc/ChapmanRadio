<?php
namespace AppBundle\Controller\dj;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:57 AM
 */

use ChapmanRadio\DB;
use ChapmanRadio\Evals;
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\Request;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use ChapmanRadio\TrainingSignupModel;
use ChapmanRadio\TrainingSlotModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TrainingController extends Controller
{

    /**
     * @Route("/dj/training", name="dj_training")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("DJ Training Signups");
        //Template::RequireLogin("/dj/training","DJ Training Signups");

        $user = Session::GetCurrentUser();

        $myslot = TrainingSignupModel::WhereFirst("trainingsignup_userid = :uid AND trainingslot_season = :season", [":season" => Site::CurrentSeason(), 'uid' => $user->id]);

        Template::AddBodyContent("<div class='couju-info'>All <b>New</b> DJs are required to attend a training session before they can broadcast.</div>");

        if (isset($_POST['request'])) {
            if (!$myslot) {
                $target = TrainingSlotModel::FromId(Request::GetInteger('slotid'));
                if ($target->count == $target->max) {
                    Template::AddCoujuError("Sorry, that spot is already filled up");
                } else {
                    DB::Insert("training_signups", [
                        'trainingsignup_slot' => $target->id,
                        'trainingsignup_userid' => $user->id
                    ]);
                    Template::AddCoujuSuccess("A training reservation has been submitted");
                }
            } else {
                Template::AddCoujuError("You already have a training reservation");
            }
        } else if (isset($_POST['cancel'])) {
            $request = DB::GetFirst("SELECT * FROM show_sitins WHERE season = :season AND showid = :id AND result = 0", array(":season" => Season::Current(), ":id" => Request::GetInteger('showid')));

            if ($request) {
                DB::Query("DELETE FROM show_sitins WHERE showid = :show AND season = :season", array(":show" => Request::GetInteger('showid'), ":season" => Season::Current()));
                Template::AddBodyContent("<div class='couju-success'>A pending request has been cancelled</div>");
            } else {
                Template::AddBodyContent("<div class='couju-error'>There is no pending request for this show</div>");
            }
        } else if ($myslot != NULL) {
            Template::AddCoujuDebug("You are already registered for training! You're signed up for <b>" . date("l F jS @ g:i a", strtotime($myslot->datetime)) . "</b><br /> If you need to change this, email webmaster@chapmanradio.com ASAP");
        } else {

            $slots = TrainingSlotModel::Where("trainingslot_season = :season ORDER BY trainingslot_datetime", [":season" => Site::CurrentSeason()]);

            if (count($slots) == 0) Template::AddBodyContent("<div class='couju-debug'>No training slots are available - check back soon!</div>");

            $lastdate = NULL;


            foreach ($slots as $slot) {

                $stamp = strtotime($slot->datetime);

                if (date('d', $stamp) != $lastdate) {
                    // header
                    Template::AddBodyContent("<h2>" . date('l F jS', $stamp) . "</h2>");
                    $lastdate = date('d', $stamp);
                }

                // check if current user has a slot
                $request = NULL;

                Template::AddBodyContent("

			<div style=' margin: 10px auto; text-align: left; width: 400px;'>
				<form class='form' method='POST'>
					<span style='font-size: 16px; padding: 5px; font-weight: bold;'>" . date('g:i a', $stamp) . "</span> 
					<span style='font-size: 13x; padding: 5px;'>" . $slot->count . " out of " . $slot->max . " taken</span>
					<input type='hidden' name='slotid' value='" . $slot->id . "' />" .
                    (($stamp < time()) ?
                        "Too late :(" :
                        (($slot->max !== $slot->count) ? "<input type='submit' name='request' value='I will go to this training session' />" : "Full up!")) . "
				</form>
				" . (($user->id == 571) ? $slot->rawdata['name'] : "") . "
			</div>");

            }
        }

        return Template::Finalize($this->container);
    }
}