<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 7:54 AM
 */

namespace AppBundle\Controller\dj;


use ChapmanRadio\Notify;
use ChapmanRadio\Request;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use ChapmanRadio\Uploader;
use ChapmanRadio\UserModel;
use ChapmanRadio\Util;
use Exception;
use Sinopia\DB;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ApplyController extends Controller
{

    /**
     * @Route("/dj/apply", name="dj_apply")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');


        Template::css("/legacy/css/formtable.css");
        Template::css("/legacy/css/dl.css");
        Template::js("/legacy/js/jquery.watermark.min.js");
        Template::js("/legacy/js/postform.js");
        Template::js("/legacy/dj/js/apply.js");

        Template::SetPageTitle("Apply for a Show");
//        Template::RequireLogin("/dj/apply","Show Applications");

        $appSeason = Site::ApplicationSeason();
        $seasonName = Season::name($appSeason);

        Template::SetBodyHeading("My Show Applications for $seasonName", "");

        // are applications open?
        if (!Site::$Applications || strtotime(Site::$ApplicationDeadline) < time()) {
            Site::Update('applications', '0');

            $override_code = Request::Get('override_code', Request::GetFrom($_SESSION, 'ApplicationOverrideCode'));
            $overrideok = ($override_code) ? (Util::decrypt($override_code) == Site::ApplicationSeason()) : false;

            if ($overrideok) { // $appSeason == Site::CurrentSeason() &&
                $_SESSION['ApplicationOverrideCode'] = $override_code;
                if (strtotime(Site::$ApplicationDeadline) < time())
                    Template::notify("Past Deadline", "Please fill out the application for $seasonName as soon as possible!", "warning");
            } else {
                Template::AddBodyContent("<div style='width:640px;margin:10px auto;text-align:left;'><h3>Applications are Closed</h3>");
                Template::AddBodyContent("<p>Sorry, but Chapman Radio is not currently accepting applications.</p><p>Applications typicallly open after the first Wednesday meeting of the semester, and then close again the following Sunday.</p>");

                if (Session::isStaff()) {
                    $staffoverridecode = Util::encrypt(Site::ApplicationSeason());
                    Template::AddBodyContent("<div class='gloss'><h3>You are on Staff</h3><p>Here is the override code:</p>
			<p><input type='text' style='width:320px;margin:auto;' value=\"" . htmlspecialchars($staffoverridecode, ENT_COMPAT, "UTF-8") . "\" autocomplete='off' readonly='true' /></p></div>");
                }
                Template::AddBodyContent("<p>Did you miss the deadline? <a href='/faqs#MissedDeadline'>Get help with a missed deadine.</a></p>");
                Template::AddBodyContent("<form method='get' action='$_SERVER[REQUEST_URI]'><div class='gloss'>
			<h3>Apply</h3>
			<p>If you were given an special code to override the deadline, enter that code here:</p>" .
                    (isset($_REQUEST['override_code']) ? "<p style='color:red'>Incorrect. Please try again:</p>" : "") .
                    "<div style='text-align:center'><input style='width:320px;' id='code' type='text' name='override_code' value=\"" . Request::GetAsPrintable('override_code') . "\" />
			<br />
			<input type='submit' value=' Submit ' />
			</div>
		</div></form>");

                return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($this->container,''));
            }
        }

// data we'll use a lot
        $userid = Session::getCurrentUserID();
        $user = Session::getCurrentUser();
        $handle_email_link = true;

        $show = null;
        if (isset($_REQUEST['editshow'])) {
            $showid = Request::GetInteger('editshow');
            $show = ShowModel::FromId($showid);

            if (!$showid) {
                Template::AddInlineError("Please pick a show, then try again.");
                $show = null;
            } else if (!$show || !$show->HasDj($user->id)) {
                Template::AddInlineError("The application you are trying to edit could not be loaded.<br />This error can occur if you are trying to edit an application that has been deleted, or if you are trying to edit an application that you are not a DJ of.");
                $show = null;
            } else {
                Template::SetBodyHeading("Show Application", $show->name);
                Template::AddToBodyHeading("<a style='position: absolute; top: 0; right: 0; color: #CCC; margin: 10px; display: block;' href='/dj/apply'>Back to My Applications</a>");
            }
        }

// Form submits
        if (isset($_POST['NEW_SHOW'])) {
            $showname = Request::Get('showname');
            $existshow = DB::GetFirst("SELECT showid FROM shows WHERE showname = :name AND seasons LIKE :seasons", [
                ":name" => $showname,
                ":seasons" => "%$appSeason%"
            ]);

            $usershow = DB::GetFirst("SELECT showid FROM shows WHERE showname = :name AND (userid1 = :uid OR userid2 = :uid OR userid3 = :uid OR userid4 = :uid OR userid5 = :uid)", [
                ":name" => $showname,
                ":uid" => $user->id
            ]);

            if (!$showname) {
                Template::AddInlineError("Please enter a show name.");
            } else if (strlen($showname) > 100) {
                Template::AddInlineError("Woah, seriously? Your show name, <b>$showname</b>, is <i>way</i> too long.");
            } else if ($usershow) {
                Template::AddInlineError("Sorry, " . $user->fname . ". You already have a show with this name.<br />To continue that show, choose it in list of existing shows.<br /><br />If it does not appear, contact webmaster@chapmanradio.com to have it re-enabled.");
            } else if ($existshow) {
                Template::AddInlineError("Sorry, " . $user->fname . ". The name, <b>$showname</b>, is already taken for $seasonName. Please choose a different name.");
            } else {
                $showid = DB::Insert("shows", [
                    "showname" => $showname,
                    "userid1" => $user->id,
                    "seasons" => $appSeason,
                    "createdon" => date("Y-m-d"),
                    "status" => "incomplete",
                    "revisionkey" => "CRDFDTWBMT"]);
                $showname = stripslashes($showname);
                Template::AddInlineSuccess("You've created a new show application, <b>$showname</b>.");
            }
        } else if (isset($_POST['CONTINUE_SHOW'])) {
            $show = ShowModel::FromId(Request::GetInteger('showid'));
            if (!$show) {
                Template::AddInlineError("Please pick a show from the drop down menu, then try again.");
            } else {
                $show->SetStatus('incomplete');
                $show->AddSeason($appSeason);
                Template::AddInlineSuccess("You've opened up <b>" . $show->name . "</b> as an application for $seasonName.");
            }
        } else if (isset($_POST['EMAIL_CODE'])) {
            $email = trim(Request::Get('email'));
            if (!$email) {
                Template::AddInlineError("Please enter an email address");
            } else if (!preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}\$/", $email)) {
                Template::AddInlineError("Sorry, but it looks like <b>$email</b> is an invalid email address.");
            } else {
                $link = "https://chapmanradio.com/dj/apply?addcode=" . urlencode(Util::encrypt($user->id));
                $retval = Notify::mail($email, "Chapman Radio co-DJ Code", "<h1>Co-DJ Code</h1><p>Hello,</p><p>You are receiveing this email because <b>" . $user->name . "</b> wants to be a co-DJ on your show.</p><p>If you have an open show application on Chapman Radio, and you want " . $user->fname . " to become a part of your show, follow this link:</p><p style='text-align:center'><a href='$link'>$link</a></p><p>If you haven't applied for a show in $seasonName, then you may ignore this email.</p>");
                if (!$retval) {
                    Template::AddInlineSuccess("Your unique co-DJ code has been emailed to <b>$email</b>");
                } else {
                    Template::AddInlineError("Your email did not send.<br />$retval");
                }
            }
        } else if (isset($_POST['SAVE_DJ'])) {
            $djid = Request::GetInteger('userid');
            $dj = UserModel::FromId($djid);
            $djname = Request::Get('djname');
            $classclub = Request::Get('classclub') == 'class' ? 'class' : 'club';
            $confirmnewsletter = Request::GetBool('confirmnewsletter');
            if (!$djid) {
                Template::AddInlineError("Invalid User ID #. Please try again");
            } else if (!$dj) {
                Template::AddInlineError("User ID #$djid does not exist. Please refresh the page and try again");
            } else {
                if (!$djname) $djname = $dj->name;
                DB::Query("UPDATE users SET djname = :djname, classclub = :classclub, confirmnewsletter = :confirmnewsletter WHERE userid = :id", [
                    ":djname" => $djname,
                    ":classclub" => $classclub,
                    ":confirmnewsletter" => $confirmnewsletter,
                    ":id" => $djid
                ]);
                Template::AddInlineSuccess("Your changes to <b>{$dj->fname}</b> have been saved.");
            }
        } else if (isset($_POST['ADD_DJ'])) {
            $codj_code = Request::Get('codj_code', 0);
            $codj_id = Util::decrypt($codj_code);
            $codj = UserModel::FromId($codj_id);

            if (!$codj_code || !is_numeric($codj_id) || $codj_id == 0) {
                Template::AddInlineError("Missing / Invalid co-dj code. Please try again.");
            } else if (!$show) {
                Template::AddInlineError("Internal Error: No show is currently loaded. Try again, or email webmaster@chapmanradio.com for help.");
            } else if (!$codj) {
                Template::AddInlineError("Sorry, but the user you are trying to add as a co-DJ could not be found. Email webmaster@chapmanradio.com for help.");
            } else if ($show->HasDj($codj_id)) {
                Template::AddInlineError("{$codj->name} is already a DJ for {$show->name}");
            } else {
                try {
                    $show->AddDj($codj_id);
                    Template::AddInlineSuccess("You've just added <b>{$codj->name}</b> as a co-DJ to <b>{$show->name}</b>.");
                    $handle_email_link = false;
                } catch (Exception $e) {
                    Template::AddInlineError("There is a limit of 5 DJs for a single show.");
                }
            }
        } else if (isset($_POST['REMOVE_DJ'])) {
            $codj_id = Request::GetInteger('codjid');
            $codj = UserModel::FromId($codj_id);
            if (!$codj_id) {
                Template::AddInlineError("Sorry, that co-dj could not be removed because the User ID # $codj_id was missing. Email webmaster@chapmanradio.com for help.");
            } else if ($userid == $codj_id) {
                Template::AddInlineError("You can't remove yourself from your own application, that's just silly.");
            } else if (!$codj) {
                Template::AddInlineError("Sorry, but the user you are trying to remove as a co-DJ could not be found. Try again, or email webmaster@chapmanradio.com for help.");
            } else if (!$show) {
                Template::AddInlineError("Internal Error: No show is currently loaded. Try again, or email webmaster@chapmanradio.com for help.");
            } else {
                try {
                    $show->RemoveDj($codj_id);
                    Template::AddInlineSuccess("<b>{$codj->name}</b> has been removed from <b>{$show->name}</b>.");
                } catch (Exception $e) {
                    Template::AddInlineError("Error: Unable to remove DJ. Email webmaster@chapmanradio.com for help.");
                }
            }
        } else if (isset($_POST['SAVE_SHOWINFO'])) {
            try {
                $fields = array("name", "genre", "description", "explicit", "musictalk", "turntables", "podcastcategory");
                foreach ($fields as $field) $show->Update($field, Request::Get($field, 0));
                Template::AddInlineSuccess("Your changes have been saved.");
            } catch (Exception $e) {
                Template::AddInlineError("There was an error saving your changes");
            }
        } else if (isset($_POST['SAVE_QUESTIONS'])) {
            if (!$show) {
                Template::AddInlineError("Internal Error: No show is currently loaded. Try again, or email webmaster@chapmanradio.com for help.");
            } else {
                $questions = array("app_differentiate", "app_promote", "app_timeline", "app_giveaway", "app_speaking", "app_equipment", "app_prepare", "app_examples");
                foreach ($questions as $question) $show->Update($question, Request::Get($question));
                Template::AddInlineSuccess("Your responses have been saved.");
            }
        } else if (isset($_POST['SAVE_AVAILABILITY'])) {
            if (!$show) {
                Template::AddInlineError("Internal Error: No show is currently loaded. Try again, or email webmaster@chapmanradio.com for help.");
            } else {
                Template::AddInlineSuccess("Your <b>availability</b> has been saved.");
                $show->Update('availability', Request::Get('availability'));
                $show->Update('availabilitynotes', Request::Get('availabilitynotes'));
            }
        } else if (isset($_POST['FINALIZE_APPLICATION'])) {
            if (!$show) {
                Template::AddInlineError("Internal Error: No show is currently loaded. Try again, or email webmaster@chapmanradio.com for help.");
            } else {
                $show->Update('status', 'finalized');
                Template::AddInlineSuccess("Congratulations, your application for <b>{$show->name}</b> has been received!");
                $show = null;
            }
        }

// Handle image uploads where JS failed
        try {
            if (Uploader::HandleModel($show) !== NULL) Template::AddInlineSuccess("Your image has successfully been uploaded.");
        } catch (Exception $e) {
            Template::AddInlineError($e->GetMessage());
        }

// Incoming email link
        if ($handle_email_link && isset($_GET['addcode'])) {
            $codjcode = Request::Get('addcode');
            $codjid = intval(Util::decrypt($codjcode));
            $codj = UserModel::FromId($codjid);

            if (!$codjcode) {
                Template::AddInlineError("You didn't enter a <b>co-DJ code</b>. Please enter a co-DJ code and try again");
            } else if (!$codjid) {
                Template::AddInlineError("Sorry, but the <b>co-DJ code</b> you used was <b>invalid</b>.");
            } else if (!$codj) {
                Template::AddInlineError("Sorry, but that <b>co-DJ code</b> has expired. Please ask your co-dj for a new code.");
            } else {
                Template::AddToBodyHeading("<a style='position: absolute; top: 0; right: 0; color: #CCC; margin: 10px; display: block;' href='/dj/apply'>Back to My Applications</a>");
                Template::AddBodyContent("<div style='width:684px;margin:10px auto;text-align:left;'>");

                $result = ShowModel::FromResults(DB::GetAll("SELECT * FROM shows WHERE (seasons LIKE '%$appSeason%') AND (status='incomplete') AND (userid1=$userid OR userid2=$userid OR userid3=$userid OR userid4=$userid OR userid5=$userid)"));

                if (empty($result)) {
                    Template::AddInlineError("You don't have any open applications for $seasonName.<br />Please <a href='/dj/apply'>start an application</a>, then try again");
                } else {
                    Template::AddBodyContent("<form method='post' action='$_SERVER[REQUEST_URI]'>
			<h3>Add {$codj->name} to which show?</h3><div style='clear:both; overflow: auto; margin: 5px;'>");
                    foreach ($result as $show) {
                        Template::AddBodyContent("<div style='float:left; width: 150px;'>
					<label for='showid" . $show->id . "'><img src='" . $show->img90 . "' alt='' /></label><br />
					<input type='radio' name='editshow' value='" . $show->id . "' id='showid" . $show->id . "' style='width:auto;' />
					<label for='showid" . $show->id . "'>" . $show->name . "</label><br />
					</div>");
                    }
                    Template::AddBodyContent("
				</div><div>
					<input type='hidden' name='codj_code' value='" . $codjcode . "' />
					<input type='submit' name='ADD_DJ' class='button' value=' Add co-DJ ' />
				</div></form>");
                }
                Template::AddBodyContent("</div>");

                return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($this->container));
            }
        }

        $tab = Request::Get('tab');
        if (!$show) $tab = "default";

// output
        switch ($tab) {
            case "djs":
                self::RenderDjTab();
                break;
            case "showinfo":
                self::RenderShowInfoTab();
                break;
            case "questions":
                self::RenderQuestionsTab();
                break;
            case "picture":
                self::RenderPictureTab();
                break;
            case "availability":
                self::RenderAvailabilityTab();
                break;
            case "finalize":
                self::RenderFinalizeTab();
                break;
            default: // start page
                self::RenderStartPage();
                break;
        }


        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($this->container));
    }

    function RenderStartPage()
    {
        global $appSeason, $seasonName, $userid, $user;
        Template::AddBodyContent("<div class='couju-info'>Make sure your show application says 'Received' - otherwise we can't add it to the schedule</div><div style='width:684px;margin:10px auto;text-align:left;'><h3>My Applications</h3><br /><table class='formtable' cellspacing='0'>");
        $statusColors = array("incomplete" => "#D60", "finalized" => "#090", "cancelled" => "#A00", "accepted" => "#090");

        $count = 0;
        $shows = ShowModel::FromDj($userid);
        foreach ($shows as $show) {
            if ($show->status == 'cancelled') continue;
            if ($show->status != 'incomplete' && !$show->HasSeason($appSeason)) continue;

            $appStatus = "Unknown";
            if ($show->status == 'incomplete') $appStatus = 'incomplete';
            if ($show->status == 'accepted') $appStatus = 'received';
            if ($show->status == 'finalized') $appStatus = 'received';

            $rowclass = ++$count % 2 == 0 ? "evenRow" : "oddRow";
            $color = $statusColors[$show->status];

            Template::AddBodyContent("<tr class='$rowclass'><td><img src='" . $show->img50 . "' alt='' /><td>" . $show->name . "</td><td><i style='color:#757575;'>Status:</i><br /><span style='white-space: nowrap;color:$color'>" . ucfirst($appStatus) . "</span></td><td>");
            if ($show->status == "incomplete") Template::AddBodyContent("<a href='?editshow=" . $show->id . "&tab=djs' style='white-space:nowrap;'>Edit Application</a>");
            Template::AddBodyContent("</td></tr>");
        }

        if (!$count) Template::AddBodyContent("<tr class='evenRow'><td colspan='10' style='background:#F4F4F4;text-align:center;'>You don't have any open applications right now.</td></tr>");

        Template::AddBodyContent("</table>");
        Template::AddBodyContent("<h3>Start a New Application</h3>
	<div style='min-height:280px;margin-bottom:20px;'>
		<p style='margin-left:20px;'><input type='radio' name='for' id='newshow' value='1' onchange='checkAppCheckBoxes()' /><label for='newshow'> I'm applying for a <b>new show</b>.</label></p>
		<div style='display:none;' id='newshowdiv'>
			<form method='post' action='$_SERVER[REQUEST_URI]'><table class='formtable' cellspacing='0'>
			<tr class='oddRow'><td colspan='2' style='text-align:center'>New Show Application</td></tr>
			<tr class='evenRow'><td>What do you want your new show to be called?</td><td><input type='text' name='showname' value='' id='showname' /></td></tr>
			<tr class='oddRow'><td colspan='2' style='text-align:center;'>
				<input onchange='checkAcceptBoxes();' type='checkbox' name='accept1' value='1' id='accept1' style='width:auto;' /> <label for='accept1'>I agree to the <a href='/policies' target='_blank'>Chapman Radio Policies</a></label><br />
				<input onchange='checkAcceptBoxes();' type='checkbox' name='accept2' value='1' id='accept2' style='width:auto;' /> <label for='accept2'>I will adhere to the <a href='/syllabus' target='_blank'>Current Syllabus</a></label></td></tr>
			<tr class='evenRow'><td colspan='2' style='text-align:center;'><input type='submit' id='newshowsubmittbutton' disabled='disabled' name='NEW_SHOW' value=' Start a new application ' /></p>
			</table></form>
		</div>
		
		<p style='margin-left:20px;'>
			<input type='radio' name='for' id='existingshow' value='1' onchange='checkAppCheckBoxes()' />
			<label for='existingshow'> I'm going to continue an <b>existing show</b>.</label>
		</p>
			
		<div style='display:none;' id='existingshowdiv'>
			<form method='post' action='$_SERVER[REQUEST_URI]'><table class='formtable' cellspacing='0'>
			<tr class='oddRow'><td colspan='2' style='text-align:center'>Continue Existing Show</td></tr>");

        $shows = array();
        $picker = "<option value=''> - Pick a Show - </option>";

        $showdata = ShowModel::FromDj($userid);
        foreach ($showdata as $show) {
            if ($show->status == 'cancelled') continue;
            $shows[$show->id] = $show->name;
            $picker .= "<option value='" . $show->id . "'>" . $show->name . "</option>";
        }

        if ($shows) {
            Template::AddBodyContent("<tr class='evenRow'><td>Which show do you want to continue?</td><td><select onchange='updateContinueShowButton()' name='showid' id='showid'>$picker</select></td></tr>
		<tr class='oddRow'><td colspan='2' style='text-align:center'><input style='width:auto;' id='continueshowsubmit' type='submit' name='CONTINUE_SHOW' value=' Continue ' /></td></tr>");
            Template::script("shows = " . json_encode($shows) . ";");
        } else {
            Template::AddBodyContent("<tr class='evenRow'><td colspan='2'>Sorry, " . $user->fname . ".<br />You don't have any past shows that are eligible to continue for $seasonName. Feel free to start a new show, though.<br /><br />If you had a show that was cancelled, it cannot be continued automatically. Please email webmaster@chapmanradio.com to have a previously cancelled show re-enabled.</td></tr>
		<tr class='oddRow'><td colspan='2' style='text-align:center'><input style='width:auto;' id='continueshowsubmit' type='submit' name='CONTINUE_SHOW' value=' Continue for $seasonName ' disabled='disabled' /></td></tr>");
        }
        Template::AddBodyContent("
		</table></form>
		</div>
		<p style='margin-left:20px;'><input type='radio' name='for' id='codj' value='2' onchange='checkAppCheckBoxes()' /><label for='codj'> I want to be a <b>co-DJ</b> on an existing application.</label></p>
		<div style='display:none;' id='codjdiv'>
			<form method='post' action='$_SERVER[REQUEST_URI]'>
			<table class='formtable' cellspacing='0'>
			<tr class='oddRow'><td style='text-align:center'>Become a co-DJ</td></tr>
			<tr class='evenRow'><td><p>We use a unique code to securely identify each DJ. If you want to be added to a show, just use this tool to send your unique code to another DJ (your co-host) already on the show. That person will get an email, with a link to add you to their show. You could also use another way to get your code to that DJ.</p></td></tr>
			<tr class='oddRow'><td style='text-align:center;'><div class='gloss' style='width:330px;padding:10px 20px;margin:10px auto 20px;'>
			<p>This is your co-DJ code:</p>
			<p><input type='text' name='codjcode' style='width:320px;' onfocus='this.select()' readonly='readonly' value=\"" . htmlspecialchars(Util::encrypt($userid)) . "\" /></p>
			<p>Email this code to: <input type='text' name='email' value='' id='email' style='width:200px;' /><input style='width:auto;' type='submit' name='EMAIL_CODE' value='Send Code' /></p>
			</div></td></tr>
			
			</table></form>
		</div>
	</div></div>");
    }

    function RenderDjTab()
    {
        global $show, $userid;
        Template::AddBodyContent(menu("djs"));
        Template::AddBodyContent("<div style='width:684px;margin:10px auto;text-align:left;'>");

        Template::AddBodyContent("<h3>Current DJs</h3>");
        $djs = $show->GetDjModels();
        foreach ($djs as $row) {
            Template::AddBodyContent("<div class='gloss'>
			<form method='post' action='$_SERVER[REQUEST_URI]'><img src='{$row->img64}' style='float:right;' />
			<dl>
			<dt>Name</dt><dd>{$row->name}</dd>
			<dt>DJ Name <i>(Optional)</i></dt>
			<dd><input type='text' name='djname' value=\"" . htmlspecialchars($row->djname, ENT_COMPAT, "UTF-8") . "\" /></dd>
			<dt>Participation</dt><dd>
				<input type='radio' name='classclub' value='class' id='class{$row->id}' style='width:auto' " . ($row->classclub == "class" ? "checked='checked'" : "") . " /> <label for='class{$row->id}'>I'm enrolled in the <b>class</b> for credit.</label><br />
				<input type='radio' name='classclub' value='club' id='club{$row->id}' style='width:auto' " . ($row->classclub == "club" ? "checked='checked'" : "") . " /> <label for='club{$row->id}'>I'm doing the <b>club</b> for fun.</label></dd>
			</dl>
			<br />
			<p><input type='checkbox' name='confirmnewsletter' value='1' id='confirmnewsletter{$row->id}' style='width:auto;' " . ($row->confirmnewsletter ? "checked='checked'" : "") . " /> <label for='confirmnewsletter{$row->id}'>I understand that Chapman Radio will occasionally email me at {$row->email}.</label></p>
			<input type='hidden' name='userid' value='{$row->id}' />
			<input type='submit' name='SAVE_DJ' value=' Save ' />
			</form>");
            if ($row->id != $userid) {
                Template::AddBodyContent("<form method='post' action='$_SERVER[REQUEST_URI]'>
			<input type='hidden' name='codjid' value='{$row->id}' />
			<input type='submit' name='REMOVE_DJ' value=' Remove from show ' onclick='return confirm(\"You are about to remove this co-DJ. Continue?\");' /></form>");
            }
            Template::AddBodyContent("</div>");
        }

        Template::AddBodyContent("<h3>Add Another DJ</h3>
		<p>You can add a cohost (co-DJ) to your show. Anyone who has a Chapman Radio account can become a part of your show.</p>
		<form method='post' action='$_SERVER[REQUEST_URI]'>
			<div class='gloss'>
				<p>Enter a co-DJ code:</p>
				<p><input type='text' name='codj_code' style='width:320px;' /></p>
				<p><input type='submit' name='ADD_DJ' value=' Add co-DJ ' /></p>
			</div>
			<div class='gloss'>
				<strong>Where do I get a \"co-DJ code\"?</strong>
				<p>The person you would like to add to your show needs to create an account at <a href='/join' target='_blank'>chapmanradio.com/join</a></p>
				<p>Once logged in, your co-dj can find their unique co-DJ code at <a href='/dj/apply' target='_blank'>chapmanradio.com/dj/apply</a></p>
			</div>
		</form>");

        Template::AddBodyContent("</div>");
    }

    function RenderShowInfoTab()
    {
        global $show;
        Template::AddBodyContent(menu("showinfo"));
        Template::AddBodyContent("<div style='width:684px;margin:10px auto;text-align:left;'>");
        Template::AddBodyContent("
	<form method='post' action='$_SERVER[REQUEST_URI]'>
		<table class='formtable' cellspacing='0'>
			<tr class='oddRow'>
				<td>Show Name</td>
				<td style='width:284px;'><input type='text' name='name' value=\"" . $show->name . "\" /></td>
			</tr>
			<tr class='evenRow'>
				<td>Genre</td>
				<td><select name='genre'><option value=''> - Pick a Genre - </option>");
        $genres = Site::$Genres;
        foreach ($genres as $genre) Template::AddBodyContent("<option value='$genre' " . ($genre == $show->genre ? "selected='selected'" : "") . ">$genre</option>");
        Template::AddBodyContent("</td></tr>
		<tr class='oddRow'><td>Description</td><td><textarea name='description'>" . $show->description . "</textarea></td></tr>
		<tr class='evenRow'><td colspan='2' style='text-align:center;padding:18px 10px;'>
			<input type='radio' name='musictalk' value='music' id='music' " . ($show->musictalk == 'music' ? "checked='checked'" : "") . " style='width:auto;' /> <label for='music'>Mostly Music</label>
			&nbsp; &nbsp; &nbsp; &nbsp; 
			<input type='radio' name='musictalk' value='both' id='both' " . ($show->musictalk == 'both' ? "checked='checked'" : "") . " style='width:auto;' /> <label for='both'>Even Music &amp; Talk</label>
			&nbsp; &nbsp; &nbsp; &nbsp; 
			<input type='radio' name='musictalk' value='talk' id='talk' " . ($show->musictalk == 'talk' ? "checked='checked'" : "") . " style='width:auto;' /> <label for='talk'>Mostly Talk</label>
		</td></tr>
		<tr class='oddRow'><td>Uncensored</td><td><input type='checkbox' name='explicit' id='explicit' " . ($show->explicit ? "checked='checked'" : "") . " value='1' style='width:auto;' /> <label for='explicit'>This show will have explicit language. I understand that my show will have to broadcast late at night</label></td></tr>
		<tr class='evenRow'><td>Turntables</td><td style='line-height:20px;'>
			<input type='radio' name='turntables' id='turntablesyes' value='yes' " . ($show->turntables == 'yes' ? "checked='checked'" : "") . " style='width:auto;' /> <label for='turntablesyes'>Yes, I'll use the turntables in my show.</label><br />
			<input type='radio' name='turntables' id='turntablesno' value='no' " . ($show->turntables == 'no' ? "checked='checked'" : "") . "style='width:auto;' /> <label for='turntablesno'>No, I don't need the turntables.</label><br />
			<input type='radio' name='turntables' id='turntablesteachme' value='teachme' " . ($show->turntables == 'teachme' ? "checked='checked'" : "") . "style='width:auto;' /> <label for='turntablesteachme'>Turntables? Teach me how to use them!</label><br />
		</td></tr>
		<tr class='oddRow'><td>Podcast Category</td><td><select name='podcastcategory'><option value=''> - Pick a Podcast Category - </option>");

        $podcastcategories = Podcast::$categories;
        foreach ($podcastcategories as $optgroup => $categories) {
            Template::AddBodyContent("<optgroup label='$optgroup'>");
            foreach ($categories as $val => $option) {
                Template::AddBodyContent("<option value='$val' " . ($val == $show->podcastcategory ? "selected='selected'" : "") . ">$option</option>");
            }
            Template::AddBodyContent("</optgroup>");
        }
        Template::AddBodyContent("</select><br /><small style='color:#757575'>Chapman Radio will automatically create a Podcast with each episode of your show.</small></td></tr>
		<tr class='evenRow'><td colspan='2' style='text-align:center'>
			<input type='submit' name='SAVE_SHOWINFO' value=' Save ' />
		</td></tr>
	");
        Template::AddBodyContent("</table></form>");
        Template::AddBodyContent("</div>");
    }

    function RenderQuestionsTab()
    {
        global $show;
        Template::AddBodyContent(menu("questions"));
        Template::IncludeStyle(".apply-question { float: left; width: 47%; border: 1px solid #EEE; padding: 5px; margin: 5px; text-align: left; } .apply-question textarea { margin:5px; width: 97%; min-height: 75px; padding: 3px; border: 1px solid #CCC; }");
        Template::AddBodyContent("
		<form method='post' action='$_SERVER[REQUEST_URI]'>
		<div style='clear:both'>
			<div class='apply-question'>
				What will differentiate your show and make it stand out on the radio?<br />
				<textarea name='app_differentiate'>" . $show->app_differentiate . "</textarea>
			</div>
			<div class='apply-question'>
				How will you promote your show and raise listenership?<br />
				<textarea name='app_promote'>" . $show->app_promote . "</textarea>
			</div>
			<div class='apply-question'>
				Please explain the timeline / content of a typical show.<br />
				<textarea name='app_timeline'>" . $show->app_timeline . "</textarea>
			</div>
			<div class='apply-question'>
				Are you interested in being a part of out giveaways? Do you have any creative ideas for how to give away prizes?<br />
				<textarea name='app_giveaway'>" . $show->app_giveaway . "</textarea>
			</div>
			<div class='apply-question'>
				Do you have any experience in public speaking or radio broadcasts?<br />
				<textarea name='app_speaking'>" . $show->app_speaking . "</textarea>
			</div>
			<div class='apply-question'>
				Are you prepared for an hour long show every week, or would you prefer a show every other week? If our schedule permits, would you be interested in more then one hour a week?<br />
				<textarea name='app_equipment'>" . $show->app_equipment . "</textarea>
			</div>
			<div class='apply-question'>
				Tell us what you would do to prepare for your show.<br />
				<textarea name='app_prepare'>" . $show->app_prepare . "</textarea>
			</div>
			<div class='apply-question'>
				List 6 examples of music that you would play or topics that you talk about.<br />
				<textarea name='app_examples'>" . $show->app_examples . "</textarea>
			</div>
		</div>
		<div>
			<input type='submit' class='button' name='SAVE_QUESTIONS' value=' Save ' />
		</div>
		</form>");
    }

    function RenderPictureTab()
    {
        global $show;
        Template::AddBodyContent(self::menu("picture"));
        Template::AddBodyContent("
		<table class='formtable' cellspacing='0' style='text-align:center'>
			<tr class='oddRow'><td>Current Picture</td></tr>
			<tr class='evenRow'><td><div style='margin: 0 auto;'><img id='dj-apply-picture' style='max-width: 310px;' src='" . $show->img310 . "' alt='' /></div></td></tr>
			<tr class='oddRow'><td style='width:300px;text-align:left;'>" . Uploader::RenderModel($show, 'dj-apply-picture') . "</td></tr>
		</table>");
    }

    function RenderAvailabilityTab()
    {
        global $show;
        Template::Css("/legacy/css/timeslots.css?v2");
        Template::JS("/legacy/js/timeslots.js?v2");

        Template::AddBodyContent(self::menu("availability"));
        Template::AddBodyContent("
	<div class='couju-info'>We need to know when you are available to schedule a show time.<br /><b>Click and drag</b> the schedule to let us know when you are <b style='color:#A00'>not available</b></div>
	<div class='t_container'>
	<div class='t_blank'></div><div class='t_label t_toplabel'>Monday</div><div class='t_label t_toplabel'>Tuesday</div><div class='t_label t_toplabel'>Wednesday</div><div class='t_label t_toplabel'>Thursday</div><div class='t_label t_toplabel'>Friday</div><div class='t_label t_toplabel'>Saturday</div><div class='t_label t_toplabel'>Sunday</div>");
        $table = array();

        if ($show->availability) {
            $fields = explode(",", $show->availability);
            foreach ($fields as $field) {
                if (!$field) continue;
                $col = 0;
                $vals = explode("-", $field);
                if (count($vals) == 2) list($row, $col) = $vals;
                else $row = $vals;

                if (!isset($table[$row])) $table[$row] = array();
                if ($col) $table[$row][$col] = true;
                else $table[$row][$col] = false;
            }
        }

        for ($row = 7; $row <= 28; $row++) {
            $rowclass = "";//$row % 2 == 0 ? 't_evenRow' : 't_oddRow';
            Template::AddBodyContent("<div class='t_row $rowclass'><div class='t_label'>" . Util::hourName($row) . "</div>");
            if (!isset($table[$row])) $table[$row] = array();
            for ($col = 1; $col <= 7; $col++) {
                if (!isset($table[$row][$col])) $table[$row][$col] = false;
                Template::AddBodyContent("<div class='t_cell " . (($table[$row][$col]) ? "t_selected" : "t_unselected") . "' id='t$row-$col'></div>");
            }
            Template::AddBodyContent("</div>");
        }
        Template::AddBodyContent("<div id='t_div' draggable='false' ondragstart='return false;' style='-webkit-user-drag:none;'></div></div>
	<form method='post' action='$_SERVER[REQUEST_URI]'>
		<input type='hidden' id='availability_output' name='availability' />
			<table class='formtable' cellspacing='0' style='text-align:center;margin:10px auto 20px;'>
			<tr class='oddRow'><td>Availability Notes</td></tr>
			<tr class='evenRow'><td>Comments about my availability:<br /><textarea id='availabilitynotes' name='availabilitynotes' rows='4' cols='40' maxlength='600'>" . $show->availabilitynotes . "</textarea><br /><small style='color:#757575'>It's difficult to schedule all of Chapman Radio's DJs. Please mark yourself as unavailable only for the days in which you have a serious class or activity that makes it impossible for you to do your show in that timeslot.</small></td></tr>
			<tr class='oddRow'><td><input type='submit' id='availabilitysubmit' name='SAVE_AVAILABILITY' value=' Save Availability ' /></td></tr>
		</table>
	</form>");
    }

    function RenderFinalizeTab()
    {
        global $show;
        Template::AddBodyContent(self::menu("finalize"));

        $requirements = self::calcRequirements($show);
        if ($requirements) {
            Template::AddBodyContent("
		<div class='couju-error'>Your application is <b style='color:#A00'>incomplete</b>!<br/>You can't submit your application until you have filled out all of the requirements.</div><div class='req'>");
            $prevCat = "";
            foreach ($requirements as $dat) {
                list($cat, $requirement) = $dat;
                if ($cat != $prevCat) {
                    switch ($cat) {
                        case "djs":
                            $catName = "DJs";
                            break;
                        case "showinfo":
                            $catName = "Show Info";
                            break;
                        default:
                            $catName = ucfirst($cat);
                    }
                    Template::AddBodyContent("<div style='margin-top: 10px;'><a href='?editshow=" . $show->id . "&tab=" . $cat . "'><h2>$catName</h2></a></div>");
                    $prevCat = $cat;
                }
                Template::AddBodyContent("<div>$requirement</div>");
            }
        } else {
            Template::AddBodyContent("
			<div class='couju-notice'>Your application is <b style='color:#090'>almost complete</b>!<br />Just read and confirm the following statements to submit your show for approval / scheduling</div>
			<div id='apply-checkboxes'>");

            $cboxCount = 0;
            function cbox($msg)
            {
                global $cboxCount;
                $cboxCount++;
                return "<p style='margin-bottom: 3px;'><input type='checkbox' name='cbox$cboxCount' onchange='checkcboxes();' id='cbox$cboxCount' value='1' /> <label for='cbox$cboxCount'>$msg</label></p>";
            }

            Template::AddBodyContent("<blockquote style='text-align: left; width: 550px; margin: 0 auto;'>");
            Template::AddBodyContent(cbox("I understand that after I <b>finalize</b> my application, I won't be able to make any changes to it."));
            Template::AddBodyContent(cbox("I understand that all accepted shows and their DJs are subject to the <a href='/policies' target='_blank'>Chapman Radio Policies</a> and current <a href='/syllabus'>Syllabus</a>."));
            Template::AddBodyContent(cbox("I understand the <b>3 Strikes Policy</b>: If any DJ on this show gets three strikes, this show will be cancelled."));
            Template::AddBodyContent("<blockquote>");
            Template::AddBodyContent(cbox("1 unexcused <b>absence</b> from my show = 1 strike"));
            Template::AddBodyContent(cbox("3 <b>tardies</b> to my show or workshop = 1 strike"));
            Template::AddBodyContent(cbox("1 missed <b>workshop</b> = 1 strike"));
            //Template::AddBodyContent(cbox("Less than 2 <b>peer evaluations</b> completed in a week = 1 strike"));
            Template::AddBodyContent("</blockquote>");
            Template::AddBodyContent(cbox("I have read and understand Chapman University's <a href='http://www.chapman.edu/campus-services/information-systems/security/dmca.aspx' target='_blank'>Digital Millennium Copyright Act (DMCA) Policy</a>. I understand how the provisions of the DMCA could affect my show. I understand that I am completely responsible, equally along with my co-djs, for all content that is broadcast during my show."));
            Template::AddBodyContent(cbox("I understand that Chapman University and Chapman Radio do not actively monitor my show and cannot be responsible for the content that is broadcast. I understand that responsibility for my show is shared between all registered DJs on the show, and shall notify Chapman Radio of any changes to the DJs responsible for my show."));
            Template::AddBodyContent(cbox("I understand the Chapman Radio is going to be awesome this semester!"));
            Template::AddBodyContent("</blockquote>");
            Template::AddBodyContent("<form method='post' action=''><p style='text-align:center'><input type='submit' name='FINALIZE_APPLICATION' id='submitbutton' value=' Finalize ' disabled='true' /></p></form></div>");
        }
        Template::AddBodyContent("</div>");
    }

    function menu($default)
    {
        global $show;
        $pages = array("djs" => "DJs", "showinfo" => "Show Info", "questions" => "Questions", "picture" => "Picture", "availability" => "Availability", "finalize" => "Submit");
        $menu = "<div class='tabs' style='margin-bottom: 10px; margin-top: 10px;'><ul>";
        foreach ($pages as $tab => $label) {
            $highlighted = $tab == $default ? "class='active'" : "";
            $menu .= "<li $highlighted><a href='?editshow=" . $show->id . "&tab=$tab'>$label</a></li>";
        }
        $menu .= "</ul></div>";
        return $menu;
    }

    function calcRequirements($show)
    {
        $requirements = array();
        foreach ($show->userids as $djid) {
            if (!$djid) continue;
            $dj = UserModel::FromId($djid);
            if (!$dj) continue;
            if (!$dj->classclub) $requirements[] = array("djs", "Is <b>{$dj->fname}</b> in the <b>class</b> or the club</b>?");
            if (!$dj->confirmnewsletter) $requirements[] = array("djs", "<b>{$dj->fname}</b> needs to confirm that it's okay for Chapman Radio to send <b>emails</b>.");
        }
        if (!$show->name) $requirements[] = array("showinfo", "You need a <b>name</b>");
        if (!$show->genre) $requirements[] = array("showinfo", "Please specify a <b>genre</b>");
        if (!$show->description) $requirements[] = array("showinfo", "Please write a <b>description</b>");
        if (!$show->musictalk) $requirements[] = array("showinfo", "Please specify if you show is mostly <b>music or talk</b>");
        if (!$show->turntables) $requirements[] = array("showinfo", "Please specify if you need <b>turntables</b>");
        if (!$show->podcastcategory) $requirements[] = array("showinfo", "Please specify a <b>podcast category</b>");

        $questions = array("app_differentiate", "app_promote", "app_timeline", "app_giveaway", "app_speaking", "app_equipment", "app_prepare", "app_examples");

        $incomplete = 0;
        foreach ($questions as $question) if (!$show->$question) $incomplete++;
        if ($incomplete) $requirements[] = array("questions", "You didn't answer <b>$incomplete</b> of the <b>" . count($questions) . " questions.</b>");

        if (!$show->ImgExists()) $requirements[] = array("picture", "You need to <b>upload a picture</b> for your show.");

        if (!$show->availability) $requirements[] = array("availability", "You need to specify your <b>availability</b>.");

        return $requirements;
    }
}