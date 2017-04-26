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

    /**
     * @Route("/join", name="join")
     */
    public  function  RegisterAction(Request $request)
    {
        $user = new Users();


        $form = $this->createFormBuilder($user)
            ->add('fname', TextType::class, array('label' => 'First Name'))
            ->add('lname', TextType::class , array('label' => 'Last Name'))
            ->add('email', TextType::class, array('label' => 'Email'))
            ->add('phone', TextType::class, array('label' => 'Phone'))
            ->add('studentid', TextType::class, array('label' => 'Student Id'))
            ->add('save', SubmitType::class, array('label' => 'Register'))
            ->getForm();

        if ($form->isSubmitted() && $form->isValid()) {
            $user_data = $form->getData();
        }

        $form->handleRequest($request);


        return $this->render('auth/login.html.twig',array("join_form" => $form->createView()));
    }

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
            return new \Symfony\Component\HttpFoundation\Response( Template::Finalize("<div style='width:537px;margin:10px auto;text-align:left;'>
		<p>Hello, " . $user->fname . ".</p>
		<p>You already have an account with Chapman Radio, so you don't need to apply for one.</p>
		<p>Go to <a href='/dj'>my account</a>.</p><p><p>Not " . $user->fname . "? <a href='/logout?source=join'>Logout</a></p>
		</div>"));
        }

        if (isset($me) && $me) {
            $temp = DB::GetFirst("SELECT * FROM users WHERE fbid = :fbid", array(":fbid" => $me['id']));
            if ($temp) {
                $logout = $facebook->getLogoutUrl();
                return new \Symfony\Component\HttpFoundation\Response( Template::Finalize("<div class='gloss'>
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

        return new \Symfony\Component\HttpFoundation\Response( Template::Finalize($join));
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
            "verifycode" => $vcode));
//        "djname" => "",
//            "gender" => "",
//            "seasons" => "",
//            "lastlogin"=> new \DateTime("now"),
//            "lastip" => "",
//            "password"=>"",
//            "staffgroup"=>"",
//            "staffposition"=>"",
//            "staffemail" => "",
//            "quizpassedseasons"=>"",
//            "revisionkey"=>""

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
        return new \Symfony\Component\HttpFoundation\Response(  "");
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

        if(Session::HasUser()) {
            $user = Session::GetCurrentUser();
            Template::SetBodyHeading("Chapman Radio", "Login");
            return new \Symfony\Component\HttpFoundation\Response( Template::Finalize("<div style='width:570px;margin:auto;text-align:left;'>
		<h3>Already Logged In</h3><br />
		<p>Hello, {$user->name}. You're already logged in.</p>
		<p>Go to my <a href='/dj'>DJ page</a>.</p>
		<p>Not {$user->fname}? <a href='/logout'>Logout</a><br /></p>
		</div>"));
        }

// check for a facebook login
        if(isset($me) && $me) {
            $email = (isset($me['email'])) ? $me['email'] : "";
            $row = DB::GetFirst("SELECT fname, email, userid FROM users WHERE fbid='$fbid' LIMIT 0,1");
            $userid = 0;
            if($row) {
                // logged in with facebook before
                $userid = $row['userid'];
            }
            else {
                // never logged in with facebook before
                $row = DB::GetFirst("SELECT fname,userid FROM users WHERE email = :email LIMIT 0,1", array(":email" => $email));
                if($email != "" && $row) {
                    // logged in with an email address before
                    $userid = $row['userid'];
                    DB::Query("UPDATE users SET fbid=$fbid WHERE userid=$userid");
                }
                else {
                    // never logged in w/ email address before
                    $logoutUrl = $facebook -> getLogoutUrl();
                    Template::SetBodyHeading("Chapman Radio", "New Account");
                    return new \Symfony\Component\HttpFoundation\Response(Template::Finalize("<div class='leftcontent'><p>Hello, $me[first_name].</p><p>You're trying to log into Chapman Radio with your Facebook account. Unfortunately, we don't have any Chapman Radio accounts associated with this Facebook account.</p><p>If you <b>already have</b> a Chapman Radio account, please <a href='$logoutUrl'>logout of Facebook</a>, then try to login again.</p><p>If you <b>don't have</b> a Chapman Radio account, you can <a href='/join'>Join Chapman Radio</a>.</div>"));
                }
            }
            Session::Login($userid);
        }

// okay, there is no facebook login to do

// process
        $loginERROR = "";
        if(isset($_POST['USER_LOGIN'])) {
            $email = ChapmanRadioRequest::Get('email');
            $password = ChapmanRadioRequest::Get('password');
            if(!$email && !$password) $loginERROR = "Please enter your email and password:";
            else if(!$email) $loginERROR = "Please enter your email address:";
            else if(!$password) $loginERROR = "Please enter your password:";
            else if(!preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}\$/", $email)) $loginERROR = "It looks like <b>".stripslashes($email)."</b> is an invalid email address. Please try again:";
            else {
                $row = DB::GetFirst("SELECT userid,fname,password FROM users WHERE email = :email", array(":email" => $email));
                if(!$row) $loginERROR = "Sorry, but that email isn't registered with Chapman Radio. Would you like to <a href='/join'>Join</a>?";
                else {
                    $correctPassword = Util::decrypt($row['password']);
                    if($password == $correctPassword ) Session::Login($row['userid']);
                    else if($correctPassword == "") $loginERROR = "Sorry, but this account is not setup with a password. You must use Facebook to login.";
                    else $loginERROR = "Sorry, that password was incorrect.";
                }
            }
        }

        if($loginERROR) $loginERROR = "<p style='color:red;'>$loginERROR</p>";

// output

// style
# Template::style(".loginContainer .gloss {width:auto;margin:10px;} .loginContainer p { font-size:13px; } .loginContainer { margin:10px auto; } .loginContainer td {width:300px;text-align:left;padding:0 30px;} .oldfashioned td {width:auto;} ");

// output the login page
        Template::SetBodyHeading("Chapman Radio", "Login");
        Template::AddBodyContent("<p style='color:#484848;padding:10px; text-align: center;'>");

        if(isset($_SESSION['redirectPageName'])) Template::AddBodyContent("Please login to view <b>{$_SESSION['redirectPageName']}</b>.<br />");

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

        return new \Symfony\Component\HttpFoundation\Response( Template::Finalize());
    }
}