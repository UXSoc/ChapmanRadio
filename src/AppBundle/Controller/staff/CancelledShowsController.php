<?php
namespace AppBundle\Controller\staff;

use ChapmanRadio\DB;
use ChapmanRadio\Season;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CancelledShowsController extends Controller
{

    /**
     * @Route("/staff/cancelledshows", name="staff_cancelledshows")
     */
    public function indexAction(Request $request)
    {
        define('PATH', '../');

        Template::SetPageTitle("Cancelled Shows");
        Template::SetBodyHeading("Chapman Radio Admin", "Cancelled Shows");
        Template::RequireLogin("/staff/cancelled","Staff Resources", "staff");

        $season = Season::current();
        $seasonName = Season::name($season);

        Template::css("/legacy/css/formtable.css");


        $path = $request->getRequestUri();
        Template::AddBodyContent("<form method='get' action='$path' id='seasonpicker'><div>Cancelled shows for: <select onchange='$(\"#seasonpicker\").submit();' name='season'>" . Season::picker(2011, false, $season, true) . "<input type='submit' value='&gt;' /></div></form>");

        $shows = ShowModel::FromResults(DB::GetAll("SELECT * FROM shows WHERE status='cancelled' AND seasons LIKE '%$season%'"));
        if (empty($shows)) {
            Template::AddBodyContent("<p style='text-align:center;padding:40px;'>There are no cancelled shows for $seasonName</p>");
        } else {
            Template::AddBodyContent("<p>There are <b>" . count($shows) . "</b> cancelled show(s).</p>");
            Template::AddBodyContent("<table class='eros' style='margin:10px auto; text-align:left; width:600px;'>");
            foreach ($shows as $show) {
                Template::AddBodyContent("<tr><td><a href='" . $show->permalink . "'><img src='" . $show->img50 . "' /></a> </td><td><span style='color:#A00;float:right;'>Cancelled</span><h3><a href='" . $show->permalink . "'>" . $show->name . "</a></h3><p>" . $show->description . "</p><table class='formtableinner'>");
                foreach ($show->GetDjModels() as $dj) {
                    if (!$dj) continue;
                    $djid = $dj->id;
                    Template::AddBodyContent("<tr><td><img src='{$dj->img50}' /></td><td>{$dj->name}</td><td><a href='/staff/reports/user?userid={$dj->id}' target='_blank' style='white-space:nowrap;'>" . count($dj->GetStrikes($season)) . " strike(s)</a></td><td>" . $dj->email . "<br />" . $dj->phone . "<br />" . $dj->classclub . "</td></tr>");
                }
                Template::AddBodyContent("</table>");
                Template::AddBodyContent("</td></tr>");
            }
            Template::AddBodyContent("</table>");
        }

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}