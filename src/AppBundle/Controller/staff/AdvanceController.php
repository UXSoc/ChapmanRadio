<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:08 AM
 */

use ChapmanRadio\DB;
use ChapmanRadio\Evals;
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\Icecast;
use ChapmanRadio\Notify;
use ChapmanRadio\Picker;
use ChapmanRadio\Request;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use ChapmanRadio\Util;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdvanceController extends Controller
{

    /**
     * @Route("/staff/advance", name="staff_advance")
     */
    public function indexAction(ContainerInterface $container = null)
    {

        Template::SetPageTitle("Staff - Advanced");
        Template::SetBodyHeading("Site Administration", "Advanced Settings");
        Template::RequireLogin("Staff Resources", "staff");

        Template::css("/css/formtable.css");

        $season = Season::current();
        $seasonName = Season::name($season);

        Template::AddBodyContent("<div style='width: 600px; margin: 0 auto;'><div style='background:red;color:white;margin:5px auto;padding:5px;width:350px;'>Do not change unless you know what you are doing</div>");

        if (isset($_POST['SAVE_GLOBAL_SETTINGS'])) {

            $broadcasting = Request::GetBool('global_broadcasting');

            $currentseason = Request::Get('global_currentseason', Site::CurrentSeason());

            $scheduleseason = Request::Get('global_scheduleseason', Site::ScheduleSeason());

            $applications = Request::GetBool('global_applications');

            $applicationsseason = Request::Get('global_applicationseason', Site::ApplicationSeason());

            $applicationtimestamp = strtotime(@$_REQUEST['applicationsdeadline']);


            if (!Season::valid($currentseason) || !Season::valid($scheduleseason) || !Season::valid($applicationsseason) || $applicationtimestamp === false || $applicationtimestamp <= 0) {

                Template::AddInlineError("An error occurred with your input and your changes were not saved. Please double check your settings and try again.");

            } else {

                $applicationsdeadline = date("n/j/y g:ia T", $applicationtimestamp);

                Site::Update('broadcasting', $broadcasting);

                Site::Update('currentseason', $currentseason);

                Site::Update('scheduleseason', $scheduleseason);

                Site::Update('applications', $applications);

                Site::Update('applicationsseason', $applicationsseason);

                Site::Update('applicationsdeadline', $applicationsdeadline);


                $icecastserver = Request::Get('icecast_server');

                if ($icecastserver) {

                    if (substr($icecastserver, 0, 4) != "http") $icecastserver = "http://" . $icecastserver;

                    if (substr($icecastserver, -1) != "/") $icecastserver = $icecastserver . "/";

                    Site::Update('icecastserver', $icecastserver);

                }


                Site::Update('icecastusername', Request::Get('icecast_username'));

                Site::Update('icecastpassword', Request::Get('icecast_password'));

                Site::Init();


                Template::AddInlineSuccess("Your changes have been saved.");
            }
        } else if (isset($_POST['EMAIL_CODE'])) {
            $userid = Request::GetInteger('userid');
            if (!$userid) Template::AddInlineError("Missing or Invalid User ID #.<br />Please pick a user from the drop down menu then try again");
            else {
                $user = DB::GetFirst("SELECT fname,name,email FROM users WHERE userid='$userid'");
                if (!$user) Template::AddInlineError("User ID #$userid does not exist.");
                else {
                    $staff = Session::GetCurrentUser();
                    $code = Util::encrypt(Site::ApplicationSeason());
                    $appSeasonName = Season::name(Site::ApplicationSeason());

                    $retval = Notify::mail($user['email'], "$appSeasonName Show Application Deadline", "<h2>Override Deadline</h2><p>Hello, $user[fname]</p><p>A staff member, {$staff->name}, has given you access to override the deadline for a $appSeasonName.</p><p>To apply for a show, use the following link:</p><p style='text-align:center;'><a href='http://chapmanradio.com/dj/apply?override_code=" . urlencode($code) . "'>http://chapmanradio.com/dj/apply?override_code=" . urlencode($code) . "</a></p><p>Please complete the show application as soon as possible.</p>");


                    if ($retval)
                        Template::AddInlineError("The email failed to send to <b>\"$user[name]\" &lt;$user[email]&gt;</b><br />$retval");
                    else
                        Template::AddInlineSuccess("You just emailed the override deadline code to <br /><b>\"$user[name]\" &lt;$user[email]&gt;</b>");
                }
            }
        }


        $streams = Icecast::streams(true);

        if (!$streams) {

            Template::AddInlineError("<b>Invalid Icecast Settings</b><br />Icecast could not be reached. Please contact double check the settings or contact the technical staff to help fix this problem");

        }


        Template::AddBodyContent("
<form class='table' method='post' style='text-align:left'>

	<div style='margin-top: 5px; padding: 5px; border: 1px solid #999;'>

		<h3>Global Site Settings</h3>

		<span>Broadcasting is <b>" . (Site::$Broadcasting ? "enabled" : "disabled") . "</b>.</span>

		<br />

		<input type='checkbox' name='global_broadcasting' " . (Site::$Broadcasting ? "checked='checked'" : "") . " />

		<label for='global_broadcasting'>Enable Broadcasting</label>

		<br />

		<span>Current Season</span>

		<select name='global_currentseason' class='float-right'>

		" . Season::picker(2011, false, Site::CurrentSeason(), true) . "

		</select>

		<br />

		<span>Schedule Season</span>

		<select name='global_scheduleseason' class='float-right'>

		" . Season::picker(2011, true, Site::ScheduleSeason(), true) . "

		</select>

	</div>

	<div style='margin-top: 5px; padding: 5px; border: 1px solid #999;'>

		<h3>Show Applications</h3>

		<span>Applications are <b>" . (Site::$Applications ? "enabled" : "disabled") . "</b>.</span>

		<br />

		<input type='checkbox' id='global_applications' name='global_applications' " . (Site::$Applications ? "checked='checked'" : "") . " />

		<label for='global_applications'>Enable Applications</label>

		<br />

		<span>Accept applications for:</span>

		<select name='global_applicationseason' class='float-right'>

		" . Season::picker(2011, true, Site::ApplicationSeason(), true) . "

		</select>

		<br />

		<span>Applications are due by:</span>

		<input type='text' name='applicationsdeadline' class='float-right' value=\"" . Util::Format(Site::$ApplicationDeadline) . "\" />

	</div>

	<div style='margin-top: 5px; padding: 5px; border: 1px solid #999;'>

		<h3>Search Engine Optimization</h3>

		<label for='global_metadescription'>Meta description</label>

		<input type='text' class='float-right' style='width: 310px' name='global_metadescription' value=\"" . htmlspecialchars(Site::$MetaDescription) . "\" />

		<br />

		<label for='global_metakeywords'>Meta keywords</label>

		<input type='text' class='float-right' style='width: 310px' name='global_metakeywords' value=\"" . htmlspecialchars(Site::$MetaKeywords) . "\" />

	</div>

	<div style='margin-top: 5px; padding: 5px; border: 1px solid #999;'>

		<h3>Broadcasting Server</h3>

		<label for='icecast_server'>Icecast Server</label>

		<input type='text' class='float-right' style='width: 310px' name='icecast_server' value=\"" . htmlspecialchars(Site::$IcecastServer) . "\" />

		<br />

		<label for='icecast_username'>Icecast Username</label>

		<input type='text' class='float-right' style='width: 310px' name='icecast_username' value=\"" . htmlspecialchars(Site::$IcecastUsername) . "\" />

		<br />

		<label for='icecast_password'>Icecast Password</label>

		<input type='text' class='float-right' style='width: 310px' name='icecast_password' value=\"" . htmlspecialchars(Site::$IcecastPassword) . "\" />

	</div>
	<div class='center'>
		<input type='submit' name='SAVE_GLOBAL_SETTINGS' value=' Save Changes ' />
	</div>
</form>


<form class='table' method='post' action='$_SERVER[PHP_SELF]'>
	<div class='center header'>
		<span>Override Deadline Code</span>
	</div>
	<div class='center'>
		<input type='text' name='code' style='width:320px;' onfocus='this.select();' value=\"" . htmlspecialchars(Util::encrypt(Site::ApplicationSeason()), ENT_COMPAT, "UTF-8") . "\" />
		<br />
		<i style='color:#757575;font-size:11px;'>Valid for $seasonName</i>
	</div>
	<div class='center'>
		<span>Email to: " . Picker::Users($season) . "</span>
		<br />
		<input type='submit' name='EMAIL_CODE' value='Send Email' />
	</div>
</form>");


// Icecast

        if ($streams) {

            Template::AddBodyContent("<table class='formtable'><tr><td>Mount</td><td>Name</td><td>Description</td><td>Listeners</td><td>Bitrate</td></tr>");

            foreach ($streams as $mount => $data) Template::AddBodyContent("<tr><td>$mount</td><td>" . $data['name'] . "</td><td>" . $data['description'] . "</td><td>" . $data['listeners'] . "</td><td>" . $data['bitrate'] . "</td></tr>");

            Template::AddBodyContent("</table>");

        }


        Template::AddBodyContent("</div>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}