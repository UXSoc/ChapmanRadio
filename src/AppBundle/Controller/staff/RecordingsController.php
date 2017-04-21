<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use ChapmanRadio\RecordingModel;
use ChapmanRadio\Request;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RecordingsController extends Controller
{
    /**
     * @Route("/staff/recordings", name="staff_recordings")
     */
    public function indexAction(ContainerInterface $container = null)
    {

        Template::SetPageTitle("Staff");
        Template::SetBodyHeading("Site Administration", "Recent Recordings");
        Template::RequireLogin("Staff Resources", "staff");

        $limit = Request::GetInteger('limit', 30);
        $recordings = RecordingModel::FromResults(DB::GetAll("SELECT * FROM mp3s INNER JOIN shows ON mp3s.showid = shows.showid ORDER BY recordedon DESC LIMIT $limit"));

        Template::AddBodyContent("<table class='eros' style='width: 100%; text-align: left;'><thead><tr><td>Timestamp</td><td>Show</td><td>File</td><td>Exists</td></tr></thead>");

        foreach($recordings as $recording)
            Template::AddBodyContent("<tr><td>".$recording->recordedon."</td><td>".$recording->rawdata['showname']."</td><td>".$recording->url."</td><td>".($recording->Exists() ? "Yes" : "No")."</td></tr>");

        Template::AddBodyContent("</table>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}