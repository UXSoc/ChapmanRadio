<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/25/17
 * Time: 10:03 AM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Users;
use ChapmanRadio\DB;
use ChapmanRadio\Imaging;
use ChapmanRadio\Notify;
use ChapmanRadio\Request as ChapmanRadioRequest;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Template;
use ChapmanRadio\Uploader;
use ChapmanRadio\UserModel;
use ChapmanRadio\Util;
use ChapmanRadio\Validation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\User;


class AuthController extends Controller
{

    public function RegisterAction(Request $request)
    {
        $user = new Users();


        $form = $this->createFormBuilder($user)
            ->add('fname', TextType::class, array('label' => 'First Name'))
            ->add('lname', TextType::class, array('label' => 'Last Name'))
            ->add('email', TextType::class, array('label' => 'Email'))
            ->add('phone', TextType::class, array('label' => 'Phone'))
            ->add('studentid', TextType::class, array('label' => 'Student Id'))
            ->add('save', SubmitType::class, array('label' => 'Register'))
            ->getForm();

        if ($form->isSubmitted() && $form->isValid()) {
            $user_data = $form->getData();
        }

        $form->handleRequest($request);


        return $this->render('auth/login.html.twig', array("join_form" => $form->createView()));
    }

    /**
     * @Route("/activate", name="activate")
     */
    public function activateAction(Request $request)
    {

        define('PATH', '../');
        $season = Season::current();
        $seasonName = Season::name($season);

        Template::js("/legacy/js/parsley.min.js");

        Template::SetPageTitle("Active my Account");
        Template::SetBodyHeading("Activate my Account for $seasonName");
        Template::Bootstrap();

// Template::AddBodyContent("<div style='width:640px;margin:10px auto 60px;text-align:left;'><p>Activate or Renew your Chapman Radio Account.</p><p>Brand new and existing Chapman Radio accounts need to be activated or renewed every semester. We do this in order to keep information current. It's also important as a way to keep DJs up-to-date with new policies.</p>");

        /* Page flows:

         - Returning user tries to login and needs to be reactivated
         - New user clicks link in email and needs to be activated
         - New user finds their way here without code, ask for code
         */

        $user = Session::GetCurrentUser();
        $loggedin = ($user != NULL);

        if (!$loggedin) {
            $code = ChapmanRadioRequest::Get('code');
            if ($code) $user = UserModel::FromVerifyCode($code);
        }

        $errors = array();
        if ($user != NULL && isset($_POST['submitbutton'])) {
            // Check info fields
            if (isset($_POST['activate_info_form'])) {
                if (ChapmanRadioRequest::IsNull('fname'))
                    $errors["fname"] = "Please enter your first name.";
                if (ChapmanRadioRequest::IsNull('lname'))
                    $errors["lname"] = "Please enter your last name.";
                if (ChapmanRadioRequest::IsNull('email') || !preg_match("/^[A-Za-z0-9._%+-]+@(mail\.|)chapman\.edu$/", trim($_REQUEST['email'])))
                    $errors["email"] = "Please enter a valid Chapman University email address.";
                if (ChapmanRadioRequest::IsNull('classclub'))
                    $errors["classclub"] = "Please enter your class/club status.";
                if (ChapmanRadioRequest::IsNull('phone'))
                    $errors["phone"] = "Please enter your phone number.";
                if (ChapmanRadioRequest::IsNull('phone') || ChapmanRadioRequest::GetInteger('studentid') == 0)
                    $errors["studentid"] = "Please enter a valid Student ID Number.";
                if (ChapmanRadioRequest::IsNull('confirminfo'))
                    $errors["confirminfo"] = "Please confirm that you have read and updated this information.";
            }

            // Check password fields
            if (isset($_POST['activate_password_form'])) {
                if (ChapmanRadioRequest::IsNull('password'))
                    $errors["password"] = "Please enter a password.";
                if (ChapmanRadioRequest::IsNotNull('password') && ChapmanRadioRequest::Get('password') != ChapmanRadioRequest::Get('passwordconfirm')) {
                    $errors["password"] = "Please enter your password again";
                    $errors["passwordconfirm"] = "The passwords you entered didn't match.";
                }
            }

            // Submit info if no errors
            if (empty($errors) && isset($_POST['activate_info_form'])) {
                DB::Query("UPDATE users SET fname = :fname, lname = :lname, email = :email, phone = :phone, studentid = :studentid, classclub = :classclub WHERE userid = :userid", array(
                    ":fname" => $_REQUEST['fname'],
                    ":lname" => $_REQUEST['lname'],
                    ":email" => $_REQUEST['email'],
                    ":phone" => $_REQUEST['phone'],
                    ":studentid" => $_REQUEST['studentid'],
                    ":classclub" => $_REQUEST['classclub'],
                    ":userid" => $user->id
                ));
            }

            if (empty($errors) && isset($_POST['activate_password_form'])) {
                DB::Query("UPDATE users SET password = :password WHERE userid = :userid", array(
                    ":password" => Util::encrypt($_REQUEST['password']),
                    ":userid" => $user->id
                ));
            }

            if (empty($errors)) {
                $user->AddSeason($season);
                $user->Login();

                Template::AddBodyContent("<div style='width:570px;margin:20px auto 60px;text-align:left;'>
			<h3>Account Activated</h3>
			<p>Thanks, " . $user->fname . "</p>
			<p>Your account is now active for <b>$seasonName</b>.</p>
			<p>You can review the Chapman Radio policies any time at <a href='/policies'>chapmanradio.com/policies</a>.<br /></p>
			<h3>What next?</h3>
			<p>Do you want to <a href='/dj/apply'>apply for a show</a>?</p>
		</div>");

                Template::notify("Renewed", "Your account has been actived for $seasonName. Thanks!");
                return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
            }
        } else if (isset($_POST['submitbutton'])) {
            Template::AddBodyContent("<div style='color:#A00;text-align:center;margin:10px auto 20px'>Problem with page: Post handling method did not have access to user</div>");
        }

        if (!empty($errors)) {
            Template::AddBodyContent("<div style='color:#A00;text-align:center;margin:10px auto 20px'><b>Missing information.</b><br />Please fill in all required fields, then try re-submitting.</div>");
        }

        Template::AddBodyContent("<form method='post' action='$_SERVER[REQUEST_URI]' data-parsley-namespace='data-parsley-' data-parsley-validate>");

        /* What form content to display */
        if ($loggedin) {
            if ($user->IsActivated()) self::RenderNoActivation($user,$seasonName);
            else self::RenderReActivate($user,$seasonName);
        } else {
            if ($user != null) self::RenderActivate($user,$seasonName);
            else self::RenderUnknown($request);
        }

        Template::AddBodyContent("</form>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }


// for existing users who are already active
    function RenderNoActivation($user,$seasonName)
    {
        Template::AddBodyContent("<div class='gloss'><h3>Already Activated</h3><p>Hey " . $user->fname . ", you're already activated for $seasonName.</p><br /><p>Not " . $user->fname . "? <a href='/logout'>Logout</a></p></div>");
    }

// For new users, set a password and accept policies
    function RenderActivate($user,$seasonName)
    {
        self::RenderPasswordForm($user);
        self::RenderCheckboxForm($seasonName);
    }

// For existing users, check personal info and accept policies
    function RenderReActivate($user,$seasonName)
    {
        self::RenderInfoForm($user);
        self::RenderCheckboxForm($seasonName);
    }

// For requests with no user and no code, ask for a code or login
    function RenderUnknown(Request $request)
    {
        global $seasonName;

        $path = $request->getRequestUri();
        Template::AddBodyContent("
		<div style='text-align: left; width: 600px; margin: 10px auto; padding: 10px; border: 1px solid #CCC;'>
		<h3 style='margin-bottom:10px;'>New to Chapman Radio?</h3>
		<form method='get' action='$path'>");
        if (isset($_REQUEST['code'])) Template::AddBodyContent("<p style='color:red'>Invalid code. Please try again:</p>");
        Template::AddBodyContent("<p>Enter your activation code: <input name='code' value='' /> <input type='submit' value='Activate' />
		</form>
		</div>
		<div style='text-align: left; width: 600px; margin: 10px auto; padding: 10px; border: 1px solid #CCC;'>
		<h3 style='margin-bottom:10px;'>Already a Member?</h3>
		<p>To renew an existing account for <b>$seasonName</b>, please <a href='/login'>log in</a>.</p>
		</div>
		");
    }

    function RenderInfoForm($user)
    {
        Template::AddBodyContent("
		<div style='text-align: left; width: 600px; margin: 10px auto; padding: 10px; border: 1px solid #CCC;'>
		<h3>Update My Information</h3>
		<p>Welcome back " . $user->fname . "! Does everything still look correct here?</p><br />
		<div class='zeus-form'>
			<input type='hidden' name='activate_info_form' value='1' />
			<div>
				" . self::GetError('fname') . "
				<span>First Name</span>
				<input type='text' name='fname'  value=\"" . ChapmanRadioRequest::GetAsPrintable('fname', $user->fname) . "\" />
			</div>
			<div>
				" . self::GetError('lname') . "
				<span>Last Name</span>
				<input type='text' name='lname' value=\"" . ChapmanRadioRequest::GetAsPrintable('lname', $user->lname) . "\" />
			</div>
			<div>
				" . self::GetError('email') . "
				<span>Chapman Email</span>
				<input type='text' name='email' value=\"" . ChapmanRadioRequest::GetAsPrintable('email', $user->email) . "\" />
			</div>
			<div>
				" . self::GetError('classclub') . "
				<span>Class or Club</span>
				<select name='classclub'>
					<option " . ((ChapmanRadioRequest::Get('classclub', $user->classclub) == "class") ? "selected" : "") . " value='class'>Class</option>
					<option " . ((ChapmanRadioRequest::Get('classclub', $user->classclub) == "club") ? "selected" : "") . " value='club'>Club</option>
				</select>
			</div>
			<div>
				" . self::GetError('phone') . "
				<span>Phone</span>
				<input type='text' name='phone' value=\"" . ChapmanRadioRequest::GetAsPrintable('phone', $user->phone) . "\" />
			</div>
			<div>
				" . self::GetError('studentid') . "
				<span>Student ID</span>
				<input type='text' name='studentid' value=\"" . ChapmanRadioRequest::GetAsPrintable('studentid', $user->studentid) . "\" />
			</div>
			<div>
				<span style='color:#757575;'>This is your current profile picture. After renewing your account, you'll be able to change your picture from your Profile page</span>
				<img src='" . $user->img192 . "' alt='' />
			</div>
			<div>
				" . self::GetError('confirminfo') . "
				<input type='checkbox' id='confirminfo' name='confirminfo' data-parsley-required='true' value='1' style='width:auto;' />
				<label for='confirminfo'> Yes, this information is correct.</label>
			</div>
		</div>
		</div>");
    }

    function RenderPasswordForm($user)
    {
        Template::AddBodyContent("
		<div style='text-align: left; width: 600px; margin: 10px auto; padding: 10px; border: 1px solid #CCC;'>
		<h3>Chapman Radio Account Password</h3>");

        if ($user->fbid) Template::AddBodyContent("<p>Your Chapman Radio Account is integrated with Facebook. You don't need to set a password, just click the blue Facebook button on the login page. We never see your Facebook password.</p><br />");

        else Template::AddBodyContent("<p>Your Chapman Radio Account is not currently integrated with Facebook. You need to set a password now to login, but you can also add Facebook login later if you would like so you don't need to remember this password.</p><br />
	<div class='zeus-form'>
		<input type='hidden' name='activate_password_form' value='1' />
		<div>
			" . self::GetError('password') . "
			<span>Chapman Radio Password</span>
			<input type='password' name='password' id='join-password' value='' data-parsley-required='true' data-parsley-minlength='8' />
		</div>
		<div>
			" . self::GetError('passwordconfirm') . "
			<span>Re-enter Password</span>
			<input type='password' name='passwordconfirm' value='' data-parsley-required='true' data-parsley-equalto='#join-password' />
		</div>
	</div>");

        //<tr class='oddRow'><td colspan='2' style='text-align:center;'>Facebook Connect is <b>".($user['fbid']?"On":"Off")."</b> for my account.".($user['fbid']?"<br /><table style='margin:auto;'><tr><td>My Chapman Radio Account<br /><img src='$user[icon]' alt='' /></td><td style='vertical-align:middle;'><img src='/img/arrows/double.png' alt='' /><td>My Facebook Account<br /><img src='https://graph.facebook.com/$user[fbid]/picture' /></td></tr></table>":"")."</td></tr>

        Template::AddBodyContent("</div>");
    }

    function RenderCheckboxForm($seasonName)
    {
        Template::AddBodyContent("
		<div style='text-align: left; width: 600px; margin: 10px auto; padding: 10px; border: 1px solid #CCC;'>
		<h3>$seasonName Policies</h3>
		<p>To activate your Chapman Radio account, you must confirm that you will adhere to Chapman Radio's policies and procedures.</p>
		<p>Please read each criteria &amp; mark the checkbox to confirm that you understand.</p>
		<ol id='activate-checkboxes' style='list-style-type:none;line-height:21px;'>" .
            self::cbox("My account is subject to the <a href='/policies' target='_blank'>Chapman Radio Policies</a>.") .
            self::cbox("As a member of Chapman Radio, I will adhere to the <b>Course Syllabus</b>, which is always available at <a href='/syllabus' target='_blank'>chapmanradio.com/syllabus</a>. This applies to all members, in both the class and club.") .
            self::cbox("I understand the <b>3 Strikes Policy</b>: If I earn 3 strikes, combined from all of my shows, then any all of my shows will be cancelled.") .
            self::cbox("I understand that there are <b>3 ways to earn Strikes</b>: Missing a show, missing 2 workshop meetings, or being late 3 times to shows or meetings.") .
            self::cbox("I understand I will be emailed by Chapman Radio when I receive a Strike, and it is my responsibility to contact Staff if I think there was an error or I have a question. I also know I can check my attendance status online at any time.") .
            "<br />" .
            self::cbox("I have read and understand the Chapman University <a href='http://www.chapman.edu/campus-services/information-systems/security/acceptable-use-policy.aspx' target='_blank'>Acceptable Use Policy</a>. The use of any Chapman University or Chapman Radio services or equipment is subject to this policy.") .
            self::cbox("I have read and understand Chapman University's <a href='http://www.chapman.edu/campus-services/information-systems/security/dmca.aspx' target='_blank'>Digital Millennium Copyright Act (DMCA) Policy</a>. I understand how the provisions of the DMCA could affect my show. I understand that I am completely responsible, equally along with my co-djs, for all content that is broadcast during my show.") .
            self::cbox("I understand that Chapman University and Chapman Radio do not actively monitor my show and cannot be responsible for the content that is broadcast. I understand that responsibility for my show is shared between all registered DJs on the show, and shall notify Chapman Radio of any changes to the DJs responsible for my show.") .
            "</ol>");
        Template::script("function checkcboxes(){ var problem = false; $('#activate-checkboxes input[type=checkbox]').each(function(i, e){ if(!$(e).is(':checked')) { $('#submitbutton').prop('disabled', true); problem = true; return; } }); if(!problem) $('#submitbutton').prop('disabled', false); }");
        Template::AddBodyContent("<p style='text-align:center;margin-top:20px;'><input type='submit' name='submitbutton' id='submitbutton' value=' Activate my Account ' /><script>checkcboxes();</script></p></div>");
    }

    function GetError($key)
    {
        global $errors;
        if (isset($errors[$key]) && $errors[$key] != "") return "<span class='error'>" . $errors[$key] . "</span>";
        return "";
    }

    function cbox($msg)
    {
        if (!isset($_GLOBALS['cboxCount'])) $_GLOBALS['cboxCount'] = 0;
        global $cboxCount;
        $cboxCount++;
        $checked = isset($_REQUEST['accept' . $cboxCount]) ? "checked='checked'" : "";
        return "<li><label for='accept$cboxCount'><input type='checkbox' name='accept$cboxCount' value='$cboxCount' id='accept$cboxCount' $checked onchange='checkcboxes();' /> $msg</label></li>";
    }

    /**
     * @Route("/join", name="join")
     */
    public function RegistrationAction(Request $request)
    {
        define('PATH', '../');
        require_once "./../inc/facebook.php";


        Template::SetPageTitle("Join");
        Template::Bootstrap();

        Template::css("/legacy/css/formtable.css");
        Template::js("/legacy/js/parsley.min.js");

        $user = Session::GetCurrentUser();
        if (Session::HasUser()) {
            return new \Symfony\Component\HttpFoundation\Response(Template::Finalize("<div style='width:537px;margin:10px auto;text-align:left;'>
		<p>Hello, " . $user->fname . ".</p>
		<p>You already have an account with Chapman Radio, so you don't need to apply for one.</p>
		<p>Go to <a href='/dj'>my account</a>.</p><p><p>Not " . $user->fname . "? <a href='/logout?source=join'>Logout</a></p>
		</div>"));
        }

        if (isset($me) && $me) {
            $temp = DB::GetFirst("SELECT * FROM users WHERE fbid = :fbid", array(":fbid" => $me['id']));
            if ($temp) {
                $logout = $facebook->getLogoutUrl();
                return new \Symfony\Component\HttpFoundation\Response(Template::Finalize("<div class='gloss'>
			<h3>Account in Use</h3>
			<p>Sorry, but $me[first_name]'s facebook account is already in use at Chapman Radio.</p>
			<p><a href='/login'>Login to Chapman Radio</a></p>
			<p><a href='$logout'>Logout of Facebook</a></p>
			</div>"));
            }
        }

        $uploadedfile = NULL;
        if (isset($_POST['JOIN'])) $error = self::TryJoin();

        Template::SetBodyHeading("Chapman Radio", "Join Us");
        Template::AddBodyContent("<p style='width:537px;margin:10px auto;text-align:left;'>Interested in becoming a DJ? Get an account with Chapman Radio and you'll be able to broadcast on the hottest college radio station!</p>");

        $join = "<form method='post' action='' enctype='multipart/form-data' data-parsley-namespace='data-parsley-' data-parsley-validate>
	<table style='margin:0 auto 10px;border:1px solid #AAA;text-align:left;' cellspacing='0' class='formtable'>
	<tr><td colspan='2' style='text-align:center;'>";

        if (isset($error) && $error != "") $join .= "<div class='couju-error'>$error</div>";
        if (!$me) $join .= "<a class='loginButton__facebook'>Use my Facebook Account</a><div style='color: #999; font-size: 13px;'>We just use Facebook for login. No posting or spamming.</div>";
        else $join .= "Using $me[name]'s Facebook Account. <a href='" . $facebook->getLogoutUrl() . "'>Click here to Logout.</a>";

        $me_email = ($me && isset($me['email'])) ? $me['email'] : "";

// fname
        $join .= "<tr style='width:200px;'>
	<td>First Name</td>
	<td style='width:300px;'>" . (($me) ?
                "<input type='hidden' name='fname' value=\"$me[first_name]\" /><b>$me[first_name]</b>" :
                "<input type='text' data-parsley-required='true' name='fname' value=\"" . ChapmanRadioRequest::GetAsPrintable('fname', '') . "\" />") .
            "</td></tr>";

// lname
        $join .= "<tr style='width:200px;'>
	<td>Last Name</td>
	<td style='width:300px;'>" . (($me) ?
                "<input type='hidden' name='lname' value=\"$me[last_name]\" /><b>$me[last_name]</b>" :
                "<input type='text' data-parsley-required='true' name='lname' value=\"" . ChapmanRadioRequest::GetAsPrintable('lname', '') . "\" />") .
            "</td></tr>";

// email
        $join .= "<tr>
	<td>Chapman Email</td>
	<td><input type='text' id='email' name='email' data-parsley-required='true' data-parsley-type='email' value=\"" . ChapmanRadioRequest::GetAsPrintable('email', $me_email) . "\" />
	</td></tr>";

        $join .= "<tr>
	<td>Confirm Email</td>
	<td><input type='text' name='emailconfirm' value=\"" . ChapmanRadioRequest::GetAsPrintable('emailconfirm', $me_email) . "\" />
	</td></tr>";

// phone number
        $join .= "<tr>
	<td>Phone Number</td>
	<td><input name='phone' data-parsley-required='true' data-parsley-type='phone' value=\"" . ChapmanRadioRequest::GetAsPrintable('phone', '') . "\" /><br />
	<span style='color:#757575'>We'll usually just email you, but we ask for your phone number just in case.</span>
	</td></tr>";

// student id
        $join .= "<tr>
	<td>Student ID</td>
	<td><input name='studentid' data-parsley-required='true' data-parsley-type='number' value=\"" . ChapmanRadioRequest::GetAsPrintable('studentid', '') . "\" /><br />
	<span style='color:#757575'>Your Chapman Student ID Number is used to give you access to the studio.</span>
	</td></tr>";

// upload
        $join .= "<tr>
	<td>Profile Picture</td>
	<td><input type='file' name='upload' /><br />
	<span style='color:#757575'>We'll use the photo you upload on your Show Profile page, so keep in mind that it will be publically visible.</span><br />";

        if ($uploadedfile) {
            $checked = "checked='checked'";
            $join .= "<table style='margin:auto;'><tr><td><input type='checkbox' name='useupload' id='useupload' value='$uploadedfile' $checked style='margin:8px;width:auto;' /></td><td><label for='useupload'></label></td><td><label for='useupload'><img src='$uploadedfile' /></label></td></tr></table>";
        } else if ($me) {
            $fbpic = "https://graph.facebook.com/$me[id]/picture";
            $checked = isset($_REQUEST['usefbpic']) ? "checked='checked'" : "";
            $join .= "<table style='margin:auto;'><tr><td><input type='checkbox' name='usefbpic' id='usefbpic' value='$fbpic' $checked style='margin:8px;width:auto;' /></td><td><label for='usefbpic'><img src='$fbpic' alt='' /></label></td><td><label for='usefbpic'>Use my Facebook<br />Profile Picture</label></td></tr></table>";
        }
        $join .= "</td></tr>";

        $join .= "<tr><td colspan='2' style='text-align:center;'><input type='submit' name='JOIN' value=' Submit ' /></td></tr>";
        $join .= "</table></form>";

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($join));
    }

    function TryJoin()
    {
        global $uploadedfile, $me;

        $v = new Validation();
        $v->Required('fname', "Please enter your first name");
        $v->Required('lname', "Please enter your last name");
        $v->Required('email', "Please enter a valid Chapman University email address");
        $v->EmailChapman('email', "Please enter a valid Chapman University email address");
        $v->EmailNotUsed('email', "This email is in use.<br />Please <a href='/login'>login</a> or use a different email address.");
        $v->Matches('email', 'emailconfirm', "Please check your email to make sure it matches");
        $v->Required('phone', "Please enter your phone number");
        $v->Integer('studentid', "Please enter a valid Student ID");

        if ($v->HasErrors()) return $v->FirstError();


        if (isset($_REQUEST['useupload'])) {
            $uploadedfile = $_REQUEST['useupload'];
        } else if (isset($_REQUEST['usefbpic'])) {
            $uploadedfile = Imaging::DownloadRemoteImage("http://graph.facebook.com/" . $me['id'] . "/picture?width=310&height=310");
        } else if (isset($_FILES['upload'])) {
            try {
                $uploadedfile = Uploader::UploadFileByIdentifier('upload');
            } catch (\Exception $e) {
                $uploadedfile = NULL;
                return $e->GetMessage();
            }
        } else {
            return "Please upload a picture of yourself.";
        }

        // no errors, let's add this user
        $fbid = (isset($me) && $me) ? $me['id'] : 0;

        $email = ChapmanRadioRequest::Get('email');
        $fname = ucfirst(ChapmanRadioRequest::Get('fname'));
        $lname = ucfirst(ChapmanRadioRequest::Get('lname'));
        $vcode = uniqid("a");

        $userid = DB::Insert("users", array(
            "fbid" => $fbid,
            "email" => $email,
            "fname" => $fname,
            "lname" => $lname,
            "name" => "$fname $lname",
            "phone" => ChapmanRadioRequest::Get('phone'),
            "studentid" => ChapmanRadioRequest::Get('studentid'),
            "verifycode" => $vcode,
            //modify
            "djname" => "",
            "gender" => "",
            "seasons" => "",
            "lastlogin" => new \DateTime("now"),
            "lastip" => "",
            "password" => "",
            "staffgroup" => "",
            "staffposition" => "",
            "staffemail" => "",
            "quizpassedseasons" => "",
            "revisionkey" => ""));

        // now moved the uploaded file from /tmp to /content
        $userModel = UserModel::FromId($userid);
        Uploader::PostProcessModelUpload($userModel, $uploadedfile);

        $fname = stripslashes($fname);
        $email = stripslashes($email);
        $vurl = "https://chapmanradio.com/activate?code=$vcode";

        Notify::mail($email, "Welcome to Chapman Radio", "
		<h2>Welcome to Chapman Radio</h2>
		<p>Hello, <b>$fname</b>.</p>
		<p>Thanks for joining Chapman Radio!</p>
		<p>To get started, go to <a href='$vurl'>$vurl</a> to activate your account.</p>
		<p>We're glad to have you in our community!</p>");

        Template::SetBodyHeading("Join Chapman Radio", "Account Created");
        Template::Finalize("<div style='width:480px;margin:20px auto;text-align:left;'>
		<h3>Welcome to Chapman Radio</h3>
		<p>Thanks for joining Chapman Radio, $fname!</p>
		<p>We just sent a confirmation email to <b>$email</b>. Follow the link in that email to activate your account.</p>
		<p><strong style='color:red'>Be sure to check your spam folder for the confirmation email.</strong></p>
		</div>");
        return new \Symfony\Component\HttpFoundation\Response("");
    }


    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        define('PATH', '../');

        Template::SetPageTitle("Login");
        Template::SetBodyHeading("Chapman Radio", "Login");
        Template::Bootstrap();

        if (Session::HasUser()) {
            $user = Session::GetCurrentUser();
            Template::SetBodyHeading("Chapman Radio", "Login");
            return new \Symfony\Component\HttpFoundation\Response(Template::Finalize("<div style='width:570px;margin:auto;text-align:left;'>
		<h3>Already Logged In</h3><br />
		<p>Hello, {$user->name}. You're already logged in.</p>
		<p>Go to my <a href='/dj'>DJ page</a>.</p>
		<p>Not {$user->fname}? <a href='/logout'>Logout</a><br /></p>
		</div>"));
        }

// check for a facebook login
        if (isset($me) && $me) {
            $email = (isset($me['email'])) ? $me['email'] : "";
            $row = DB::GetFirst("SELECT fname, email, userid FROM users WHERE fbid='$fbid' LIMIT 0,1");
            $userid = 0;
            if ($row) {
                // logged in with facebook before
                $userid = $row['userid'];
            } else {
                // never logged in with facebook before
                $row = DB::GetFirst("SELECT fname,userid FROM users WHERE email = :email LIMIT 0,1", array(":email" => $email));
                if ($email != "" && $row) {
                    // logged in with an email address before
                    $userid = $row['userid'];
                    DB::Query("UPDATE users SET fbid=$fbid WHERE userid=$userid");
                } else {
                    // never logged in w/ email address before
                    $logoutUrl = $facebook->getLogoutUrl();
                    Template::SetBodyHeading("Chapman Radio", "New Account");
                    return new \Symfony\Component\HttpFoundation\Response(Template::Finalize("<div class='leftcontent'><p>Hello, $me[first_name].</p><p>You're trying to log into Chapman Radio with your Facebook account. Unfortunately, we don't have any Chapman Radio accounts associated with this Facebook account.</p><p>If you <b>already have</b> a Chapman Radio account, please <a href='$logoutUrl'>logout of Facebook</a>, then try to login again.</p><p>If you <b>don't have</b> a Chapman Radio account, you can <a href='/join'>Join Chapman Radio</a>.</div>"));
                }
            }
            Session::Login($userid);
        }

// okay, there is no facebook login to do

// process
        $loginERROR = "";
        if (isset($_POST['USER_LOGIN'])) {
            $email = ChapmanRadioRequest::Get('email');
            $password = ChapmanRadioRequest::Get('password');
            if (!$email && !$password) $loginERROR = "Please enter your email and password:";
            else if (!$email) $loginERROR = "Please enter your email address:";
            else if (!$password) $loginERROR = "Please enter your password:";
            else if (!preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}\$/", $email)) $loginERROR = "It looks like <b>" . stripslashes($email) . "</b> is an invalid email address. Please try again:";
            else {
                $row = DB::GetFirst("SELECT userid,fname,password FROM users WHERE email = :email", array(":email" => $email));
                if (!$row) $loginERROR = "Sorry, but that email isn't registered with Chapman Radio. Would you like to <a href='/join'>Join</a>?";
                else {
                    $correctPassword = Util::decrypt($row['password']);
                    if ($password == $correctPassword) Session::Login($row['userid']);
                    else if ($correctPassword == "") $loginERROR = "Sorry, but this account is not setup with a password. You must use Facebook to login.";
                    else $loginERROR = "Sorry, that password was incorrect.";
                }
            }
        }

        if ($loginERROR) $loginERROR = "<p style='color:red;'>$loginERROR</p>";

// output

// style
# Template::style(".loginContainer .gloss {width:auto;margin:10px;} .loginContainer p { font-size:13px; } .loginContainer { margin:10px auto; } .loginContainer td {width:300px;text-align:left;padding:0 30px;} .oldfashioned td {width:auto;} ");

// output the login page
        Template::SetBodyHeading("Chapman Radio", "Login");
        Template::AddBodyContent("<p style='color:#484848;padding:10px; text-align: center;'>");

        if (isset($_SESSION['redirectPageName'])) Template::AddBodyContent("Please login to view <b>{$_SESSION['redirectPageName']}</b>.<br />");

        $email_string = ChapmanRadioRequest::Get('email');

        Template::AddBodyContent("If you don't have an account, you should <a href='/join'>join Chapman Radio</a>.</p>
	<div class='cr-login-left'>
		<h3>Use Facebook</h3>
		<p>Login instantly and securely with your Facebook account. We never see your password or detailed personal info, and we can't post to your timeline.</p>
		<div style='text-align:center;padding:12px 6px;'>
			<a class='loginButton__facebook'>Login with Facebook</a>
		</div>
	</div>
	<div class='cr-login-right'>
		<h3>Old-fashioned</h3>
		<p>If you don't have a Facebook account, you can still login using your Chapman email address.</p>
		$loginERROR
		
		<form class='form-horizontal' role='form' method='post'>

		<div class='form-group'>
		<label for='email' class='col-sm-2 control-label'>Email</label>
		<div class='col-sm-10'>
		<input type='email' class='form-control' id='email' name='email' placeholder='Email' value='{$email_string}'>
		</div>
		</div>

		<div class='form-group'>
		<label for='password' class='col-sm-2 control-label'>Password</label>
		<div class='col-sm-10'>
		<input type='password' name='password' id='password' class='form-control' placeholder='Password' >
		</div>
		</div>

		<div class='form-group'>
		<div class='col-sm-offset-2 col-sm-10'>
		<button type='submit' name='USER_LOGIN' class='btn btn-default'>Login</button>
		<a class='pull-right' href='/resetpassword'>Forgot your password?</a>
		</div>
		</div>
		</form>

	</div>
	<br class='_clear' />");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}