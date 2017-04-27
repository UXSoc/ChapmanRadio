<?php
namespace AppBundle\Controller\staff;
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:08 AM
 */


use ChapmanRadio\DB;
use ChapmanRadio\Evals;
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AliasesController extends Controller
{
    /**
     * @Route("/staff/aliases", name="staff_aliases")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Staff");
        Template::SetBodyHeading("Site Administration", "URL Aliases");
        Template::RequireLogin("/staff/aliases","Staff Resources", "staff");
        $aliases = DB::GetAll("SELECT * FROM aliases");
        Template::AddBodyContent("<table class='eros -full'><thead><tr><td>Path</td><td>Url</td><td>Expires</td></tr></thead>");
        foreach($aliases as $alias) Template::AddBodyContent("<tr><td>{$alias['path']}</td><td>{$alias['url']}</td><td>{$alias['expires']}</td></tr>");

        Template::AddBodyContent("</table>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());

    }

}