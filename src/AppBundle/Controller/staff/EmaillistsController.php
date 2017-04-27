<?php
namespace AppBundle\Controller\staff;

use ChapmanRadio\DB;
use ChapmanRadio\Request as ChapmanRadioRequest;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class EmaillistsController extends Controller
{
    /**
     * @Route("/staff/emaillists", name="staff_emaillists")
     */
    public function indexAction(Request $request)
    {
        define('PATH', '../');

        Template::SetPageTitle("Email Lists - Staff");
        Template::SetBodyHeading("Staff Resources", "Email Lists");
        Template::RequireLogin("/staff/emaillists","Staff Resources", "staff");

        Template::css(PATH . "css/formtable.css");

        Template::AddBodyContent("<div class='leftcontent'>");

        if (isset($_POST['NewEmailList'])) {
            $listname = ChapmanRadioRequest::Get('listname');
            $email = ChapmanRadioRequest::Get('email');

            if (!preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}\$/", $email))
                Template::AddInlineError("Thats a not valid email address");

            else {
                if (!DB::GetFirst("SELECT * FROM emaillists WHERE listname='$listname' AND email='$email'")) {
                    DB::Insert("emaillists", array("listname" => $listname, "email" => $email));
                    Template::AddInlineSuccess("The email <b>$email</b> has been added to the email list <b>$listname</b>");
                } else {
                    Template::AddInlineNotice("The email <b>$email</b> is already on the email list <b>$listname</b>");
                }
            }
        }

// load email lists data
        $result = DB::GetAll("SELECT * FROM emaillists ORDER BY listname,email");
        $lists = array();
        $list_options = "";
        foreach ($result as $row) {
            if (!isset($lists[$row['listname']])) {
                $lists[$row['listname']] = array();
                $list_options .= "<option value='" . $row['listname'] . "'>" . $row['listname'] . "</option>";
            }
            $lists[$row['listname']][$row['emaillistid']] = $row;
        }

// new email

        $path = $request->getRequestUri();
        Template::AddBodyContent("<h2>Add Email to List</h2><form method='post' action='$path'><table class='formtable' cellspacing='0'><tr class='oddRow'><td>List</td><td><select name='listname'>$list_options</select></td></tr><tr class='evenRow'><td>Email</td><td><input type='text' name='email' value='' /></td></tr><tr class='oddRow'><td style='text-align:center;' colspan='2'><input type='submit' name='NewEmailList' value=' Create ' /></td></tr></table></form>");


        Template::AddBodyContent("<h2>Email Lists</h2>");
        if (!$lists) Template::AddBodyContent("<p>There are currently no lists.</p>");
        foreach ($lists as $listname => $list) {
            $label = $listname;
            Template::AddBodyContent("<h3 style='width:570px;margin:10px auto;'>$label</h3><table cellspacing='0' class='formtable'><tr class='evenRow'><td style='text-align:center;' colspan='2'><tt>$listname</tt></td></tr>");
            $count = 0;
            foreach ($list as $emailistid => $row) {
                $rowclass = ++$count % 2 == 0 ? 'evenRow' : 'oddRow';
                Template::AddBodyContent("<tr class='$rowclass'><td>$row[email]</td></tr>");
            }
            Template::AddBodyContent("</table>");
        }

        Template::AddBodyContent("</div><br style='margin-bottom:20px' />");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }

}