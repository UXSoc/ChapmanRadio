<?php
namespace AppBundle\Controller\Staff;

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

class ListensController extends Controller
{
    /**
     * @Route("/staff/listens", name="staff_listens")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Staff");
        Template::SetBodyHeading("Site Administration", "Listen Log");
        Template::RequireLogin("/staff/listens","Staff Resources", "staff");

        $limit = Request::GetInteger('limit', 30);

        $lsitens = DB::GetAll("SELECT * FROM listens INNER JOIN mp3s ON mp3s.mp3id = listens.recording_id INNER JOIN shows ON mp3s.showid = shows.showid ORDER BY timestamp DESC LIMIT $limit");

        Template::AddBodyContent("<table class='eros -full'><thead><tr><td>Timestamp</td><td>Show</td><td>Recording ID</td><td>Source</td><td>IP</td></tr></thead>");

        foreach($lsitens as $listen)
            Template::AddBodyContent("<tr><td>{$listen['timestamp']}</td><td>".$listen['showname']." (".$listen['showid'].")</td><td>".$listen['recording_id']."</td><td>".$listen['source']."</td><td>".inet_ntop($listen['ipaddr'])."</td></tr>");

        Template::AddBodyContent("</table>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());

    }

}