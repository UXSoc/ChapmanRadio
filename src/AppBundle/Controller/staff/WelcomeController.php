<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */
use ChapmanRadio\DB;
use ChapmanRadio\Season;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use ChapmanRadio\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class WelcomeController extends Controller
{
    /**
     * @Route("/staff/welcome", name="staff_welcome")
     */
    public function indexAction(ContainerInterface $container = null)
    {

        Template::SetPageTitle("Staff");
        Template::SetBodyHeading("Site Administration", "Welcome Message");
        Template::RequireLogin("Staff Resources", "staff");
        Template::Bootstrap();

        $users = UserModel::FromResults(DB::GetAll("SELECT * FROM users WHERE seasons LIKE :season", [":season" => "%" . Site::CurrentSeason() . "%"]));
        $season = Season::Name(Site::CurrentSeason());
        foreach ($users as $user) {

            //if($user->id != 571) continue;

            $newreturning = ($user->seasoncount == 1) ? "new" : "returning";
            $newreturning_detail = ($user->seasoncount == 1) ? "(you need to signup for training at <a href='https://chapmanradio.com/training'>chapmanradio.com/training</a>)" : "(you've been here {$user->seasoncount} semesters)";

            $classclub = ($user->classclub == 'class') ? "class" : "club";
            $classclub_detail = ($user->classclub == 'class') ? "you're here for 2 credits and either COM108 or COM308 is on your university class schedule" : "you're just here for fun";

            if ($classclub == 'class') $meeting_detail = "need to be at <u>all</u> of the weekly meetings";
            else if ($newreturning == 'new') $meeting_detail = "need to be at <u>all</u> of the weekly meetings";
            else $meeting_detail = "only need to be at <u>some</u> of the weekly meetings. <br />See <a href='https://chapmanradio.com/calendar'>chapmanradio.com/calendar</a> for details.";

            $apps = ShowModel::FromResults(DB::GetAll("SELECT * FROM shows WHERE (userid1=:id OR userid2=:id OR userid3=:id OR userid4=:id OR userid5=:id) AND seasons LIKE :likeseason AND (status = 'accepted')", [":id" => $user->id, ":likeseason" => '%' . Site::CurrentSeason() . '%']));

            $no_app_class_warn = ($user->classclub == 'club') ? "" : "<b>Remember, to pass the class you are required to have a show with us!</b><br />";

            $msg = "<h3>Hey {$user->fname}! Welcome to Chapman Radio for {$season}</h3>
		<p>We just want to take a moment to check that we have everything correct:</p>
		<ul>
			<li>You are a <b>{$newreturning}</b> DJ {$newreturning_detail}</li>
			<li>You are in the <b>{$classclub}</b>, {$classclub_detail}</li>
			<li>That means you {$meeting_detail}</li>
		</ul>
		<p>Here are all the show applications we received from you:</p>
		<ul>";
            foreach ($apps as $app) $msg .= "<li><em>{$app->name}</em> with {$app->GetDjNamesCsv()}</li>";
            if (count($apps) == 0) $msg .= "<li style='color: red'>No applications received!<br />$no_app_class_warn</li>";
            $msg .= "
		</ul>
		<p>Please check the schedule at <a href='https://chapmanradio.com/schedule'>chapmanradio.com/schedule</a> right now to verify your assigned slot(s)<br />
		<br />
		If you are a new show (with a biweekly slot), be sure to check both \"This Week\" and \"Next Week\" to see when your first show will be. We will start broadcasting <strong>Monday September 14th</strong> using the \"Next Week\" schedule (check the dates to be sure)<br />
		<br />
		If anything looks wrong, or you need to change something, please email webmaster@chapmanradio.com ASAP</p>
		<p> - Chapman Radio Staff</p>
	";

            Template::Add($msg);

            // mail
            if (isset($_REQUEST['sendmail'])) {
                //notify::mail($user->email, "Welcome to Chapman Radio", $msg);
                //Template::Add("Mail sent to {$user->email}! <hr>");
            }

            Template::Add("<hr>");
        }
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());

    }
}