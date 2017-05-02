<?php

namespace AppBundle\Controller\Staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use ChapmanRadio\Editor;
use ChapmanRadio\FeatureModel;
use ChapmanRadio\Log;
use ChapmanRadio\Request;
use ChapmanRadio\Template;
use ChapmanRadio\Uploader;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class FeaturesController extends Controller
{
    /**
     * @Route("/staff/ajax/query", name="staff_dialog_feature_query")
     */
    public function staffAjaxQueryAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Query Utility");
        //Template::RequireLogin("Staff Resources", "staff");

        $id = Request::GetInteger('id');
        if(!$id) die("Missing id #");

        $table = Request::Get('table');
        if(!$table) die("Missing table request variable");

        $field = Request::Get('field');
        if(!$field) die("Missing field request variable");

        $val = Request::Get('val');
//if(!$val) die("Missing val request variable");

        switch($table) {
            case 'users': $idname = "userid"; break;
            case 'shows': $idname = "showid"; break;
            case 'features': $idname = "feature_id"; break;
            case 'giveaways': $idname = "giveawayid"; break;
            default: die("invalid table: $table");
        }

        Log::StaffEvent("Updated $field in $table for #$id to $val");

        DB::Query("UPDATE `$table` SET `$field` = :val WHERE `$idname` = :id", array(":val" => $val, ":id" => $id));

        if(DB::AffectedRows()) return new \Symfony\Component\HttpFoundation\Response("<p style='color:green;'>Saved. <b>1 Row</b> updated.</p>");
        else return new \Symfony\Component\HttpFoundation\Response("<p style='color:orange;'>Saved. <b>0 Rows</b> updated.</p>");
    }

    /**
     * @Route("/staff/dialog/feature_edit", name="staff_dialog_feature_edit")
     */
    public function staffEditAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::RequireLogin("Staff Resources", "staff");
        Template::SetPageTemplate("blank");
        Template::IncludeCss("/legacy/css/dialog.css");
        Template::IncludeJs("/legacy/staff/js/dialog_edit.js");

        $feature_id = Request::GetInteger('feature_id', NULL);
        if (!$feature_id) return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($this->container,"Missing feature_id request variable"));

        $feature = FeatureModel::FromId($feature_id);
        if (!$feature) return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($this->container,"Invalid feature_id request variable"));

