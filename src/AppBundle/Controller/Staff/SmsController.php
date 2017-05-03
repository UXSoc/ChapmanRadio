<?php
namespace AppBundle\Controller\Staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\LiveChat;
use ChapmanRadio\Request;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SmsController extends Controller
{
    /**
     * @Route("/staff/sms", name="staff_sms")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Staff - SMS");
        Template::SetBodyHeading("Site Administration", "SMS / Livechat History");
        //Template::RequireLogin("/staff/sms","Staff Resources", "staff");
        Template::css("/legacy/css/formtable.css");

        $limit = Request::GetInteger('limit', 50);
        $result = livechat::getMostRecent($limit, isset($_GET['all']));
        Template::AddBodyContent("<table class='eros'>");

        foreach($result as $row)
            Template::AddBodyContent("<tr>
		<td>".(str_replace(",","<br />",date("g:ia,n/j/y",strtotime($row['datetime']))))."</td>
		<td>".($row['direction'] == 'out' ? "To" : "From")." <br /><b>$row[contactid]</b></td>
		<td>".htmlspecialchars($row['message'],ENT_COMPAT,"UTF-8")."</td>
		</tr>");

        Template::AddBodyContent("</table></div>");

        return Template::Finalize($this->container);
    }

}