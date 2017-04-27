<?php
namespace AppBundle\Controller\staff;

use ChapmanRadio\DB;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Template;
use ChapmanRadio\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DupsController extends Controller
{
    /**
     * @Route("/staff/dups", name="staff_dups")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Staff");
        Template::SetBodyHeading("Site Administration", "Duplicates");
        Template::RequireLogin("/staff/dups","Staff Resources", "staff");
        Template::Bootstrap();

        $dupshows = DB::GetAll("SELECT s1.*,s2.showid as dsid FROM shows as s1 INNER JOIN shows AS s2 ON s1.showname = s2.showname AND s1.showid < s2.showid");
        Template::Add("<table class='eros' style='width: 100%; text-align: left;'><thead><tr><td>ID</td><td>Show Name</td><td>DJs</td><td>Show Seasons</td><td>Dup</td><td>Dup DJs</td><td>Dup Seasons</td></tr></thead><tbody>");

        foreach($dupshows as $show){
            $sm = ShowModel::FromResult($show);
            $du = ShowModel::FromId($show['dsid']);
            Template::Add("<tr><td>{$sm->id}</td><td>{$sm->name}</td><td>{$sm->GetDjNamesCsv()}</td><td>{$sm->seasons_csv}</td><td>{$du->id}</td><td>{$du->GetDjNamesCsv()}</td><td>{$du->seasons_csv}</td></tr>");
        }
        Template::Add("</tbody></table><br />");

        $dupshows = DB::GetAll("SELECT u1.*,u2.userid as duid FROM users as u1 INNER JOIN users AS u2 ON u1.fname = u2.fname AND u1.lname = u2.lname AND u1.userid < u2.userid");
        Template::Add("<table class='eros' style='width: 100%; text-align: left;'><thead><tr><td>ID</td><td>User</td><td>Seasons</td><td>Dup</td><td>Dup Name</td><td>Dup Seasons</td></tr></thead><tbody>");

        foreach($dupshows as $show){
            $um = UserModel::FromResult($show);
            $du = UserModel::FromId($show['duid']);
            Template::Add("<tr><td>{$um->id}</td><td>{$um->name}</td><td>{$um->seasons_csv}</td><td>{$du->id}</td><td>{$du->name}</td><td>{$du->seasons_csv}</td></tr>");
        }
        Template::Add("</tbody></table><br />");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }

}