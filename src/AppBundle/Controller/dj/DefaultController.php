<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:57 AM
 */

namespace AppBundle\Controller\dj;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:08 AM
 */

namespace AppBundle\Controller\dj;


use ChapmanRadio\DB;
use ChapmanRadio\Evals;
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DefaultController extends Controller
{

    /**
     * @Route("/dj", name="dj_eval")
     */
    public function indexAction(ContainerInterface $container = null)
    {



        Template::SetPageTitle("DJ Account");
        Template::SetBodyHeading("Chapman Radio", "DJ Account");
        Template::RequireLogin("DJ Account");

// organize content into a table
        $menu = array(
            array(
                "img" => "/legacy/img/decor/dj/myshows.png",
                "label" => "My Shows",
                "text" => "/legacy/img/text/myshows.png",
                "description" => "View your statistics, manage your recordings, and edit your show preferences.",
                "link" => "/dj/shows",
            ),
            array(
                "img" => "/legacy/img/decor/dj/myprofile.png",
                "label" => "My Profile",
                "text" => "/legacy/img/text/myprofile.png",
                "description" => "Update your contact information or manage your profile picture.",
                "link" => "/dj/profile",
            ),
            array(
                "img" => "/legacy/img/decor/dj/tags.png",
                "label" => "Evals",
                "text" => "/legacy/img/text/evals.png",
                "description" => "Start evaluating a fellow DJ or see what peers have to say about your show.",
                "link" => "/dj/evals",
            ),
        );

        Template::css("/legacy/css/formtable.css");

        Template::AddBodyContent("<br /><iframe src='//player.vimeo.com/video/111051355' width='942' height='450'></iframe>");

        Template::AddBodyContent("<table style='margin:10px auto;' class='formtable' cellspacing='0' cellpadding='0'>");

        foreach($menu as $key => $item) {
            $rowclass = ($key + 1) % 2 == 0 ? 'evenRow' : 'oddRow';
            extract($item);
            Template::AddBodyContent("<tr class='$rowclass' style='cursor:pointer;' onmouseover='this.rel=this.style.backgroundColor;this.style.backgroundColor=\"#E0E0E0\";' onmouseout='this.style.backgroundColor=this.rel;' onclick='window.location=\"$link\"'>
		<td><img src='$img' alt=\"" . htmlentities($label) . "\" /></td>
		<td><img src='$text' alt=\"" . htmlentities($label) . "\" /><br />
			<p style='text-align:left;'>$description</p>
			<p><a href='$link'>&raquo; $label</a></p>
		</td>
	</tr>");
        }

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize("</table>"));

    }
}