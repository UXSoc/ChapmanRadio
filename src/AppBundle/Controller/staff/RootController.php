<?php
namespace AppBundle\Controller\staff;
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\Icecast;
use ChapmanRadio\Notify;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Template;
use ChapmanRadio\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class RootController extends Controller
{

    /**
     * @Route("/staff/root", name="staff_root")
     */
    public function indexAction(Request $request)
    {
        define('PATH', '../');

        Template::SetPageTitle("Staff - Advanced");
        Template::SetBodyHeading("Site Administration", "Advanced Settings");
        Template::RequireLogin("/staff/recordings","Staff Resources", "staff");
        Template::css("/legacy/css/formtable.css");

        $season = Season::current();
        $seasonName = Season::name($season);
        $streams = Icecast::streams();

        Template::AddBodyContent("<div style='width: 600px; margin: 0 auto;'><div style='background:red;color:white;margin:5px auto;padding:5px;width:350px;'>Do not change unless you know what you are doing</div>");

        if(isset($_POST['util_simulatelogin_submit'])) {
            if(!isset($_REQUEST['util_simulatelogin_userid'])){
                Template::AddInlineError("Missing userid variable");
            }
            else{
                $userid = $_REQUEST['util_simulatelogin_userid'];
                $user = UserModel::FromId($userid);
                if($user) {
                    Template::notify("Login", "You are now simulating login for <b>{$user->name}</b>.");
                    $cur = Session::GetCurrentUserId();
                    Notify::Mail("webmaster@chapmanradio.com", "Root Simulated Login", "User {$cur} just simulated login for {$userid}");
                    Session::login($userid);
                }
                else {
                    Template::AddInlineError("Invalid userid variable");
                }
            }
        }
        $path = $request->getRequestUri();
        Template::AddBodyContent("
<form class='table' method='post' action='$path'>
	<div class='center'>Simulate Login</div>
	<div>
		Login as User #
		<input type='text' name='util_simulatelogin_userid' value='' />. 
	</div>
	<div class='center'>
		<input type='submit' name='util_simulatelogin_submit' value='Simulate Login' />
	</div>
</form>");

        Template::AddBodyContent("</div>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}