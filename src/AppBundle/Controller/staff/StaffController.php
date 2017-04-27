<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use function ChapmanRadio\error;
use ChapmanRadio\Log;
use ChapmanRadio\Picker;
use ChapmanRadio\Request as ChapmanRadioRequest;
use ChapmanRadio\Season;
use ChapmanRadio\Template;
use ChapmanRadio\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class StaffController extends Controller
{
    /**
     * @Route("/staff/staff", name="staff_staff")
     */
    public function indexAction(Request $request)
    {
        define('PATH', '../');

        Template::SetPageTitle("Staff - Admin");
        Template::SetBodyHeading("Site Administration", "Staff Members");
        Template::RequireLogin("/staff/staff","Staff Resources", "staff");

        $season = Season::current();
        $seasonName = Season::name($season);

        Template::AddBodyContent("<div style='width:640px;margin:20px auto 10px;text-align:left;'>");

        if(isset($_POST['ADD_STAFF'])) {
            $userid = ChapmanRadioRequest::GetInteger('userid');
            if(!$userid) {
                Template::AddInlineError("please pick a user from the drop down menu, then try again");
            }
            else {
                $user = UserModel::FromId($userid);
                if(!$user){
                    Template::AddInlineError("There's a problem with that user. Please try again");
                }
                else{
                    $staffgroup = ChapmanRadioRequest::Get('staffgroup');
                    $staffposition = ChapmanRadioRequest::Get('staffposition');
                    $staffemail = ChapmanRadioRequest::Get('staffemail');
                    Log::StaffEvent("User {$user->name} (#$userid) added to staff as $staffposition ($staffgroup)");
                    DB::Query("UPDATE users SET type = :type, staffgroup = :sg, staffposition = :sp, staffemail = :se WHERE userid= :uid", array(
                        ":uid" => $userid,
                        ":type" => ChapmanRadioRequest::Get('type') == 'staff' ? 'staff' : 'dj',
                        ":sg" => $staffgroup,
                        ":se" => $staffemail,
                        ":sp" => $staffposition
                    ));
                    Template::AddInlineSuccess("You have added <b>{$user->name}</b> to staff");
                }
            }
        }

        if(isset($_POST['SAVE_STAFF'])) {
            $user = UserModel::FromId(ChapmanRadioRequest::GetInteger('userid'));
            if(!$user) error("that user doesnt exist");
            $staffgroup = ChapmanRadioRequest::Get('staffgroup');
            $staffposition = ChapmanRadioRequest::Get('staffposition');
            $staffemail = ChapmanRadioRequest::Get('staffemail');
            Log::StaffEvent("Staff Member {$user->name} (#{$user->id}) updated to $staffposition ($staffgroup)");
            DB::Query("UPDATE users SET type='staff', staffgroup = :sg, staffposition = :sp, staffemail = :se WHERE userid = :uid", array(
                ":uid" => $user->id,
                ":sg" => $staffgroup,
                ":sp" => $staffposition,
                ":se" => $staffemail));

            Template::AddInlineSuccess("You have updated staff member <b>{$user->name}</b>");
        }

        if(isset($_POST['DELETE_STAFF'])) {
            $user = UserModel::FromId(ChapmanRadioRequest::GetInteger('userid'));
            if(!$user) error("that user doesnt exist");
            Log::StaffEvent("User {$user->name} (#{$user->id}) removed from staff");
            DB::Query("UPDATE users SET type='dj', staffgroup='', staffposition='', staffemail='' WHERE userid = :uid", array(":uid" => $user->id));
            Template::AddInlineSuccess("You have removed <b>{$user->name}</b> from staff");
        }

        Template::AddBodyContent("<h3>New Staff Member</h3><form method='post'>
<p>To add a staff member, they must have a Chapman Radio Account (<a href='/join'>chapmanradio.com/join</a>) and they must be activated for $seasonName (<a href='/activate'>chapmanradio.com/activate</a>)</p>
<table class='formtable' cellspacing='0'>
<tr class='oddRow'>
	<td>User</td>
	<td>".Picker::Users()."</td>
</tr><tr class='evenRow'>
	<td>Group</td>
	<td>
		<input type='text' name='staffgroup' value=\"".ChapmanRadioRequest::GetAsPrintable('staffgroup')."\" />
		<br /><small style='color:#757575;width:220px;'>e.g. Management, Technical, Programming, Communications, Events - case insensitive</span>
	</td>
</tr><tr class='oddRow'>
	<td>Position</td>
	<td>
		<input type='text' name='staffposition' value=\"".ChapmanRadioRequest::GetAsPrintable('staffposition')."\" />
		<br /><small style='color:#757575;width:220px;'>e.g. General Manager, Engineer, Electronic Director, etc. - case insensitive</span>
	</td>
</tr><tr class='evenRow'>
	<td>Staff Email</td>
	<td>
		<input type='text' name='staffemail' value=\"".ChapmanRadioRequest::GetAsPrintable('staffemail')."\" />
	</td>
</tr><tr class='evenRow'>
	<td colspan='2' style='text-align:center;'>
		<input type='hidden' name='type' value='staff' />
		<input type='submit' name='ADD_STAFF' value='Add to Staff' />
	</td>
</tr>
</table></form>");

        Template::css("/legacy/css/formtable.css");

        Template::AddBodyContent("<h3>Current Staff Members</h3></div>");
        $users = UserModel::FromResults(DB::GetAll("SELECT * FROM users WHERE type='staff' ORDER BY staffgroup,fname"));
        $count = 0;
        $path = $request->getRequestUri();
        foreach($users as $user){
            $rowclass = ++$count % 2 == 0 ? "evenRow" : "oddRow";
            Template::AddBodyContent("<form method='post' action='$path'>
	<table class='formtable' cellspacing='0' style='width:auto;'><tr class='$rowclass'>
		<td><img src='{$user->img64}' alt='' /></td>
		<td><b>{$user->name}</b><br /><a href='/staff/users?input={$user->id}' target='_blank'>&raquo; More Info</a></td>
		<td>Group:<input type='text' name='staffgroup' value=\"".htmlspecialchars($user->staffgroup, ENT_COMPAT, "UTF-8")."\" /></td>
		<td>Position:<input type='text' name='staffposition' value=\"".htmlspecialchars($user->staffposition, ENT_COMPAT, "UTF-8")."\" /></td>
		<td>Email:<input type='text' name='staffemail' value=\"".htmlspecialchars($user->staffemail, ENT_COMPAT, "UTF-8")."\" /></td>
		<td><input type='hidden' name='userid' value='{$user->id}' />
		<input type='submit' name='SAVE_STAFF' value='Save' style='width:auto;' />
		<input type='submit' name='DELETE_STAFF' value='Remove from Staff' style='width:auto;' /></td>
	</tr></table>
	</form>");
        }

        Template::AddBodyContent("<br style='margin-bottom:20px' />");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }

}