// Handle image uploads where JS failed
        try {
            if (Uploader::HandleModel($feature) !== NULL) {
                Template::AddCoujuSuccess("Your image has successfully been uploaded.");
            }
        } catch (Exception $e) {
            Template::AddCoujuError($e->GetMessage());
        }

        Template::AddBodyContent("<div style='right: 10px; position: absolute;'>
<img id='edit-feature-picture' style='max-width: 500px; max-height: 500px;' src='{$feature->img}' />" .
            Uploader::RenderModel($feature, 'edit-feature-picture') . "</div>");
        Template::AddBodyContent("<h2>Feature: {$feature->title} (#{$feature->id})</h2>");
        Template::AddBodyContent("<br style='clear:both;'/>");

        $editor = new Editor($feature, 'features');
        $editor->Each(function ($field, $editor) {
            if ($field == 'id') return $editor->None();
            else if ($field == 'type') return $editor->Dropdown(array('normal' => 'normal', 'html' => 'html', 'showoftheweek' => 'showoftheweek', 'recenttracks' => 'recenttracks', 'upcomingshows' => 'upcomingshows'));
            else if ($field == 'text') return $editor->Textarea();
            else if ($field == 'active') return $editor->TrueFalse();
            else if ($field == 'size') return $editor->Dropdown(array('310' => '310x310', '626' => '310x626', '942' => '310x942'));
            else if ($field == 'revisionkey') return $editor->None();
            else return $editor->Text();
        });

        $editor->End();

        Template::AddBodyContent("<div style='padding-bottom: 70px;'></div><div id='query-result' style='position: fixed; bottom: 0; left: 0px; right: 0px; padding: 5px; height: 70px; border-top: 1px solid #666; background: white;'></div>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }

    /**
     * @Route("/staff/features", name="staff_features")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Site Administration");
        Template::SetBodyHeading("Site Administration", "Features");
        Template::RequireLogin("/staff/features", "Staff Resources", "staff");

        Template::shadowbox();
        Template::js("/legacy/staff/js/dialog_edit.js");

        if (isset($_POST['new_feature'])) {

            $userid = DB::Insert("features", array(
                "feature_size" => 310,
                "feature_text" => "",
                "feature_posted" => new \DateTime(null),
                "feature_expires" => new \DateTime(null),

            ));

//            DB::Query("INSERT INTO features (feature_size,feature_text,feature_posted) VALUES ('310',''," . new \DateTime("now").")");
            $id = DB::LastInsertId();
            Template::AddBodyContent("<script>$(document).ready(function(){ setTimeout(function(){ $('#staff-feature-edit-link-{$id}').trigger('click'); }, 1000); });</script>");
        }

        Template::AddBodyContent("<form method='POST'><input style='position: absolute; right: 0; top: 10px;' name='new_feature' type='submit' value='New Feature' /></form>");

        Template::AddCoujuDebug("<div style='text-align: left;'>Features appear on the website homepage and the iPhone app. For a feature to display, it must be <b style='color:#060'>active</b>. For a feature to be active:<br />
- The 'posted' date must be in the past. This lets you schedule future features. Inactive features because of this are <u style='color:#939'>pending</u><br />
- The 'expires' date must be in the future, or 0000-00-00. This lets you schedule limited-time features. Inactive features because of this are <u style='color:#A33'>expired</u><br />
- The 'active' field must be On. This lets you temporarily disable features, regardless of their schedule. Inactive features because of this are <u style='color:#33A'>disabled</u><br /><br />
The approximate order of features is based on the <b>priority</b> property. Higher numbers will appear at the top of the homepage, lower numbers will appear towards the bottom. If there isn't a full row, then some features won't display.<br /><br />
Some features are interactive, like the show of the week, or recent tracks. These have a special <b>type</b> and update automatically. You can rearrange these using the priority property, but you cannot change what they look like.</div>");

        $features = FeatureModel::FromResults(DB::GetAll("SELECT * FROM features ORDER BY feature_priority DESC"));

        $active = array();
        $inactive = array();

        foreach ($features as $feature) {
            if ($feature->status == 'active') $active[] = self::RenderRow($feature);
            else $inactive[] = self::RenderRow($feature);
        }

        Template::AddBodyContent("<br /><h2>Active Features</h2>");
        self::RenderTable($active);

        Template::AddBodyContent("<br /><h2>Inactive Features</h2>");
        self::RenderTable($inactive);

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($this->container));

    }

    function RenderTable($rows)
    {
        Template::AddBodyContent("<table style='width: 100%' class='eros'>
		<thead><tr>
		<td style='width: 5%'>Priority</td>
		<td style='width: 10%'>Status</td>
		<td style='width: 10%'>Type</td>
		<td>Title</td>
		<td style='width: 5%'>Size</td>
		<td style='width: 15%'>Posted</td>
		<td style='width: 15%'>Expires</td>
		</tr></thead>
		<tbody>" . implode($rows, "") . "</tbody></table>");
    }

    function RenderRow($feature)
    {
        return "<tr class='dialog-link -flutter' id='staff-feature-edit-link-{$feature->id}' data-dialog='/staff/dialog/feature_edit?feature_id={$feature->id}'>
		<td>{$feature->priority}</td>
		<td>" . self::RenderStatus($feature->status) . "</td>
		<td>{$feature->type}</td>
		<td>{$feature->title}</td>
		<td>{$feature->size}</td>
		<td>" . self::RenderDatetime($feature->posted) . "</td>
		<td>" . self::RenderDatetime($feature->expires) . "</td>
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