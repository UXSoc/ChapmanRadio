<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:02 PM
 */

use ChapmanRadio\DB;
use ChapmanRadio\Request;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ErrorController{

    /**
     * @Route("/staff/error", name="staff_error")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Staff");
        Template::SetBodyHeading("Site Administration", "Recent Site Errors");
        Template::RequireLogin("/staff/error","Staff Resources", "staff");

        $limit = Request::GetInteger('limit', 30);

        $errors = DB::GetAll("SELECT * FROM errors ORDER BY `timestamp` DESC LIMIT $limit");

        $cip = Request::ClientAddress();

        Template::AddBodyContent("<table class='eros -full'><thead><tr>
	<td style='width: 12%'>Timestamp</td>
	<td>IP</td>
	<td>Code</td>
	<td>Data</td>
	</tr></thead>");

        foreach($errors as $error) Template::AddBodyContent("<tr>
	<td>{$error['timestamp']}</td>
	<td ".(($error['ip'] == $cip)?"style='color:blue;'":"").">{$error['ip']}</td>
	<td>{$error['code']}</td>
	<td>{$error['data']}".($error['referer'] == "" ? "" : "<br /><br />(Referer: {$error['referer']})")."</td>
	</tr>");

        Template::AddBodyContent("</table>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}