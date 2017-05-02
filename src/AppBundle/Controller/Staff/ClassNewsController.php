<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/1/17
 * Time: 6:54 PM
 */

namespace AppBundle\Controller\Staff;

use ChapmanRadio\DB;
use ChapmanRadio\NewsModel;
use ChapmanRadio\Season;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Template;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class ClassNewsController extends Controller
{
    /**
     * @Route("/staff/cancelledshows", name="staff_cancelledshows")
     */
    public function indexAction(Request $request)
    {
        define('PATH', '../');
        require PATH . "inc/global.php";

        Template::SetPageTitle("Site Administration");
        Template::SetBodyHeading("Site Administration", "Class News");
        Template::RequireLogin("Staff Resources", "staff");

        Template::Bootstrap();

        Template::shadowbox();
        Template::js("/staff/js/dialog_edit.js");

        /* ********************************************************** */
        Template::AddPageHeading("Post a Class News Item");

        $form = \Sinopia\FormBuilder::Start(['id' => "cr-classnews-create"])
            ->Text(['id' => 'title', 'title' => 'Title', 'required' => true])
            ->TextArea(['id' => 'body', 'title' => 'Body', 'required' => true])
            ->DateTime(['id' => 'posted', 'title' => 'Post On', 'value' => new DateTime()])
            ->DateTime(['id' => 'expires', 'title' => 'Expires On'])
            ->SubmitButton(['text' => 'Add Item']);

        if ($form->Posted()) {
            if ($form->Valid()) {
                $fields = $form->GetFields();
                $fields['postedby'] = Session::GetCurrentUserId();
                $item = NewsModel::Create($fields);
                Template::AddAlertSuccess("Created News Item #{$item->id}");
            } else {
                Template::AddAlertError("Unable to add item: " . $form->ValidationSummary());
            }
        }

        Template::Add($form->Render());

        /* ********************************************************** */
        Template::AddPageHeading("Active Class News");
        $all = NewsModel::All();
        $items = [];
        foreach ($all as $item) $items[] = self::RenderRow($item);
        self::RenderTable($items);

        Template::Finalize($this->container);

    }

    function RenderTable($rows)
    {
        Template::AddBodyContent("<table style='width: 100%' class='table cr-table table-hover tablesorter tablesorter-bootstrap'>
		<thead><tr>
		<td style='width: 10%'>Status</td>
		<td>Title</td>
		<td>Body</td>
		<td style='width: 15%'>Posted</td>
		<td style='width: 15%'>Expires</td>
		</tr></thead>
		<tbody>" . implode($rows, "") . "</tbody></table>");
    }

    function RenderRow($item)
    {
        return "<tr class='dialog-link' id='staff-feature-edit-link-{$item->id}' data-dialog='/staff/dialog/feature_edit?feature_id={$item->id}'>
		<td>" . self::RenderStatus($item->status) . "</td>
		<td>{$item->title}</td>
		<td>{$item->body}</td>
		<td>{$item->posted}</td>
		<td>{$item->expires}</td>
		</tr>";
    }

    function RenderDatetime($time)
    {
        if ($time == '0000-00-00 00:00:00') return "N/A";
        return date("M jS, Y", strtotime($time));
    }

    function RenderStatus($status)
    {
        switch ($status) {
            case 'pending':
                return "<span style='color:#939'>$status</span>";
            case 'active':
                return "<span style='color:#393'>$status</span>";
            case 'expired':
                return "<span style='color:#A33'>$status</span>";
            case 'disabled':
                return "<span style='color:#33A'>$status</span>";
        }
        return $status;
    }
}