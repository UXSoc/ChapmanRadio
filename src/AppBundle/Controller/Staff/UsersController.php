<?php
namespace AppBundle\Controller\Staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */


use ChapmanRadio\DB;
use ChapmanRadio\Log;
use ChapmanRadio\Request;
use ChapmanRadio\Season;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use ChapmanRadio\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UsersController extends Controller
{
    /**
     * @Route("/staff/users", name="staff_users")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("User Lookup");
        //Template::RequireLogin("/staff/users","Staff Resources", "staff");
        Template::js("/legacy/js/jquery.watermark.min.js");
        Template::Bootstrap();
        Template::shadowbox();
        Template::js("/legacy/staff/js/dialog_edit.js");

        $season = Site::CurrentSeason(true);
        $seasonName = Season::name($season);
        $input = Request::Get('input');

        Template::SetBodyHeading("Site Administration", "User Management");

// create user pickers
        $opts = array();
        $season = Site::CurrentSeason();
        $result = DB::GetAll("SELECT userid,fname,lname FROM users ORDER BY fname");  // WHERE seasons LIKE '%$season'
        foreach ($result as $row) $opts[] = "<option value='$row[userid]'>$row[fname] $row[lname] (#{$row['userid']})</option>";

// output the default page
        Template::Add("
	<div class='row'>
		<div class='col-md-4' style='border-right: 1px solid #CCC;'>
			<form class='form-horizontal' method='get' action=''>
				<h2>Search for User</h2>
				<div class='form-group'>
					<div class='col-sm-offset-1 col-sm-11'>
					<input type='text' placeholder='User ID #, Name, or Email' class='form-control' name='input' value=\"" . $input . "\" />
					</div>
				</div>
				<div class='form-group'>
					<label for='season' class='col-sm-4 control-label'>Filter by: </label>
					<div class='col-sm-8'>
					<select name='season' class='form-control'><option value='*'>No filter</option>" . Season::picker(2011, false, Request::Get('season', $season), true) . "</select>
					</div>
				</div>
				<div class='form-group'>
					<div class='col-sm-offset-1 col-sm-11'>
					<button type='submit' class='btn btn-default'>Search</button>
					</div>
				</div>
			</form>
		</div><div class='col-md-4'>
			<form method='get' action=''>
				<h2>Fetch Emails</h2>
				<div class='form-group'>
				<select name='season' class='form-control'>" . Season::picker(2011, false, $season, true) . "</select>
				</div>
				<div class='form-group'>
				<select name='classclub' class='form-control'>
					<option value='both'>Class + Club</option>
					<option value='class'>Class Only</option>
					<option value='club'>Club Only</option>
				</select>
				</div>
				<div class='form-group'>
				<button type='submit' class='btn btn-default' name='FETCH_EMAILS'>Fetch</button>
				</div>
			</form>
		</div><div class='col-md-4' style='border-left: 1px solid #CCC;'>

			<form class='form-horizontal' method='get' action=''>
				<h2>Merge Accounts</h2>
				<div class='form-group'>
					<label for='useridfrom' class='col-sm-2 control-label'>Delete</label>
					<div class='col-sm-10'>
					<select class='form-control' name='useridfrom'><option value=''> - Pick a User - </option>" . implode($opts) . "</select>
					</div>
				</div>
				<div class='form-group'>
					<label for='useridto' class='col-sm-2 control-label'>Keep</label>
					<div class='col-sm-10'>
					<select class='form-control' name='useridto'><option value=''> - Pick a User - </option>" . implode($opts) . "</select>
					</div>
				</div>
				<div class='form-group'>
					<div class='col-sm-offset-2 col-sm-10'>
					<button type='submit' name='cr_merge_users' class='btn btn-default'>Merge Accounts</button>
					</div>
				</div>
			</form>	
	</div></div>");

        if ($input) {
            Template::css("/legacy/css/formtable.css");
            $fields = array("fbid", "studentid", "name", "djname", "email");
            $users = UserModel::Search($input, Request::Get('season', NULL), "ORDER BY lname ASC");
            if (count($users) == 0) Template::Add("<p>No results found.</p>");
            else foreach ($users as $user) {
                Template::AddBodyContent("
			<div style='display: block; background: #FFF; border: 1px solid #CCC; vertical-align: top; padding: 5px; margin: 5px auto; width: 695px; overflow: auto;'>
			<table class='eros dialog-link -flutter' data-dialog='/staff/dialog/user_edit?userid={$user->id}' style='width: 350px; text-align: left; float: left;'>
			<thead><tr><td colspan=2>User #{$user->id}: {$user->name}</td></tr></thead>");

                foreach ($fields as $field) {
                    Template::AddBodyContent("<tr><td>$field</td><td>{$user->$field}</td></tr>");
                }
                Template::AddBodyContent("</table><table class='eros' style='width: 330px; float: right; text-align: left;'><tbody>");

                foreach ($user->GetShowModels() as $usershow) {
                    Template::AddBodyContent("<tr class='dialog-link -flutter' data-dialog='/staff/dialog/show_edit?showid={$usershow->id}'><td>{$usershow->name} (#{$usershow->id})</td></tr>");
                }

                Template::AddBodyContent("</tbody></table></div>");
            }
        }

        if (isset($_REQUEST['FETCH_EMAILS'])) {
            switch ($_REQUEST['classclub']) {
                case 'class':
                case 'club':
                    $classclubfilter_safe = "AND classclub = '{$_REQUEST['classclub']}'";
                    $emails_descriptor = strtoupper($_REQUEST['classclub'][0]) . substr($_REQUEST['classclub'], 1);
                    break;
                case 'both':
                default:
                    $classclubfilter_safe = "";
                    $emails_descriptor = "All";
                    break;
            }

            $result = DB::GetAll("SELECT lname,fname,name,email FROM users WHERE (type = 'staff' OR seasons LIKE '%$season%') {$classclubfilter_safe}");
            $emails = array();
            $mailchimps = array();
            foreach ($result as $row) {
                if ($row['email']) {
                    $emails[] = "\"" . str_replace("\"", "", $row['name']) . "\" &lt;" . $row['email'] . "&gt;";
                    $mailchimps[] = "\"" . str_replace("\"", "", $row['fname']) . "\",\"" . str_replace("\"", "", $row['lname']) . "\",\"" . $row['email'] . "\"";
                }
            }
            Template::AddBodyContent("<div style='width:750px;margin:10px auto;'><h3>{$emails_descriptor} Emails for $seasonName</h3><br /><div style='text-align:center;'><textarea rows='14' cols='84'>" . implode(",", $emails) . "</textarea><br />MailChimp Format:<br /><textarea rows='14' cols='84'>" . implode("\n", $mailchimps) . "</textarea></div></div>");
        }

        if (isset($_REQUEST["cr_merge_users"])) {
            $active = true;
            $useridfrom = Request::GetInteger('useridfrom');
            $userfrom = UserModel::FromId($useridfrom);
            $useridto = Request::GetInteger('useridto');
            $userto = UserModel::FromId($useridto);
            if (!$useridfrom || !$useridto)
                Template::AddInlineError("Please pick both a user to merge from and a user to merge to.");
            else if (!$userfrom || !$userto)
                Template::AddInlineError("Invalid userids. Please try again.");
            else if ($useridfrom == $useridto)
                Template::AddInlineError("You can't merge one user into itself. Please pick two separate user ids. (You entered $useridfrom and $useridto)");
            else {
                $changes = array(
                    array("alterations", "alteredby"),
                    array("attendance", "userid"),
                    array("evals", "userid"),
                    array("genrecontent", "staffid"),
                    array("grade_values", "user_id"),
                    array("news", "news_postedby"),
                    array("quizes", "userid"),
                    array("show_sitins", "result"),
                    array("staff_log", "userid"),
                    array("suspendedloginattempts", "userid"),
                    array("shows", "userid1"),
                    array("shows", "userid2"),
                    array("shows", "userid3"),
                    array("shows", "userid4"),
                    array("shows", "userid5"),
                    array("strikes", "userid")
                );
                Log::StaffEvent("Merged user #$useridfrom ({$userfrom->name}) into user #$useridto ({$userto->name})");
                Template::AddInlineSuccess("You are merging User ID #$useridfrom ({$userfrom->name}) into User ID #$useridto ({$userto->name}).");
                Template::AddBodyContent("<table class='formtable'><tr><th>Table</th><th>Field</th><th>Changes</th></tr>");
                foreach ($changes as $change) {
                    list($table, $field) = $change;
                    $affected = DB::Query("UPDATE `$table` SET `$field`='$useridto' WHERE `$field`='$useridfrom'")->rowCount();
                    Template::AddBodyContent("<tr><td>$table</td><td>$field</td><td>" . ($affected ? "<b>$affected row(s)</b> affected" : "0 rows affected") . "</td>");
                }
                Template::AddBodyContent("</table>");
                DB::Query("DELETE FROM users WHERE userid='$useridfrom'");
                Template::AddInlineNotice("User ID #$useridfrom ($userfrom->name) has been permanently deleted.");
            }
        }
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($this->container));
    }
}