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
use ChapmanRadio\Template;
use ChapmanRadio\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginIssuesController extends Controller
{
    /**
     * @Route("/staff/login", name="staff_login")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        Template::SetPageTitle("Suspended Login Attempts");
        Template::SetBodyHeading("Chapman Radio Admin", "Suspended Login Attempts");
        Template::RequireLogin("Staff Resources", "staff");
        $season = Season::current();

        $seasonName = Season::name($season);

        Template::css("/css/formtable.css");


        $attempts = DB::GetAll("SELECT sla.type as login_type, sla.timestamp as login_time, users.* FROM suspendedloginattempts as sla INNER JOIN users ON users.userid = sla.userid WHERE season = :season ORDER BY sla.type, sla.timestamp", array(":season" => $season));

        if (empty($attempts)) {
            Template::AddBodyContent("<p style='text-align:center;padding:40px;'>There are no suspended login attempts for $seasonName</p>");
        } else {
            Template::AddBodyContent("<p>There are <b>" . count($attempts) . "</b> suspended login attempt(s).</p>");
            Template::AddBodyContent("<table class='formtable' cellspacing='0' cellpadding='0' style='margin:10px auto;text-align:left;width:600px;'><tr><td><b>Type</b></td><td><b>Time</b></td><td colspan='10'><b>User</b></td></tr>");
            $count = 0;
            foreach ($attempts as $attempt) {
                $rowclass = ++$count % 2 == 0 ? 'evenRow' : 'oddRow';
                $dj = UserModel::FromResult($attempt);
                if (!$dj) continue;
                Template::AddBodyContent("<tr class='$rowclass'><td>{$dj->rawdata['login_type']}</td><td>" . date("g:ia n/j/y T", $dj->rawdata['login_time']) . "</td>");
                $djname = ($dj->djname && $dj->name != $dj->djname) ? "{$dj->name}<br /><span class='genre'>{$dj->djname}</span>" : $dj->name;
                Template::AddBodyContent("<td><img src='{$dj->img64}' /></td><td>$djname</td><td><a href='/staff/reports/user?userid={$dj->id}' target='_blank' style='white-space:nowrap;'>" . count($dj->GetStrikes($season)) . " strike(s)</a></td><td>{$dj->email}<br />{$dj->phone}<br />{$dj->classclub}</td></tr>");
            }

            Template::AddBodyContent("</table>");

        }
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}