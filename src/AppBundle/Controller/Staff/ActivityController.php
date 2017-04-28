<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:57 AM
 */

namespace AppBundle\Controller\Staff;

use ChapmanRadio\DB;
use ChapmanRadio\Evals;
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Template;
use ChapmanRadio\UserModel;
use ChapmanRadio\Util;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ActivityController extends Controller
{
    /**
     * @Route("/staff/activity", name="staff_activity")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');
        Template::SetPageTitle("Site Activity - Admin");
        Template::SetBodyHeading("Chapman Radio Admin", "Site Activity");
        Template::RequireLogin("/staff/activity","Staff Resources", "staff");

        Template::css("/legacy/css/formtable.css");

// listeners
        $listeners = DB::GetFirst("SELECT chapmanradio,chapmanradiolowquality,datetime FROM stats ORDER BY datetime DESC LIMIT 0,1");
        Template::AddBodyContent("<h3>Listeners</h3>");
        Template::AddBodyContent("<p>Hi quality: <b>".($listeners['chapmanradio'])."</b><br />Lo quality: <b>".($listeners['chapmanradiolowquality'])."</b><br /><i>as of ".date('g:ia F jS',strtotime($listeners['datetime']) )."</i><br /></p>");

// user activity
        Template::AddBodyContent("<h3>User Activity</h3>");
        Template::AddBodyContent("<div style='height:300px;overflow:auto;'><table class='eros' cellspacing='0' cellpadding='0' style='margin:10px auto;'>");

        $result = UserModel::FromResults(DB::GetAll("SELECT * FROM users ORDER BY lastlogin DESC LIMIT 0,16"));

        $userid = Session::GetCurrentUserId();
        foreach($result as $user){
            if($user->id == $userid) continue;
            $time = Util::timeDifference(strtotime($user->lastlogin))." ago";
            Template::AddBodyContent("<tr><td><img src='".$user->img64."' alt='' /></td><td>".$user->name."</td><td>$time</td></tr>");
        }
        Template::AddBodyContent("</table></div>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }

}