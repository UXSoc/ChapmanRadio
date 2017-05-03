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
use ChapmanRadio\Imaging;
use ChapmanRadio\Season;
use ChapmanRadio\Request as ChapmanRadioRequest;
use ChapmanRadio\Session;
use ChapmanRadio\Template;
use ChapmanRadio\Uploader;
use ChapmanRadio\UserModel;
use ChapmanRadio\Util;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{

    /**
     * @Route("/dj/profile", name="dj_profile")
     */
    public function indexAction(Request $request)
    {
        define('PATH', '../');
        require_once PATH ."inc/facebook.php";


        Template::SetPageTitle("My Profile");
        Template::SetBodyHeading("DJ Resources", "My Profile");
       // Template::RequireLogin("/dj/profile","DJ Resources");
        Template::Css("/legacy/css/formtable.css");

        Template::AddBodyContent("<p style='margin:10px auto;'>You can edit your information, change your password, or update your profile picture.</p>");

        $fields = array(/*
	"First Name" => "fname",
	"Last Name" => "lname",*/
            "Full Name" => "name",
            "DJ Name" => "djname",
            "Email" => "email",
            "Student ID #" => "studentid",
            "Phone Number" => "phone",
            "Pet Preference" => "petpreference",
            "Class or Club" => "classclub",
            "submit"
        );
        $prefix = "update-profile-";

        $userid = Session::GetCurrentUserID();
        $user = UserModel::FromId($this->getUser()->getId());
        if(!$user) throw new Exception('Non existent user accessed user page');

// process a save request

        if(isset($_POST['SAVE_PROFILE'])) {
            $req = array();
            foreach($fields as $eng => $field) {
                if(is_numeric($eng)) continue;
                if(!isset($_REQUEST[$prefix.$field])) Template::Error($this->container,"Missing information: Please go back and enter <b>$eng</b>");
                else if($field != 'name') $user->Update($field, ChapmanRadioRequest::Get($prefix.$field));
            }

            Template::AddBodyContent("<div class='gloss' style='width:360px;margin:10px auto;'><p style='color:green'>Updated</p><p>You've successfully updated your profile information.</p></div>");
        }

        if(isset($_POST['CHANGE_PASSWORD'])) {
            extract(DB::GetFirst("SELECT password AS correctpassword FROM users WHERE userid='$userid'"));
            $oldpassword = @$_REQUEST['password'] or "";
            $password = @$_REQUEST['password'] or "";
            $passwordconfirm = @$_REQUEST['passwordconfirm'] or "";
            if(!$password || !$passwordconfirm) {
                Template::AddBodyContent(self::notify("Please enter a new password and confirm it.","#A00"));
            }
            else if($password != $passwordconfirm) {
                Template::AddBodyContent(self::notify("The passwords you entered didn't match","#A00"));
            }
            else if($oldpassword != $correctpassword && Util::encrypt($oldpassword) != $correctpassword) {
                Template::AddBodyContent(self::notify("The old password your entered was incorrect. ","#A00"));
            }
            else {
                DB::Query("UPDATE users SET password = :pwd WHERE userid = :uid", array(":uid" => $userid, ":pwd" => Util::encrypt($password)));
                Template::AddBodyContent(self::notify("Your password has been updated.","#090"));
            }
        }

        if(isset($_POST['USE_FACEBOOK_PIC'])){
            if($user->fbid == 0) throw new Exception('Non-facebook connected user attempted to user facebook profile picture');
            $file = Imaging::DownloadRemoteImage("http://graph.facebook.com/{$user->fbid}/picture?width=310&height=310");
            Uploader::PostProcessModelUpload($user, $file);
            Template::AddBodyContent("<div class='gloss' style='width:360px;margin:10px auto;'><p style='color:green'>Updated</p><p><img src='".$user->img50."' alt='' style='float:left;' />Your profile picture has been synced with your Facebook account.</p><br style='clear:both' /></div>");
        }

// Handle image uploads where JS failed
        try{
            if(Uploader::HandleModel($user) !== NULL){
                Template::AddBodyContent("<div class='gloss' style='width:360px;margin:10px auto;'><p style='color:green'>Uploaded</p><p><img src='".$user->img50."' alt='' style='float:left;' />You've successfully uploaded a new profile picture.</p><br style='clear:both' /></div>");
            }
        }
        catch(Exception $e){
            Template::AddBodyContent(self::notify($e->GetMessage(),"#A00"));
        }

// display information
        Template::AddBodyContent("<div class='leftcontent'><h3>About You</h3>");
        Template::AddBodyContent("<form method='post' action=''><table class='formtable' cellspacing='0' cellpadding='0' style='margin:10px auto;'>");
        $count = 0;
        foreach($fields as $eng => $field) {
            $rowclass = ++ $count % 2 == 0 ? 'evenRow' : 'oddRow';
            switch($field) {
                case 'fname':
                case 'lname':
                case 'name':
                    Template::AddBodyContent("<tr class='$rowclass'><td>$eng</td><td>".$user->$field."<input type='hidden' name='$prefix$field' value=\"".htmlentities($user->$field)."\" /></tr>");
                    break;
                case 'petpreference':
                    $pref = $user->$field;
                    Template::AddBodyContent("<tr class='$rowclass'><td>$eng</td><td><select name='$prefix$field'>");
                    $opts = [ 'none' => 'No Preference', 'cat' => 'Cats', 'dog' => 'Dogs', 'pet' => 'Something Else ...' ];
                    foreach($opts as $key => $disp) Template::AddBodyContent("<option value='{$key}' ".($key==$pref?"selected":"").">{$disp}</option>");
                    Template::AddBodyContent("</select></tr>");
                    break;
                case 'classclub':
                    $checked = "checked='checked'";
                    switch($user->$field) {
                        case 'class': $classchecked = $checked; $clubchecked = ""; break;
                        case 'club': $classchecked = ""; $clubchecked = $checked; break;
                        default: $classchecked = ""; $clubchecked = ""; break;
                    }
                    Template::AddBodyContent("<tr class='$rowclass'><td>$eng</td><td> <input type='radio' name='{$prefix}classclub' value='class' style='width:auto;' $classchecked /> I'm enrolled in the <b>class</b> for credit<br /><input type='radio' name='{$prefix}classclub' value='club' style='width:auto;' $clubchecked /> I'm doing the <b>club</b> for fun.</td></tr>");
                    break;
                case 'submit':
                    Template::AddBodyContent("<tr class='$rowclass'><td colspan='2' style='text-align:center;'><input type='submit' name='SAVE_PROFILE' value=' Save ' /></td></tr>");
                    break;
                default:
                    Template::AddBodyContent("<tr class='$rowclass'><td>$eng</td><td><input name='$prefix$field' value=\"".htmlentities($user->$field)."\" /></tr>");
            }
        }

        $path = $request->getRequestUri();
        Template::AddBodyContent("</table></form>

<h3>My Password</h3>
<form method='post' action='$path'>
<table cellspacing='0' class='formtable'>
	<tr class='oddRow'><td style='text-align:center;' colspan='2'>Change Password</td></tr>
	<tr class='evenRow'><td>Old Password</td><td><input type='password' name='oldpassword' value='' /></td></tr>
	<tr class='oddRow'><td>New Password</td><td><input type='password' name='password' value='' /></td></tr>
	<tr class='evenRow'><td>Confirm New Password</td><td><input type='password' name='passwordconfirm' value='' /></td></tr>
	<tr class='oddRow'><td style='text-align:center;' colspan='2'>
		<input type='submit' name='CHANGE_PASSWORD' value='Change Password' />
	</td></tr>
</table>
</form>

");

        Template::AddBodyContent("<br style='margin:13px;' />
<h3>Profile Picture</h3>

<table style='margin:10px auto;'><tr><td style='text-align:center;'>
	<div class='gloss' style='margin:10px auto;width:350px;text-align:center;'>
	<img src='".$user->img310."' alt='' style='margin:20px auto;' />
	</div>
</td><td style='padding-left:40px;'>
	
<h2>Upload New Picture</h2>
<div class='gloss'>".Uploader::RenderModel($user)."</div>

<br style='margin:13px;' />

<h2>Use Facebook Picture</h2>");
        if($user->fbid) Template::AddBodyContent("<form method='post' action=''><table class='formtable' cellspacing='0' cellpadding='0' style='margin:10px auto;text-align:center;width:300px;'>
	<tr class='oddRow'><td>My Facebook Profile Icon</td><td><img src='http://graph.facebook.com/".$user->fbid."/picture' /></td>
	<tr class='evenRow'><td colspan='2'><input type='submit' name='USE_FACEBOOK_PIC' value=' Use this picture ' /></td></tr>
</table></form>");

        else if(!$me) Template::AddBodyContent("<div class='gloss' style='text-align:center;padding:18px 0;width:300px;'><a class='loginButton__facebook'>Login with Facebook</a></div>");

        else Template::AddBodyContent("<div class='couju-notice'>You're signed to Facebook, but your profile isn't linked to your profile for Chapman Radio.<br /><br />Email webmaster@chapmanradio.com if you want to connect your accounts.</div>");

        Template::AddBodyContent("</td></tr></table>");

// finish up
        Template::AddBodyContent("</div>");
        return Template::Finalize($this->container);

    }

    function notify($msg, $color='#090') {
        return "<div class='gloss' style='width:360px;margin:10px auto;'><p style='color:$color'>$msg</p></div>";
    }

}