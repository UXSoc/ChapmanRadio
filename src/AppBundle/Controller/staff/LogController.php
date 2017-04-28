<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use ChapmanRadio\Request;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LogController extends Controller
{
    /**
     * @Route("/staff/log", name="staff_log")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Staff");
        Template::SetBodyHeading("Site Administration", "Edit Log");
        Template::RequireLogin( "/staff/log","Staff Resources", "staff");

        $limit = Request::GetInteger('limit', 30);

        $events = DB::GetAll("SELECT * FROM staff_log INNER JOIN users ON staff_log.userid = users.userid ORDER BY timestamp DESC LIMIT $limit");

        Template::AddBodyContent("<table class='eros -full'><thead><tr><td>Timestamp</td><td>Staff</td><td>Details</td></tr></thead>");
        foreach($events as $event) Template::AddBodyContent("<tr><td>{$event['timestamp']}</td><td>".$event['name']."</td><td>".self::formatLogEntry($event['details'])."</td></tr>");
        Template::AddBodyContent("</table>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }

    function formatLogEntry($desc){
        $desc = preg_replace("/attendance record #?(\d+)/", "attendance record <a href='/staff/reports/instance?id=$1' target='_blank'>#$1</a>", $desc);
        $desc = preg_replace("/shows for #?(\d+)/", "show <a href='/staff/reports/show?showid=$1' target='_blank'>#$1</a>", $desc);
        $desc = preg_replace("/users for #?(\d+)/", "user <a href='/staff/reports/user?userid=$1' target='_blank'>#$1</a>", $desc);
        $desc = preg_replace("/features for #?(\d+)/", "feature <a href='/staff/features?id=$1' target='_blank'>#$1</a>", $desc);
        return $desc;
    }
}